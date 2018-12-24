<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 12/19/18
 * Time: 12:07 PM
 */

namespace Manager\Models;


use Manager\Config;

class TidbitTypes extends Table
{
    public function __construct(Config $config)
    {
        parent::__construct($config, 'tidbit_type');
    }

    public function getAllTidbitTypes()
    {
        $sql = <<<SQL
select * from $this->tableName
where enabled = ?
SQL;

        $stmt = $this->pdo()->prepare($sql);

        $stmt->execute([TidbitType::ENABLED]);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $tidbittypes = [];

        foreach ($rows as $row) {
            $tidbittypes[] = (new TidbitType($row))->toArray();
        }

        return $tidbittypes;
    }

    public function createTidbitType($name, $description, $type, $default_value)
    {
        $sql = <<<SQL
insert into $this->tableName (name, description, type, default_value, enabled)
values (?,?,?,?,?)
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([
            $name,
            $description,
            $type,
            $default_value,
            TidbitType::ENABLED
        ]);

        return $this->pdo()->lastInsertId();
    }

    public function editTidbitType($id, $name, $description, $type, $default_value)
    {
        $sql = <<<SQL
update $this->tableName 
set 
  name = ?, 
  description = ?, 
  type = ?, 
  default_value = ?
where id = ?
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([
            $name,
            $description,
            $type,
            $default_value,
            $id
        ]);

        return $stmt->rowCount() == 1;
    }

    public function deleteTidbitType($id)
    {
        $sql = <<<SQL
update $this->tableName
set
  enabled = ?
where id = ?
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([TidbitType::DISABLED, $id]);
        return $stmt->rowCount() == 1;
    }

    public function getTidbitType($id)
    {
        $sql = <<<SQL
select * from $this->tableName
where id = ?
SQL;

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([$id]);
        $tbt = $stmt->fetchAll(\PDO::FETCH_ASSOC)[0];

        return (new TidbitType($tbt))->toArray();
    }
}
