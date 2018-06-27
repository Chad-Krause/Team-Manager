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
    private $id;
    private $userid;
    private $name;
    private $inTime;
    private $outTime;
    private $enabled;

    public function __construct($row)
    {
        if($row !== null)
        {
            $this->id           = $row['id'];
            $this->userid       = $row['userid'];
            $this->name         = $row['name'];
            $this->inTime       = $row['in_time'];
            $this->outTime      = $row['out_time'];
            $this->enabled      = $row['enabled'];
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


}