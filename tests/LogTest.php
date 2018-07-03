<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/3/2018
 * Time: 10:18 AM
 */

use Manager\Models\Log;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{

    public function test__construct()
    {
        $log = new Log(['id'=>1, 'message'=>'It works!', 'date' => '', 'type'=>'1']);
        $this->assertInstanceOf(Log::class, $log);
        $this->assertContains('works', $log->getMessage());
        $this->assertEquals(1, $log->getId());
        $this->assertEquals(1, $log->getType());
    }

    public function testGettersAndSetters()
    {
        $log = new Log();
        $date = new DateTime();

        $log->setDate($date);
        $log->setMessage('It works!');
        $log->setType(1);

        $this->assertContains('works', $log->getMessage());
        $this->assertEquals($date, $log->getDate());
        $this->assertEquals(1, $log->getType());
    }
}
