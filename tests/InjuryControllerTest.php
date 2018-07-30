<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/17/2018
 * Time: 4:47 PM
 */

require_once 'DatabaseTest.php';
use Manager\Controllers\InjuryController;
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\DataSet\YamlDataSet;

class InjuryControllerTest extends DatabaseTest
{
    protected function getDataSet()
    {
        return new YamlDataSet(dirname(__FILE__) . '/Datasets/injury.yaml');
    }

    public function test__construct()
    {
        $time = time();
        $request = [];

        $controller = new InjuryController(self::$config, $time, $request);

        $this->assertInstanceOf(InjuryController::class, $controller);
    }
}
