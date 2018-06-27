<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 6/27/2018
 * Time: 3:20 PM
 */

namespace Manager\Models;


/**
 * Class Contact
 * @brief a class to store contacts (emergency contacts)
 * @package Models
 */
class Contact
{
    private $id;
    private $firstname;
    private $lastname;
    private $phone;
    private $email;
    private $streetaddress;
    private $city;
    private $state;
    private $zip;
    private $notes;


    public function __construct($row)
    {
        if($row !== null) {
            $this->id               = isset($row['id']) ? $row['id'] : null;
            $this->firstname        = $row['firstname'];
            $this->lastname         = $row['lastname'];
            $this->email            = isset($row['email']) ? strtolower($row['email']) : null;
            $this->phone            = isset($row['phone']) ? $row['phone'] : null;
            $this->streetaddress    = isset($row['streetaddress']) ? $row['streetaddress'] : null;
            $this->city             = isset($row['city']) ? $row['city'] : null;
            $this->state            = isset($row['state']) ? $row['state'] : null;
            $this->zip              = isset($row['zip']) ? $row['zip'] : null;
            $this->notes            = isset($row['notes']) ? $row['notes'] : null;
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param null $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return null|string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     */
    public function setEmail(string $email)
    {
        $this->email = strtolower($email);
    }

    /**
     * @return null
     */
    public function getStreetaddress()
    {
        return $this->streetaddress;
    }

    /**
     * @param null $streetaddress
     */
    public function setStreetaddress($streetaddress)
    {
        $this->streetaddress = $streetaddress;
    }

    /**
     * @return null
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param null $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return null
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param null $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return null
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param null $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return null
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param null $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    public function getAddress()
    {
        $address = $this->streetaddress . ', ' . $this->city . ', ' . $this->getState() . ' ' . $this->getZip();
        return $address;
    }


}