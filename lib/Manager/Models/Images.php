<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 10/29/18
 * Time: 2:00 PM
 */

namespace Manager\Models;
use Manager\Config;


class Images extends Table
{
    public function __construct(Config $config)
    {
        parent::__construct($config, "image");
    }

    /**
     * Write data to a file based on a file pointer.
     * @param int $userId
     * @param string $name
     * @param string $data
     * @param string $type
     * @param int $time
     * @return int ID for the new entry or false if fail
     */
    public function writeFile($userId,
                              $name, $file, $type, $permission, $time) {
        $pdo = $this->pdo();

        $sql = <<<SQL
insert into $this->tablename(userid, name, data, type, created, modified, permission)
values(?, ?, ?, ?, ?, ?, ?)
SQL;

        $fp = fopen($file, 'rb');
        if($fp === false) {
            return false;
        }

        $dateStr = $this->timeStr($time);

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $userId);
        $stmt->bindParam(2, $name);
        $stmt->bindParam(3, $fp, \PDO::PARAM_LOB);
        $stmt->bindParam(4, $type);
        $stmt->bindParam(5, $dateStr);
        $stmt->bindParam(6, $dateStr);
        $stmt->bindParam(7, $permission);

        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        try {
            if(!$stmt->execute()) {
                return false;
            }
        } catch(\PDOException $e) {
            return false;
        }

        return $pdo->lastInsertId();
    }
}