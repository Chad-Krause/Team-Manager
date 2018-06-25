<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 6/24/18
 * Time: 7:24 PM
 */

namespace Manager;


class Validators extends Table
{
    /**
     * Constructor
     * @param $config Config The Site Configuration object
     */
    public function __construct(Config $config)
    {
        parent::__construct($config, "validator");
    }

    /**
     * Create a new validator and add it to the table.
     * @param $userid int User this validator is for.
     * @return string The new validator.
     */
    public function newValidator($userid) {
        $validator = $this->createValidator();

        // Write to the table
        $sql = <<<SQL
insert into $this->tableName (userid, validator, date)
values (?, ?, now());
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute(array($userid, $validator));

        return $validator;
    }

    /**
     * Generate a random validator string of characters
     * @param $len int Length to generate, default is 32
     * @returns string Validator string
     */
    public function createValidator($len = 32) {
        $bytes = openssl_random_pseudo_bytes($len / 2);
        return bin2hex($bytes);
    }

    /**
     * Determine if a validator is valid. If it is,
     * return the user ID for that validator.
     * @param $validator string Validator to look up
     * @return int|null User ID or null if not found.
     */
    public function get($validator) {

        $sql = <<<SQL
select userid from $this->tableName
where validator = ?
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute(array($validator));

        if($stmt->rowCount() === 0) {
            return null;
        }

        return $stmt->fetch(\PDO::FETCH_ASSOC)['userid'];

    }

    /**
     * Remove any validators for this user ID.
     * @param $userid int The USER ID we are clearing validators for.
     */
    public function remove($userid) {
        $sql = <<<SQL
delete from $this->tableName
where userid = ?;
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute(array($userid));
    }


}
