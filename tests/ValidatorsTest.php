<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 6/29/18
 * Time: 7:48 PM
 */

require_once 'DatabaseTest.php';
use Manager\Models\Validators;
use PHPUnit\Framework\TestCase;

class ValidatorsTest extends DatabaseTest
{
    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/Datasets/validator.xml');
    }

    public function tearDown(): void
    {
        $validators = new Validators(self::$config);
        $table = $validators->getTableName();
        $sql = <<<SQL
delete from $table where 1
SQL;

        $stmt = self::$config->pdo()->prepare($sql);
        $stmt->execute();
    }

    public function test__construct()
    {
        $validators = new Validators(self::$config);
        $this->assertInstanceOf(Validators::class, $validators);
    }

    public function testGet()
    {
        $validators = new Validators(self::$config);

        // Single Validator works
        $token = $validators->newValidator(45);
        $this->assertEquals(45, $validators->get($token));
    }

    public function testNewValidator()
    {
        $validators = new Validators(self::$config);

        // Single Validator works
        $token = $validators->newValidator(45);
        $this->assertEquals(45, $validators->get($token));

        // Add another validator to same userid
        $token2 = $validators->newValidator(45);

        // Able to use either validator
        $this->assertEquals(45, $validators->get($token2));
        $this->assertEquals(45, $validators->get($token));
    }

    public function testRemove()
    {
        $validators = new Validators(self::$config);

        $token1 = $validators->newValidator(7226);
        $token2 = $validators->newValidator(7226);

        $validators->remove(7226);

        $this->assertNull($validators->get($token1));
        $this->assertNull($validators->get($token2));
    }
}
