<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/2/2018
 * Time: 11:28 AM
 */

use Manager\Controllers\Controller;
use Manager\Models\User;

use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{

    public function test__construct()
    {
        $config = new Manager\Config();
        $localize = require 'localize.inc.php';
        if(is_callable($localize)) {
            $localize($config);
        }

        //$controller = new Controller($config, []);

        //$this->assertInstanceOf(Controller::class, $controller);
    }

}
