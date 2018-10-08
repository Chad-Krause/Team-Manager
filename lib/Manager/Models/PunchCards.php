<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 10/8/18
 * Time: 12:41 AM
 */

namespace Manager\Models;

use Manager\Config;

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
        $sql = <<<SQL
insert into $this->tableName (userid, in_time, enabled, auto_logout, ipaddressid)
values 
SQL;


    }

    /**
     * @param $userid
     * @param $time
     * @param null $ipaddress
     * @return true on success
     */
    public function punchOut($userid, $time, $ipaddress = null)
    {
        $sql = <<<SQL
update $this->tableName 
set (out_time) = ? 
where userid = ? and out_time is null
SQL;


    }

    /**
     * @param $userid
     * @param $type IN or OUT
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

}