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
    private $nickname;          ///> Nickname
    private $email;             ///> Email address
    private $role;              ///> Role
    private $graduationyear;    ///> Graduation Year
    private $yearjoined;        ///> Year Joined
    private $birthday;          ///> Birthday
    private $confirmed;         ///>

    const ADMIN = 1;
    const STUDENT = 2;
    const MENTOR = 3;
    const GUARDIAN = 4;

    const CONFIRMED = 'Y';
    const UNCONFIRMED = 'N';
    const ENABLED = 'Y';
    const DISABLED = 'N';

    /**
     * User constructor.
     */
    public function __construct($row = null)
    {
        if($row !== null) {
            $this->id               = isset($row['id']) ? $row['id'] : null;
            $this->firstname        = $row['firstname'];
            $this->lastname         = $row['lastname'];
            $this->nickname         = isset($row['nickname']) ? $row['nickname'] : null;
            $this->email            = strtolower($row['email']);
            $this->role             = $row['roleid'];
            $this->graduationyear   = isset($row['graduationyear']) ? $row['graduationyear'] : null;
            $this->yearjoined       = isset($row['yearjoined']) ? $row['yearjoined'] : null;
            $this->birthday         = isset($row['birthday']) ? $row['birthday'] : null;

            if(isset($row['confirmed'])) {
                $this->confirmed = $row['confirmed'] == self::CONFIRMED;
            }
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
        if($this->isBirthday()) {
            return '🎁 ' . $this->firstname;
        } else {
            return $this->firstname;
        }
        //return $this->firstname;
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
    public function getNickname()
    {
        if($this->isBirthday()) {
            return '🎁 ' . $this->nickname;
        } else {
            return $this->nickname;
        }
    }

    /**
     * @param mixed $nickname
     */
    public function setNickname($nickname): void
    {
        $this->nickname = $nickname;
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
            'nickname' => $this->getNickname(),
            'email' => $this->getEmail(),
            'graduationyear' => $this->getGraduationyear(),
            'yearjoined' => $this->getYearjoined(),
            'birthday' => $this->getBirthday()
        );
    }

    /**
     * @return null
     */
    public function isConfirmed() : bool
    {
        return $this->confirmed;
    }

    /**
     * @param null $confirmed
     */
    public function setConfirmed($confirmed): void
    {
        $this->confirmed = $confirmed;
    }

    /**
     * @returns true if it's this user's birthday
     */
    private function isBirthday()
    {
        $date = date('m-d');
        $bd = substr($this->birthday,5);
        return $date == $bd;
    }


}