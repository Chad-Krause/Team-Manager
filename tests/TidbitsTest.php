<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 12/21/18
 * Time: 12:36 PM
 */

use Manager\Models\Tidbits;
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\DataSet\YamlDataSet;
require_once 'DatabaseTest.php';

class TidbitsTest extends DatabaseTest
{

    public function test__construct()
    {
        $tidbits = new Tidbits(self::$config);
        $this->assertInstanceOf(Tidbits::class, $tidbits);
    }

    protected function getDataSet()
    {
        return new YamlDataSet(dirname(__FILE__) . '/Datasets/master.yaml');
    }

    public function testGetTidbitsByUserId()
    {
        $tidbits = new Tidbits(self::$config);
        $list = $tidbits->getTidbitsByUserId(1);
        $this->assertEquals(2, count($list));
        $this->assertEquals('L', $list[0]['value']);
    }

    public function testGetTidbitsByTidbitTypeId()
    {
        $tidbits = new Tidbits(self::$config);
        $list = $tidbits->getTidbitsByTidbitTypeId(1);
        $this->assertEquals(2, count($list));
    }

    public function testGetTidbit()
    {
        $tidbits = new Tidbits(self::$config);
        $tidbit = $tidbits->getTidbit(1, 1);
        $this->assertEquals('L', $tidbit['value']);
        $this->assertContains('Size', $tidbit['name']);
    }

    public function testEditTidbit()
    {
        $tidbits = new Tidbits(self::$config);
        $tidbits->editTidbit(1, 1, 'M');
        $tidbit = $tidbits->getTidbit(1,1);
        $this->assertEquals('M', $tidbit['value']);
    }

    public function testDeleteTidbit()
    {
        $tidbits = new Tidbits(self::$config);
        $tidbits->deleteTidbit(1,1);
        $this->assertNull($tidbits->getTidbit(1,1));
    }

    public function testCreateTidbit()
    {
        $tidbits = new Tidbits(self::$config);
        $tidbits->deleteTidbit(1,1);
        $this->assertNull($tidbits->getTidbit(1,1));
        $tidbits->createTidbit(1, 1, 'L');
        $this->assertNotNull($tidbits->getTidbit(1,1));
        $this->assertEquals('L', $tidbits->getTidbit(1,1)['value']);
    }
}
