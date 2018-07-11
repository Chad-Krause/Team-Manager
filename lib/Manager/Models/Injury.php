<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/10/2018
 * Time: 12:16 PM
 */

namespace Manager\Models;


class Injury
{
    private $id;
    private $date_added;
    private $date_modified;
    private $reporterid;
    private $victimid;
    private $description;
    private $actionsTaken;

    public function __construct($row = null)
    {
        if($row !== null) {
            $this->id               = $row['id'];
            $this->date_added       = $row['date_added'];
            $this->date_modified    = $row['date_modified'];
            $this->reporterid       = $row['reporterid'];
            $this->victimid         = $row['victimid'];
            $this->description      = $row['description'];
            $this->actionsTaken     = $row['actionstaken'];
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
    public function getDateAdded()
    {
        return $this->date_added;
    }

    /**
     * @param mixed $date_added
     */
    public function setDateAdded($date_added): void
    {
        $this->date_added = $date_added;
    }

    /**
     * @return mixed
     */
    public function getDateModified()
    {
        return $this->date_modified;
    }

    /**
     * @param mixed $date_modified
     */
    public function setDateModified($date_modified): void
    {
        $this->date_modified = $date_modified;
    }

    /**
     * @return mixed
     */
    public function getReporterid()
    {
        return $this->reporterid;
    }

    /**
     * @param mixed $reporterid
     */
    public function setReporterid($reporterid): void
    {
        $this->reporterid = $reporterid;
    }

    /**
     * @return mixed
     */
    public function getVictimid()
    {
        return $this->victimid;
    }

    /**
     * @param mixed $victimid
     */
    public function setVictimid($victimid): void
    {
        $this->victimid = $victimid;
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
    public function getActionsTaken()
    {
        return $this->actionsTaken;
    }

    /**
     * @param mixed $actionsTaken
     */
    public function setActionsTaken($actionsTaken): void
    {
        $this->actionsTaken = $actionsTaken;
    }
}