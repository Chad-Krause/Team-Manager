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
    public function __construct($row = null)
    {
        if($row !== null) {
            $this->id               = isset($row['id']) ? $row['id'] : null;
            $this->firstname        = $row['firstname'];
            $this->lastname         = $row['lastname'];
            $this->email            = strtolower($row['email']);
            $this->role             = $row['roleid'];
            $this->graduationyear   = isset($row['graduationyear']) ? $row['graduationyear'] : null;
            $this->yearjoined       = isset($row['yearjoined']) ? $row['yearjoined'] : null;
            $this->birthday         = isset($row['birthday']) ? $row['birthday'] : null;
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
        $this->email = strtolower($email);
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
     * @param string $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'firstname' => $this->getFirstname(),
            'lastname' => $this->getLastname(),
            'email' => $this->getEmail(),
            'graduationyear' => $this->getGraduationyear(),
            'yearjoined' => $this->getYearjoined(),
            'birthday' => $this->getBirthday()
        );
    }
}