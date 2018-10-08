<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 4/19/18
 * Time: 9:05 PM
 */

namespace TRB\Models;


class Address {

    private $id;            ///< Id of the row
    private $ipaddress;     ///< Ip address
    private $enabled;

    const ENABLED = 'Y';
    const NOT_ENABLED = 'N';

    /**
     * @return string IP Address
     */
    public function getIpaddress()
    {
        return $this->ipaddress;
    }

    /**
     * @param mixed String Ip Address
     */
    public function setIpaddress($ipaddress)
    {
        $this->ipaddress = $ipaddress;
    }

    /**
     * @return Boolean true if banned
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param Boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }        ///< Beaned status

    /**
     * Constructor
     * @param $row array Address Row from the user table in the database
     */
    public function __construct($row) {
        $this->id           = $row['id'];
        $this->ipaddress    = $row['ipaddress'];
        $this->enabled      = $row['enabled'];
    }


}