<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 6/23/18
 * Time: 8:57 PM
 */

namespace Manager\Models;
use Manager\Config;


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
     * @returns User object if successful, null otherwise.
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
     * @param Email $mailer An Email object to use
     * @return null on success or error message if failure
     */
    public function add(User $user/*, mail $mailer*/) {
        // Ensure we have no duplicate email address
        if($this->exists($user->getEmail())) {
            throw new \Exception('User already exists');
        }

        // Add a record to the user table
        $sql = <<<SQL
INSERT INTO $this->tableName (firstname, lastname, email, role, date_added, date_modified, graduationyear, yearjoined, birthday)
values(?, ?, ?, ?, ?, ?, ?)
SQL;

        $statement = $this->pdo()->prepare($sql);
        $statement->execute(array(
            $user->getFirstname(),
            $user->getLastname(),
            $user->getEmail(),
            $user->getRole(),
            date("Y-m-d H:i:s"),
            date("Y-m-d H:i:s"),
            $user->getGraduationyear(),
            $user->getYearjoined(),
            $user->getBirthday()));

        $id = $this->pdo()->lastInsertId();

        // Create a validator and add to the validator table
        $validators = new Validators($this->config);
        $validator = $validators->newValidator($id);

        // Send email with the validator in it
        $link = "http://webdev.cse.msu.edu"  . $this->site->getRoot() .
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
        //$mailer->mail($user->getEmail(), $subject, $message, $headers);
    }
}