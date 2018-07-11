<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/2/2018
 * Time: 4:40 PM
 */

namespace Manager\Helpers;


use Manager\Config;
use Manager\Models\Log;
use Manager\Models\Table;
use Manager\Models\User;

class Logger extends Table
{
    const TOKEN_MODIFIED = 1;
    const INVALID_LOGIN = 2;
    const PERMISSION_DENIED = 3;
    const USER_LOGGED_IN = 4;
    const USER_LOGGED_OUT = 5;
    const INTERNAL_EXCEPTION = 6;
    const API_EXCEPTION = 7;


    public function __construct(Config $config)
    {
        parent::__construct($config, "log");
    }

    /**
     * Returns the last log
     * @return Log
     */
    public function getLastLog()
    {
        $sql = <<<SQL
select * from $this->tableName
order by id desc
limit 1
SQL;
        $stmt = $this->pdo()->prepare($sql);

        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return new Log($stmt->fetch(\PDO::FETCH_ASSOC));
        } else {
            return null;
        }
    }

    /**
     * Logs a message with a name and id
     * (DEPRECIATED - LOGS ARE NOW LOGS OF EXCEPTIONS)
     * @param $type
     * @param User $user
     * @param null $message
     * @param \DateTime|null $dateTime
     */
    public function logWithUser($type, User $user, $message = null, \DateTime $dateTime = null)
    {
        $log = new Log();

        $userString = sprintf(
            'User %d: %s %s',
            $user->getId(),
            $user->getFirstname(),
            $user->getLastname()
        );

        switch ($type) {
            case self::TOKEN_MODIFIED:
                $log->setMessage('Invalid Token. ' . $userString);
                break;
            case self::INVALID_LOGIN:
                $log->setMessage('Invalid Login. ' . $userString);
                break;
            case self::PERMISSION_DENIED:
                $log->setMessage('Permission Denied. ' . $userString);
                break;
            default:
             $log->setMessage('Unknown Log. ' . $userString);
        }

        $log->setType($type);

        if($dateTime == null) {
            $dateTime = date("Y-m-d H:i:s");
        }

        $log->setDate($dateTime);

        $this->_log($log);
    }

    private function _log(Log $log)
    {
        $sql = <<<SQL
insert into $this->tableName (message, type, date)
values(?, ?, ?)
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([
            $log->getMessage(),
            $log->getType(),
            $log->getDate()
        ]);
    }

    public function logException(\Exception $exception)
    {
        $row = array(

        );
        $log = new Log($row);
    }
}