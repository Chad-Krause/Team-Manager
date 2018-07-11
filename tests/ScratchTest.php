<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/9/2018
 * Time: 3:18 PM
 */

use PHPUnit\Framework\TestCase;

class ScratchpadTest extends TestCase
{

    public function testSQLOutput()
    {
        $config = new \Manager\Config();
        $localize = require dirname(__DIR__) . '/lib/localize.inc.php';
        if(is_callable($localize)) {
            $localize($config);
        }

        $users = new \Manager\Models\Users($config);

        $sql = <<<SQL
update user set confirmed = 0 where id = 1
SQL;
        $stmt = $users->pdo()->prepare($sql);
        $stmt->execute();

        $chad = $users->get(1);

        $this->assertNotNull($chad->isConfirmed());

    }
}
