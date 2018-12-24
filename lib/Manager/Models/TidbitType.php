<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 12/19/18
 * Time: 12:00 PM
 */

namespace Manager\Models;


class TidbitType
{
    private $id;
    private $name;
    private $description;
    private $type;
    private $default_value;

    const ENABLED = 'Y';
    const DISABLED = 'N';

    public function __construct($row = null)
    {
        if(!is_null($row)) {
            $this->id = isset($row['id']) ? $row['id'] : null;
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->type = $row['type'];
            $this->default_value = $row['default_value'];
        }
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
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
    public function getDefaultValue()
    {
        return $this->default_value;
    }

    /**
     * @param mixed $default_value
     */
    public function setDefaultValue($default_value): void
    {
        $this->default_value = $default_value;
    }

    public function toArray()
    {
        return [
            'id' => intval($this->id),
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'default_value' => $this->default_value
        ];
    }
}