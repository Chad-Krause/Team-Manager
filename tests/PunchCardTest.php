<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 6/27/2018
 * Time: 6:08 PM
 */

//require __DIR__ . '../vendor/autoload.php';

use Manager\Models\PunchCard;
use PHPUnit\Framework\TestCase;

class PunchCardTest extends TestCase
{

    public function test__construct()
    {
        $datetime = new DateTime();

        $row = [
            'id' => 1,
            'userid' => 1,
            'name' => 'Chad Krause',
            'in_time' => $datetime,
            'out_time' => $datetime,
            'enabled' => 1
        ];

        $punchcard = new PunchCard($row);

        $this->assertInstanceOf(PunchCard::class, $punchcard);
        $this->assertNotNull($punchcard);

        $this->assertEquals(1, $punchcard->getId());
        $this->assertEquals(1, $punchcard->getUserid());
        $this->assertEquals('Chad Krause', $punchcard->getName());
        $this->assertEquals($datetime, $punchcard->getInTime());
        $this->assertEquals($datetime, $punchcard->getOutTime());
        $this->assertEquals(true, $punchcard->isEnabled());
    }

    public function testGettersAndSetters()
    {
        $punchcard = new PunchCard(null);
        $datetime = new DateTime();

        $punchcard->setUserid(1);
        $punchcard->setEnabled(true);
        $punchcard->setInTime($datetime);
        $punchcard->setOutTime($datetime);

        $this->assertEquals(1, $punchcard->getUserid());
        $this->assertEquals(true, $punchcard->isEnabled());
        $this->assertEquals($datetime, $punchcard->getInTime());
        $this->assertEquals($datetime, $punchcard->getOutTime());
    }
}
