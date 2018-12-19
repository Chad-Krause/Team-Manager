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
        $salt = $row['salt'];

        // Ensure it is correct
        if($hash !== hash("sha256", $password . $salt)) {
            return null;
        }

        $user = new User($row);
        $this->setProfilePictureUrl($user);
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

        // Create a validator and add to the validator table
        $validators = new Validators($this->config);
        $validator = $validators->newValidator($id);

        // Send email with the validator in it
        $link = "https://team.chadkrause.com"  . $this->config->getRoot() .
            '/password-validate.php?v=' . $validator;

        $from = $this->config->getEmail();
        $name = $user->getFirstname();

        $subject = "Confirm your email";
        $message = <<<MSG
<html>
<p>Greetings, $name,</p>

<p>Welcome to Felis. In order to complete your registration,
please verify your email address by visiting the following link:</p>

<p><a href="$link">$link</a></p>
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
            $this->setProfilePictureUrl($user);
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
        $hash = $row['pin'];
        $salt = $row['pin_salt'];

        //print_r('real: ' . $hash);
        //print_r('new: '. hash("sha256", $pin . $salt));

        // Ensure it is correct
        return $hash == hash("sha256", $pin . $salt);
    }

    /**
     * Returns an array of all users
     * @return array(User)
     */
    public function getAllUsers()
    {
        $sql = <<<SQL
select * from $this->tableName
where enabled = ? and confirmed = ?
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([
            User::ENABLED,
            User::CONFIRMED
        ]);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $users = [];
        foreach($rows as $row) {
            $users[] = new User($row);
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

        $salt = Users::randomSalt();
        $hash = $this->hash_pw($password, $salt);

        $sql = <<<SQL
insert into $this->tableName (
      firstname, 
      lastname, 
      email, 
      roleid, 
      `password`, 
      salt, 
      date_added, 
      date_modified, 
      enabled,
      confirmed
  )
values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
SQL;

        $stmt = $this->pdo()->prepare($sql);

        try{
            $stmt->execute([
                $firstname,
                $lastname,
                $email,
                User::STUDENT,
                $hash,
                $salt,
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
set `password` = ?, salt = ?
where id = ?
SQL;

        $salt = Users::randomSalt();
        $hash = $this->hash_pw($password, $salt);

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([
            $hash,
            $salt,
            $userid
        ]);

        return $stmt->rowCount() == 1;
    }

    /**
     * Generate a random salt string of characters for password salting
     * @param $len int Length to generate, default is 16
     * @return string Salt string
     */
    public static function randomSalt($len = 16) {
        $bytes = openssl_random_pseudo_bytes($len / 2);
        return bin2hex($bytes);
    }

    /**
     * @brief Encrypt a password using salt
     */
    private function hash_pw($password, $salt) {
        return hash("sha256", $password . $salt);
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

    private function setProfilePictureUrl(User $user)
    {
        if(!is_null($user->getProfilePictureId())) {
            $user->setProfilePictureUrl($this->config->getServerDomain() . '/api/image/' . $user->getProfilePictureId());
        }
    }

}