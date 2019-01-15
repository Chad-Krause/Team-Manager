<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 6/23/18
 * Time: 8:57 PM
 */

namespace Manager\Models;
use Manager\Config;
use Manager\Helpers\APIException;
use Manager\Helpers\Email;
use Manager\Helpers\Server;


class Users extends Table
{
    /**
     * Users constructor.
     * @param Config $config site configuration
     */
    public function __construct(Config $config)
    {
        parent::__construct($config, "user");
    }

    /**
     * Test for a valid login.
     * @param $email string User email
     * @param $password string Password credential
     * @return User object if successful, null otherwise.
     */
    public function login($email, $password) {

        $sql =<<<SQL
SELECT * from $this->tableName
where email=?
SQL;

        $pdo = $this->pdo();
        $statement = $pdo->prepare($sql);
        $statement->execute(array($email));


        if($statement->rowCount() === 0) {
            return null;
        }

        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        // Get the encrypted password and salt from the record
        $hash = $row['password'];

        // Ensure it is correct
        if(!password_verify($password, $hash)) {
            return null;
        }

        $user = new User($row);
        return $user;
    }

    /**
     * Create a new user.
     * @param User $user The new user data
     * @param  Email $mailer An Email object to use
     * @throws \Exception when user exists
     * @return null on success or error message if failure
     */
    public function add(User $user, Email $mailer) {
        // Ensure we have no duplicate email address
        if($this->exists($user->getEmail())) {
            throw new \Exception('User already exists');
        }

        // Add a record to the user table
        $sql = <<<SQL
INSERT INTO $this->tableName (firstname, lastname, nickname, email, roleid, enabled, confirmed, date_added, date_modified, graduationyear, yearjoined, birthday)
values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
SQL;

        $statement = $this->pdo()->prepare($sql);

        try{
            $statement->execute(array(
                $user->getFirstname(),
                $user->getLastname(),
                $user->getNickname(),
                strtolower($user->getEmail()),
                $user->getRole(),
                true,
                false,
                date("Y-m-d H:i:s"),
                date("Y-m-d H:i:s"),
                $user->getGraduationyear(),
                $user->getYearjoined(),
                $user->getBirthday()));
        } catch(\PDOException $e) {
            throw new \Exception('Duplicate user');
        }


        $id = $this->pdo()->lastInsertId();

        $from = $this->config->getEmail();
        $name = $user->getFirstname();

        $subject = "Welcome to Waverly Robotics";
        $message = <<<MSG
<html>
<p>Greetings, $name,</p>

<p>Welcome to Waverly Robotics. In order to complete your registration, a mentor will have
to confirm your account. You should get a email notification when your account gets confirmed.</p>
</html>
MSG;
        $headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso=8859-1\r\nFrom: $from\r\n";
        $mailer->mail($user->getEmail(), $subject, $message, $headers);

        return null;
    }

    /**
     * @param string $email email to check whether user exists or not
     * @return bool true if email exists
     */
    public function exists(string $email){
        $sql =<<<SQL
SELECT * from $this->tableName
where email=?
SQL;

        $email = strtolower($email);

        $pdo = $this->pdo();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        return $stmt->rowCount() !== 0;
    }

    /**
     * Returns a user object that matches the id
     * @param int the id of the user
     * @return User
     */
    public function get($id)
    {
        $sql = <<<SQL
select * from $this->tableName where id = ? and enabled = ?
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([$id, User::ENABLED]);

        if($stmt->rowCount() !== 1) {
            return null;
        } else {
            $user = new User($stmt->fetch(\PDO::FETCH_ASSOC));
            return $user;
        }
    }

    public function getFromEmail($email) {
        $sql = <<<SQL
select * from $this->tableName
where email = ?
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([$email]);

        if($stmt->rowCount() !== 1) {
            return null;
        } else {
            return new User($stmt->fetch(\PDO::FETCH_ASSOC));
        }

    }

