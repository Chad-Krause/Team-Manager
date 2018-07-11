<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/11/2018
 * Time: 6:38 PM
 */

use Manager\Models\Injuries;
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\DataSet\YamlDataSet;

require_once 'DatabaseTest.php';

class InjuriesTest extends DatabaseTest
{
    public function getDataSet()
    {
        return new YamlDataSet(dirname(__FILE__) . '/Datasets/injury.yaml');
    }

    public function test__construct()
    {
        $injuries = new Injuries(self::$config);

        $this->assertInstanceOf(Injuries::class, $injuries);
    }
}
