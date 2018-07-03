<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/3/2018
 * Time: 10:07 AM
 */

namespace Manager\Models;


class Log
{
    private $id;
    private $message;
    private $type;
    private $date;

    public function __construct($row = null)
    {
        if($row !== null)
        {
            $this->id       = $row['id'];
            $this->date     = $row['date'];
            $this->message  = $row['message'];
            $this->type     = $row['type'];
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
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }


}