<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 4/19/18
 * Time: 9:42 PM
 */

namespace Manager\Models;


use Manager\Config;

class Addresses extends Table {

    const ENABLED = 1;
    const NOT_ENABLED = 0;

    /**
     * Constructor
     * @param $config Config The Site object
     */
    public function __construct(Config $config) {
        parent::__construct($config, "ipaddress");
    }

    /**
     * Gets the id of the ip address in the database
     * if the ip address doesn't exist, it inserts it
     * then returns the id
     * @param string $address the ip address
     * @return int|null int if successful, null if not
     */
    public function get($ipaddress) {
        if($ipaddress == null) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        }

        $sql = <<<SQL
select id from $this->tableName
where address = ?;
SQL;

        $pdo = $this->pdo();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$ipaddress]);

        if($stmt->rowCount() === 0) {
            return $this->insert($ipaddress);
        } else {
            return $stmt->fetch(\PDO::FETCH_ASSOC)['id'];
        }
    }

    /**
     * Inserts an IP Address into the database
     * @param $ipaddress
     * @return int last inserted id
     */
    private function insert($ipaddress){
        $sql = <<<SQL
insert into $this->tableName (address)
value (?);
SQL;
        $pdo = $this->pdo();
        $stmt = $pdo->prepare($sql);

        try {
            if($stmt->execute([$ipaddress]) === false) {
                return null;
            }
        } catch(\PDOException $e) {
            return null;
        }

        return $pdo->lastInsertId();
    }

    /**
     * If the Ip address is disabled, return true
     * @param $id int the id of the ip address
     * @return true if banned or error, false otherwise.
     */
    public function isDisabled($id){
        $sql = <<<SQL
select enabled from $this->tableName where id = ?;
SQL;
        $pdo = $this->pdo();

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);


        if($stmt->rowCount() === 0) {
            return true;
        } else {
            return $stmt->fetch(\PDO::FETCH_ASSOC)['enabled'] === self::NOT_ENABLED;
        }
    }
}