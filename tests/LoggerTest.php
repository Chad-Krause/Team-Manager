<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/2/2018
 * Time: 4:43 PM
 */

use Manager\Helpers\Logger;
use Manager\Models\Users;

require_once 'DatabaseTest.php';

class LoggerTest extends DatabaseTest
{
    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/Datasets/log.xml');
    }

    protected function setUp(): void
    {
        // For some reason

        $sql = <<<SQL
        select * from ztest_user
SQL;

        //$table = new \Manager\Models\Table(self::$config);
        //$stmt = $table->pdo()->prepare($sql);
        //$stmt->execute();

    }

    protected function tearDown(): void
    {
        $log = new Logger(self::$config);
        $table = $log->getTableName();
        $sql = <<<SQL
delete from $table where 1
SQL;

        $stmt = self::$config->pdo()->prepare($sql);
        //$stmt->execute();
    }

    public function test__construct()
    {
        $logger = new Logger(self::$config);

        $this->assertInstanceOf(Logger::class, $logger);
    }

    public function testLogData()
    {
        $logger = new Logger(self::$config);
        $users = new Users(self::$config);

        $chad = $users->get(1);

        $logger->logWithUser(Logger::TOKEN_MODIFIED, $chad);

        $chadlog = $logger->getLastLog();
        $this->assertNotNull($chadlog);
        $this->assertContains('Chad', $chadlog->getMessage());
    }

}
