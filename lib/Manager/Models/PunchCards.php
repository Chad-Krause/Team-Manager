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

    public function __construct(Config $config)
    {
        parent::__construct($config, "PunchCard");
    }

    /**
     * @param $userid
     * @param $time
     * @param null $ipaddress
     * @return true on success
     */
    public function punchIn($userid, $time, $ipaddress = null)
    {
        if(!$this->isEligible($userid, self::OUT)){
            return false;
        }

        $addresses = new Addresses($this->config);
        $ipaddressid = $addresses->get($ipaddress);

        $json = new JsonAPI();

        $sql = <<<SQL
insert into $this->tableName (userid, in_time, enabled, auto_logout, ipaddressid)
values (?, ?, 1, 0, ?)
SQL;
        try {
            $stmt = $this->pdo()->prepare($sql);
            $stmt->execute([$userid, $time, $ipaddressid]);
        } catch (\Exception $e) {
            return false;
        }
            return true;
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
set (out_time) = ? 
where userid = ? and out_time is null
SQL;
        try {
            $stmt = $this->pdo()->prepare($sql);
            $stmt->execute([$userid, $time]);
        } catch (\Exception $e) {
            return false;
        }
        return $stmt->rowCount() == 1;
    }

    /**
     * @param $userid
     * @param $type string
     * @return true if eligible for punch (in/out)
     */
    private function isEligible($userid, $type)
    {
        $sql = <<<SQL
select count(*) from $this->tableName
where userid = ? and out_time is null
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([$userid]);
        $count = $stmt->fetch(\PDO::FETCH_ASSOC)[0];

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
    public function getTotalHours($start_time)
    {
        $users = new Users($this->config);
        $usersTable = $users->getTableName();

        $sql = <<<SQL
select userid, CONCAT(firstname, ' ', lastname) as 'name', SUM(in_time - out_time) as 'time', SUM(auto_logout) as 'auto_logouts', MAX(in_time) as 'last_in'
from $this->tableName p
left join $usersTable u
on p.userid = u.id
where NOT ISNULL(out_time)
group by userid
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute();

        $userhours = [];

        foreach($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $userhours[] = new UserHours($row);
        }

        return $userhours;
    }

}

class UserHours
{
    public $userid;
    public $name;
    public $time;
    public $last_in;
    public $auto_logouts;

    public function __construct($row)
    {
        $userid             = $row['userid'];
        $name               = $row['name'];
        $time               = $row['time'];
        $last_in            = $row['last_in'];
        $auto_logouts       = $row['auto_logouts'];
    }
}