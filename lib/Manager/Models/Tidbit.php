<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 12/19/18
 * Time: 10:27 AM
 */

namespace Manager\Models;


class Tidbit
{
    private $userid;
    private $tidbitTypeId;
    private $name;
    private $description;
    private $value;
    private $date_added;
    private $date_modified;

    public function __construct($row = null)
    {
        $this->userid = $row['userid'];
        $this->tidbitTypeId = $row['tidbittypeid'];
        $this->value = $row['value'];
        $this->date_added = isset($row['date_added']) ? $row['date_added'] : null;
        $this->date_modified = isset($row['date_modified']) ? $row['date_modified'] : null;
        $this->name = isset($row['name']) ? $row['name'] : null;
        $this->description = isset($row['description']) ? $row['description'] : null;
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
    public function setUserid($userid): void
    {
        $this->userid = $userid;
    }

    /**
     * @return mixed
     */
    public function getTidbitTypeId()
    {
        return $this->tidbitTypeId;
    }

    /**
     * @param mixed $tidbitTypeId
     */
    public function setTidbitTypeId($tidbitTypeId): void
    {
        $this->tidbitTypeId = $tidbitTypeId;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return null
     */
    public function getDateAdded()
    {
        return $this->date_added;
    }

    /**
     * @param null $date_added
     */
    public function setDateAdded($date_added): void
    {
        $this->date_added = $date_added;
    }

    /**
     * @return null
     */
    public function getDateModified()
    {
        return $this->date_modified;
    }

    /**
     * @param null $date_modified
     */
    public function setDateModified($date_modified): void
    {
        $this->date_modified = $date_modified;
    }

    public function toArray()
    {
        return [
            'userid' => intval($this->userid),
            'tidbittypeid' => intval($this->tidbitTypeId),
            'name' => $this->name,
            'description' => $this->description,
            'value' => $this->value,
            'date_added' => $this->date_added,
            'date_modified' => $this->date_modified
        ];
    }


}