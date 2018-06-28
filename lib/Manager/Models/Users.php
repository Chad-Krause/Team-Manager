<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 6/23/18
 * Time: 8:57 PM
 */

namespace Manager\Models;
use Manager\Config;
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

        return new User($row);
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
INSERT INTO $this->tableName (firstname, lastname, email, roleid, enabled, date_added, date_modified, graduationyear, yearjoined, birthday)
values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
SQL;

        $statement = $this->pdo()->prepare($sql);

        try{
            $statement->execute(array(
                $user->getFirstname(),
                $user->getLastname(),
                strtolower($user->getEmail()),
                $user->getRole(),
                true,
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
}