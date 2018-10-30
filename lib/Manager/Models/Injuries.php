<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/10/2018
 * Time: 3:11 PM
 */

namespace Manager\Models;


use Manager\Config;
use Manager\Models\Injury;

class Injuries extends Table
{
    public function __construct(Config $config)
    {
        parent::__construct($config, "injury");
    }

    /**
     * Returns an injury object that matches the Id given
     * @param int $id
     * @return Injury|null the injury requested
     */
    public function get(int $id)
    {
        $sql = <<<SQL
select * from $this->tableName where id = ? and enabled = ?
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([$id, Injury::ENABLED]);

        if($stmt->rowCount() !== 1) {
            return null;
        }

        return new Injury($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * Adds an injury to the database
     * @param Injury $injury
     * @return int the id of the object added
     */
    public function add(Injury $injury)
    {
        $sql = <<<SQL
insert into $this->tableName (date_added, 
                    date_occurred, 
                    reporterid, 
                    victimid, 
                    description, 
                    actionstaken,
                    enabled)
value (?, ?, ?, ?, ?, ?, ?)
SQL;

        $stmt = $this->pdo()->prepare($sql);

        try {
            $stmt->execute([
                $injury->getDateAdded(),
                $injury->getDateOccurred(),
                $injury->getReporterid(),
                $injury->getVictimid(),
                $injury->getDescription(),
                $injury->getActionsTaken(),
                Injury::ENABLED
            ]);
        } catch (\Exception $e) {
            return null;
        }

        if($stmt->rowCount() !== 1) {
            return null;
        }

        return $this->pdo()->lastInsertId();
    }

    /**
     * Soft Deletes (disables) injury records
     * @param array|int $id
     * @return true on success
     */
    public function delete($id)
    {
        $sql = <<<SQL
update $this->tableName set enabled = 0 where id = ?
SQL;

        if(!is_array($id)) {
            $id = [$id];
        }

        foreach($id as $i) {
            $stmt = $this->pdo()->prepare($sql);
            $stmt->execute([$i]);
        }

        return $stmt->rowCount() < 1;
    }

    /**
     * @param Injury $injury
     * @return bool
     */
    public function update(Injury $injury)
    {
        $sql = <<<SQL
update $this->tableName set 
  date_added = ?,
  date_occurred = ?,
  victimid = ?,
  reporterid = ?,
  description = ?,
  actionstaken = ?
where id = ?
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([
            $injury->getDateAdded(),
            $injury->getDateOccurred(),
            $injury->getVictimid(),
            $injury->getReporterid(),
            $injury->getDescription(),
            $injury->getActionsTaken(),
            $injury->getId()
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Gets all reports in which the user is related to
     * @param User $user The user the reports are associated with
     * @return array|null null if none are associated
     */
    public function getAllAssociated(User $user)
    {
        $role = $user->getRole();
        $id = $user->getId();

        if($role == User::ADMIN || $role == User::MENTOR) {
            $sql = <<<SQL
select * from $this->tableName 
where enabled = 1
SQL;
            $stmt = $this->pdo()->prepare($sql);
            $stmt->execute();
        } else {
            $sql = <<<SQL
select * from $this->tableName 
where enabled = 1 and victimid = ? or reporterid = ?
SQL;
            $stmt = $this->pdo()->prepare($sql);
            $stmt->execute([
                $id,
                $id
            ]);
        }

        if($stmt->rowCount() < 1) {
            return null;
        }

        $reports = [];
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach($rows as $row) {
            $reports[] = new Injury($row);
        }

        return $reports;
    }
}