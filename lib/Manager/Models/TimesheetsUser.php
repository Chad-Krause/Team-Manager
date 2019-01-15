<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 1/2/19
 * Time: 1:25 AM
 */

namespace Manager\Models;


class TimesheetsUser extends User
{
    private $hasPin; /// boolean
    private $punchedIn; /// status (punched in / punched out)

    const PUNCHED_IN = 1;
    const PUNCHED_OUT = 0;

    /**
     * @return mixed
     */
    public function getPunchedIn()
    {
        return $this->punchedIn;
    }

    /**
     * @param mixed $punchedIn
     */
    public function setPunchedIn($punchedIn): void
    {
        $this->punchedIn = $punchedIn;
    }


    /**
     * User constructor.
     */
    public function __construct($row = null)
    {
        parent::__construct($row);

        if(isset($row['hasPin'])) {
            $this->hasPin = $row['hasPin'] == 1;
        }

        if(isset($row['punchedIn'])) {
            $this->punchedIn = $row['punchedIn'] == 1;
        }
    }

    /**
     * @return mixed
     */
    public function hasPin()
    {
        return $this->hasPin;
    }

    /**
     * @param mixed $hasPin
     */
    public function setHasPin($hasPin): void
    {
        $this->hasPin = $hasPin;
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['hasPin'] = $this->hasPin();
        $array['punchedIn'] = $this->getPunchedIn() == self::PUNCHED_IN;
        return $array;
    }
}