<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 6/27/2018
 * Time: 1:51 PM
 */

//require '../vendor/autoload.php';

//use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCase;

abstract class DatabaseTest extends TestCase
{
    protected static $config;

    public static function setUpBeforeClass()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUpBeforeClass();

        self::$config = new Manager\Config();
        $localize = require 'localize.inc.php';
        if(is_callable($localize)) {
            $localize(self::$config);
        }
    }

    /**
     * @return \PHPUnit\DbUnit\Database\Connection
     */
    protected function getConnection()
    {
        return $this->createDefaultDBConnection(self::$config->pdo(), 'msushrim_team');
    }

}