<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/2/2018
 * Time: 4:43 PM
 */

use Manager\Helpers\Log;

class LogTest extends DatabaseTest
{
    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/Datasets/log.xml');
    }



}
