<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 6/27/2018
 * Time: 5:15 PM
 */

namespace Manager\Models;


class PunchCard
{
    const DISABLED = 0;
    const ENABLED = 1;
    const AUTO_LOGOUT_TRUE = 1;
    const AUTO_LOGOUT_FALSE = 0;

    private $id;
    private $userid;
    private $name;
    private $inTime;
    private $outTime;
    private $enabled;
    private $auto_logout;
    private $ipaddress;

    public function __construct($row)
    {
        if($row !== null)
        {
            $this->id           = $row['id'];
            $this->userid       = $row['userid'];
            $this->inTime       = isset($row['in_time']) ? $row['in_time'] : null;
            $this->outTime      = isset($row['out_time']) ? $row['in_time'] : null;
            $this->enabled      = isset($row['enabled']) ? $row['enabled'] : self::DISABLED;
            $this->auto_logout  = isset($row['auto_logout']) ? $row['auto_logout'] : self::AUTO_LOGOUT_FALSE;
            $this->ipaddress    = isset($row['ipaddress']) ? $row['ipaddress'] : null;
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getInTime()
    {
        return $this->inTime;
    }

    /**
     * @param mixed $inTime
     */
    public function setInTime($inTime)
    {
        $this->inTime = $inTime;
    }

    /**
     * @return mixed
     */
    public function getOutTime()
    {
        return $this->outTime;
    }

    /**
     * @param mixed $outTime
     */
    public function setOutTime($outTime)
    {
        $this->outTime = $outTime;
    }

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return mixed
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * @param mixed $userid
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;
    }

    /**
     * @return int
     */
    public function getAutoLogout(): int
    {
        return $this->auto_logout;
    }

    /**
     * @param int $auto_logout
     */
    public function setAutoLogout(int $auto_logout): void
    {
        $this->auto_logout = $auto_logout;
    }

    /**
     * @return null
     */
    public function getIpaddress()
    {
        return $this->ipaddress;
    }

    /**
     * @param null $ipaddress
     */
    public function setIpaddress($ipaddress): void
    {
        $this->ipaddress = $ipaddress;
    }



}

class UserHours
{
    public $userid;
    public $name;
    public $nickname;
    public $time;
    public $last_in;
    public $auto_logouts;

    public function __construct($row)
    {
        $userid             = $row['userid'];
        $name               = $row['name'];
        $nickname           = $row['nickname'];
        $time               = $row['time'];
        $last_in            = $row['last_in'];
        $auto_logouts       = $row['auto_logouts'];
    }
}