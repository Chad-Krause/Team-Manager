<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 10/8/18
 * Time: 12:41 AM
 */

namespace Manager\Models;

use Manager\Config;
use Manager\Helpers\APIException;
use Manager\Helpers\JsonAPI;
use Manager\Models\Addresses;
use Manager\Models\UserHours;

class PunchCards extends Table
{
    const IN = 'in';
    const OUT = 'out';
    const AUTO_LOGOUT = 1;

    public function __construct(Config $config)
    {
        parent::__construct($config, "punchcard");
    }

    /**
     * @param $userid
     * @param $time
     * @param null $ipaddress
     * @return true on success
     */
    public function punchIn($userid, $time, $ipaddress = null)
    {
        if(!$this->isEligible($userid, self::IN)){
            return false;
        }

        $addresses = new Addresses($this->config);
        $ipaddressid = $addresses->get($ipaddress);

        if(is_null($ipaddressid)) {
            $ipaddressid = 1;
        }

        $sql = <<<SQL
insert into $this->tableName (userid, in_time, out_time, enabled, auto_logout, ipaddressid)
values (?, ?, NULL, 1, 0, ?)
SQL;

        $stmt = $this->pdo()->prepare($sql);

        try {
            $success = $stmt->execute([$userid, $time, $ipaddressid]);
        } catch (\Exception $e) {
            print_r($e);
            return false;
        }

            return $success;
    }

    /**
     * @param $userid
     * @param $time
     * @param null $ipaddress
     * @return true on success
     */
    public function punchOut($userid, $time)
    {
        if(!$this->isEligible($userid, self::OUT)){
            return false;
        }

        $sql = <<<SQL
update $this->tableName 
set out_time = ? 
where userid = ? and out_time is null
SQL;
        try {
            $stmt = $this->pdo()->prepare($sql);
            $success = $stmt->execute([$time, $userid]);
        } catch (\Exception $e) {
            print_r($e);
            return false;
        }
        return $stmt->rowCount() == 1 && $success;
    }

    /**
     * @param $userid
     * @param $type string
     * @return true if eligible for punch (in/out)
     */
    private function isEligible($userid, $type)
    {
        $sql = <<<SQL
select count(*) as 'count' from $this->tableName
where userid = ? and out_time is null
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([$userid]);
        $count = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        if($type == self::IN) {
            return $count == 0;
        } else {
            return $count == 1;
        }
    }

    /**
     * @param $start_time
     * @return array(UserHours)
     */
    public function getTotalHours($start_time = '2018-1-1 0:00:00')
    {
        $users = new Users($this->config);
        $usersTable = $users->getTableName();

        $sql = <<<SQL
select userid, CONCAT(firstname, ' ', lastname) as 'name', SUM(in_time - out_time) as 'time', SUM(auto_logout) as 'auto_logouts', MAX(in_time) as 'last_in'
from $this->tableName p
left join $usersTable u
on p.userid = u.id
where NOT ISNULL(out_time) AND in_time > ?
group by userid
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([$start_time]);

        $userhours = [];

        foreach($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $userhours[] = new UserHours($row);
        }

        return $userhours;
    }

    /**
     * Gets the user hours in HH:MM:SS since the $date
     * @param $userid
     * @param null $date
     * @return array
     */
    public function getUserHours($userid, $date = null) {
        $sql = <<<SQL
SELECT userid, SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(out_time, in_time)))) as 'totalTimeLogged'
FROM $this->tableName
WHERE userid = ? AND (in_time > ? OR ? IS NULL) AND out_time IS NOT NULL
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([
            $userid,
            $date,
            $date
        ]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC)[0];
    }

    public function punchAllUsersOut($time)
    {
        $sql = <<<SQL
update $this->tableName 
set out_time = ?, auto_logout = ?
where out_time is null
SQL;

        $stmt = $this->pdo()->prepare($sql);
        try {
            $success = $stmt->execute([$time, self::AUTO_LOGOUT]);
        } catch (\Exception $e) {
            print_r($e);
            return false;
        }
        return $stmt->rowCount();
    }

}