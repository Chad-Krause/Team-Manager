<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 6/23/18
 * Time: 8:45 PM
 */

namespace Manager\Models;


class User
{
    private $id;                ///> Id of the user
    private $firstname;         ///> First Name
    private $lastname;          ///> Last Name
    private $email;             ///> Email address
    private $role;              ///> Role
    private $graduationyear;    ///> Graduation Year
    private $yearjoined;        ///> Year Joined
    private $birthday;          ///> Birthday

    const ADMIN = "1";
    const STUDENT = "2";
    const MENTOR = "3";

    /**
     * User constructor.
     */
    public function __construct($row)
    {
        $this->id               = $row['id'];
        $this->firstname        = $row['firstname'];
        $this->lastname         = $row['lastname'];
        $this->email            = $row['email'];
        $this->role             = $row['roleid'];
        $this->graduationyear   = $row['graduationyear'];
        $this->yearjoined       = $row['yearjoined'];
        $this->birthday         = $row['birthday'];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getGraduationyear()
    {
        return $this->graduationyear;
    }

    /**
     * @param mixed $graduationyear
     */
    public function setGraduationyear($graduationyear)
    {
        $this->graduationyear = $graduationyear;
    }

    /**
     * @return mixed
     */
    public function getYearjoined()
    {
        return $this->yearjoined;
    }

    /**
     * @param mixed $yearjoined
     */
    public function setYearjoined($yearjoined)
    {
        $this->yearjoined = $yearjoined;
    }

    /**
     * @return mixed
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param date $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }


}