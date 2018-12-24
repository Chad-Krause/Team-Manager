<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 12/19/18
 * Time: 11:20 AM
 */

namespace Manager\Models;


use Manager\Config;
use Manager\Helpers\Server;

class Tidbits extends Table
{
    /**
     * Tidbits constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        parent::__construct($config, 'tidbit');
    }

    /**
     * Gets all the tidbits by userid
     * @param $userid
     * @return array
     */
    public function getTidbitsByUserId($userid)
    {
        $tidbitTypes = new TidbitTypes($this->config);
        $tidbitTypesName = $tidbitTypes->getTableName();
        $sql = <<<SQL
select 
  tb.userid, 
  tb.tidbittypeid, 
  tb.value, 
  tb.date_added, 
  tb.date_modified,
  tbt.name,
  tbt.description
  from $this->tableName tb
join $tidbitTypesName tbt
on tb.tidbittypeid = tbt.id
where userid = ?
SQL;

        $stmt = $this->pdo()->prepare($sql);

        $stmt->execute([$userid]);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $tidbits = [];
        foreach ($rows as $row) {
            $tidbits[] = (new Tidbit($row))->toArray();
        }

        return $tidbits;
    }

    /**
     * Gets all the tidbits by type
     * Most likely an admin function
     * @param $tidbitTypeId
     * @return array
     */
    public function getTidbitsByTidbitTypeId($tidbitTypeId)
    {
        $tidbitTypes = new TidbitTypes($this->config);
        $sql = <<<SQL
select *
from $this->tableName
where tidbittypeid = ?
order by userid
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([$tidbitTypeId]);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $tidbits = [];
        foreach ($rows as $row) {
            $tidbits[] = (new Tidbit($row))->toArray();
        }

        return $tidbits;
    }

    /**
     * Gets a single tidbit
     * @param $userid
     * @param $tidbittypeid
     * @return array|null
     */
    public function getTidbit($userid, $tidbittypeid)
    {
        $tidbittypes = new TidbitTypes($this->config);
        $tbtTable = $tidbittypes->getTableName();

        $sql = <<<SQL
select 
  tb.userid,
  tb.tidbittypeid,
  tbt.name,
  tbt.description,
  tb.value,
  tb.date_added,
  tb.date_modified
from $this->tableName tb
join $tbtTable tbt
on tb.tidbittypeid = tbt.id
where userid = ? and tidbittypeid = ?
SQL;

        $stmt = $this->pdo()->prepare($sql);

        $stmt->execute([$userid, $tidbittypeid]);

        $row = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if(count($row) !== 1) {
            return null;
        }

        return (new Tidbit($row[0]))->toArray();

    }

    /**
     * Edits a tidbit
     * @param $userid
     * @param $tidbittypeid
     * @param $value
     * @return bool
     */
    public function editTidbit($userid, $tidbittypeid, $value)
    {
        $sql = <<<SQL
update $this->tableName 
set 
  value = ?, 
  date_modified = ?
where userid = ? and tidbittypeid = ?
SQL;
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([
            $value,
            Server::getRequestDatetime(),
            $userid,
            $tidbittypeid
        ]);

        return $stmt->rowCount() == 1;
    }

    /**
     * Creates a tidbit of information
     * @param $userid
     * @param $tidbittypeid
     * @param $value
     * @return string
     */
    public function createTidbit($userid, $tidbittypeid, $value)
    {
        $sql = <<<SQL
insert into $this->tableName (userid, tidbittypeid, value, date_added, date_modified)
values
  (?, ?, ?, ?, ?)
SQL;

        $stmt = $this->pdo()->prepare($sql);

        $stmt->execute([
            $userid,
            $tidbittypeid,
            $value,
            Server::getRequestDatetime(),
            Server::getRequestDatetime()
        ]);

        return $stmt->rowCount() == 1;
    }

    /**
     * Deletes a tidbit
     * @param $userid
     * @param $tidbittypeid
     * @return boolean
     */
    public function deleteTidbit($userid, $tidbittypeid)
    {
        $sql = <<<SQL
delete from $this->tableName
where userid = ? and tidbittypeid = ?
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([$userid, $tidbittypeid]);

        return $stmt->rowCount() == 1;
    }

}