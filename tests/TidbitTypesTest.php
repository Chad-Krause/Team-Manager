<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 12/21/18
 * Time: 10:45 PM
 */

require_once 'DatabaseTest.php';
use Manager\Models\TidbitTypes;
use PHPUnit\Framework\TestCase;
use \PHPUnit\DbUnit\DataSet\YamlDataSet;

class TidbitTypesTest extends DatabaseTest
{
    protected function getDataSet()
    {
        return new YamlDataSet(dirname(__FILE__) . '/Datasets/master.yaml');
    }

    public function test__construct()
    {
        $tidbittypes = new TidbitTypes(self::$config);
        $this->assertInstanceOf(TidbitTypes::class, $tidbittypes);
    }

    public function testGetAllTidbitTypes()
    {
        $tidbittypes = new TidbitTypes(self::$config);
        $types = $tidbittypes->getAllTidbitTypes();
        $this->assertEquals(2, count($types));
    }

    public function testGetTidbitType()
    {
        $tidbittypes = new TidbitTypes(self::$config);
        $tidbittype = $tidbittypes->getTidbitType(1);
        $this->assertContains('Shirt Size', $tidbittype);
    }

    public function testEditTidbitType()
    {
        $tidbittypes = new TidbitTypes(self::$config);
        $tidbittypes->editTidbitType(1, 'Test', 'Edit Test', 'Type Test', '');
        $tidbit = $tidbittypes->getTidbitType(1);
        $this->assertContains('Test', $tidbit['name']);
        $this->assertContains('Edit', $tidbit['description']);
    }

    public function testDeleteTidbitType()
    {
        $tidbittypes = new TidbitTypes(self::$config);
        $this->assertTrue($tidbittypes->deleteTidbitType(2));
        $this->assertEquals(1, count($tidbittypes->getAllTidbitTypes()));
    }

    public function testCreateTidbitType()
    {
        $tidbittypes = new TidbitTypes(self::$config);
        $count = count($tidbittypes->getAllTidbitTypes());
        $id = $tidbittypes->createTidbitType('New TidbitType', 'Created during testing', 'string', 'test');
        $this->assertEquals($count + 1, count($tidbittypes->getAllTidbitTypes()));
        $tidbittype = $tidbittypes->getTidbitType($id);
        $this->assertContains('New', $tidbittype['name']);
    }

}