    /**
     * Verifies that the pin is correct
     * @param $userid
     * @param $pin
     * @return bool
     */
    public function verifyPin($userid, $pin)
    {
        $sql =<<<SQL
select * from $this->tableName
where id=?
SQL;

        $pdo = $this->pdo();
        $statement = $pdo->prepare($sql);
        $statement->execute([$userid]);


        if($statement->rowCount() === 0) {
            return false;
        }

        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        // Get the encrypted password and salt from the record
        $userPin = $row['pin'];

        if(strlen($userPin) == 0) {
            throw new APIException(APIException::PIN_NOT_SET_MSG, APIException::PIN_NOT_SET);
        }

        // Ensure it is correct
        return password_verify($pin, $userPin);
    }

    /**
     * Set's a user's pin
     * @param $userid
     * @param $pin
     */
    public function setPin($userid, $pin) {
        $sql = <<<SQL
update $this->tableName set pin = ?
where id = ?
SQL;

        $stmt = $this->pdo()->prepare($sql);

        $hash = password_hash($pin, PASSWORD_BCRYPT);

        $stmt->execute([$hash, $userid]);

        if($stmt->rowCount() > 1) {
            throw new \Exception('More than 1 record updated!');
        }
    }

    /**
     * Returns an array of all users
     * @return User[]
     */
    public function getAllUsers()
    {
        $sql = <<<SQL
select * from $this->tableName
where enabled = ?
order by lastname
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([
            User::ENABLED
        ]);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $users = [];
        foreach($rows as $row) {
            $user = new User($row);
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Creates an unconfirmed user
     *
     * @param $email
     * @param $password
     * @param $firstname
     * @param $lastname
     * @param $date
     * @return int userid
     */
    public function createUser($email, $password, $firstname, $lastname, \DateTime $date) {
        // Ensure we have no duplicate email address
        if($this->exists($email)) {
            throw new APIException(
                APIException::USER_ALREADY_EXISTS_MSG,
                APIException::USER_ALREADY_EXISTS
            );
        }
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $sql = <<<SQL
insert into $this->tableName (
      firstname, 
      lastname, 
      email, 
      roleid, 
      `password`,
      date_added, 
      date_modified, 
      enabled,
      confirmed
  )
values (?, ?, ?, ?, ?, ?, ?, ?, ?)
SQL;

        $stmt = $this->pdo()->prepare($sql);

        try{
            $stmt->execute([
                $firstname,
                $lastname,
                $email,
                User::STUDENT,
                $hash,
                $date->format(DATE_ISO8601),
                $date->format(DATE_ISO8601),
                User::ENABLED,
                User::UNCONFIRMED
            ]);

        } catch(\Exception $e) {
            throw new APIException($e->getMessage(), $e->getCode());
        }

        return $this->pdo()->lastInsertId();
    }

    /**
     * Creates a validator token and email for resetting passwords
     * @param $userid
     * @param Email $mailer
     * @return null
     */
    public function createResetPasswordValidator($email, Email $mailer) {
        $user = $this->getFromEmail($email);

        if($user == null) {
            throw new APIException(
                APIException::EMAIL_NOT_FOUND_MSG,
                APIException::EMAIL_NOT_FOUND
            );
        }

        // Create a validator and add to the validator table
        $validators = new Validators($this->config);
        $validator = $validators->newValidator($user->getId());

        $from = $this->config->getEmail();
        $name = $user->getNickname()!= null ? $user->getNickname() : $user->getFirstname();

        $link = $this->config->getDomain() . '/reset-password?v=' . $validator;

        $subject = "Reset Password";
        $message = <<<MSG
<html>
<p>Hello $name,</p>

<p>Here is your password reset link: <a href="$link">$link</a> </p>

<p>If you did not request a password reset, please ignore this email.</p>

</html>
MSG;

        $headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso=8859-1\r\nFrom: $from\r\n";
        $mailer->mail($user->getEmail(), $subject, $message, $headers);

        return null;
    }

    /**
     * Sets a new password given a userid
     * @param $userid
     * @param $password
     * @return bool
     */
    private function setPassword($userid, $password) {
        $sql = <<<SQL
update $this->tableName 
set `password` = ?
where id = ?
SQL;
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([
            $hash,
            $userid
        ]);

        return $stmt->rowCount() == 1;
    }

    /**
     * Given a validator and a password, reset the user's password
     * @param $validator
     * @param $password
     * @return null
     */
    public function resetPasswordWithValidator($validator, $password) {
        $validators = new Validators($this->config);
        $userid = $validators->getUserIdFromValidator($validator);

        if($userid == null) {
            throw new APIException(
                APIException::VALIDATOR_NOT_FOUND_MSG,
                APIException::VALIDATOR_NOT_FOUND
            );
        }

        $this->setPassword($userid, $password);
        $validators->remove($userid);
        return null;
    }

    public function updateUser(User $user, $time = null) {
        $sql = <<<SQL
update $this->tableName
set 
  firstname = ?,
  lastname = ?,
  nickname = ?,
  email = ?,
  roleid = ?,
  date_modified = ?,
  graduationyear = ?,
  yearjoined = ?,
  birthday = ?,
  profileimageid = ?
where id = ?
SQL;

        $stmt = $this->pdo()->prepare($sql);

        if($time === null) {
            $time = Server::getRequestDatetime();
        }

        $stmt->execute([
            $user->getFirstname(),
            $user->getLastname(),
            $user->getNickname(),
            $user->getEmail(),
            $user->getRole(),
            $time,
            $user->getGraduationyear(),
            $user->getYearjoined(),
            $user->getBirthday(),
            $user->getProfilePictureId(),
            $user->getId()
        ]);

        if($stmt->rowCount() > 1) {
            throw new \Exception('UPDATED MORE THAN ONE USER');
        }

        return $stmt->rowCount() == 1;
    }

    /**
     * Confirms a user
     * @param $userid
     * @return bool true if successful
     */
    public function confirmUser($userid)
    {
        $sql = <<<SQL
update $this->tableName
set
  confirmed = ?
where
  id = ?
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([
            User::CONFIRMED,
            $userid
        ]);

        $success =  $stmt->rowCount() == 1;

        if($success) {
            $link = $this->config->getDomain();

            $from = $this->config->getEmail();
            $user = $this->get($userid);
            $name = $user->getFirstname();

            $subject = "Waverly Robotics: Account Confirmed";
            $message = <<<MSG
<html>
<p>Hello $name,</p>

<p>Welcome to Waverly Robotics, Team Error 404! Your account has just been confirmed by an admin.</p>
<p>Please log in and fill out some additional information on your account, like your profile picture and PIN. Without
your pin, you will not be able to log time.</p>

<a href="$link">Click here to log in</a>
</html>
MSG;
            $mailer = new Email();
            $headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso=8859-1\r\nFrom: $from\r\n";
            $mailer->mail($user->getEmail(), $subject, $message, $headers);
        }

        return $success;
    }

    /**
     * Disables a user
     * @param $userid
     * @return bool
     */
    public function disableUser($userid)
    {
        $sql = <<<SQL
update $this->tableName
set
  enabled = ?
where
  id = ?
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([
            User::DISABLED,
            $userid
        ]);

        return $stmt->rowCount() == 1;
    }

    /**
     * Returns an array of all users
     * @return TimesheetsUser[]
     */
    public function getAllUsersForTimesheets()
    {
        $punchcards = new PunchCards($this->config);
        $pctable = $punchcards->getTableName();

        $sql = <<<SQL
select 
    *, 
    not isnull(pin) `hasPin`, 
    (
        select isnull(out_time) 
        from $pctable p 
        where p.userid = u.id 
        order by id desc 
        limit 1
    ) `punchedIn`
from $this->tableName u
where enabled = ? and confirmed = ?
order by hasPin desc, lastname
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([
            TimesheetsUser::ENABLED,
            TimesheetsUser::CONFIRMED
        ]);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $tsusers = [];
        foreach($rows as $row) {
            $user = new TimesheetsUser($row);
            $tsusers[] = $user;
        }

        return $tsusers;
    }
}