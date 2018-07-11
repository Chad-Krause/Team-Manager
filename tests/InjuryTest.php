<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/10/2018
 * Time: 2:48 PM
 */

use Manager\Models\Injury;
use PHPUnit\Framework\TestCase;

class InjuryTest extends TestCase
{

    public function test__construct()
    {
        $row = array(
            'id' => 1,
            'date_added' => '',
            'date_modified' => '',
            'victimid' => 2,
            'reporterid' => 1,
            'description' => 'Mike hurt himself cutting something',
            'actionstaken' => 'Nothing'
        );

        $injury = new Injury($row);

        $this->assertInstanceOf(Injury::class, $injury);

        $this->assertEquals(1, $injury->getId());
        $this->assertEquals(2, $injury->getVictimid());
        $this->assertEquals(1, $injury->getReporterid());
        $this->assertContains('hurt', $injury->getDescription());
        $this->assertEquals('Nothing', $injury->getActionsTaken());
    }

    public function testGettersAndSetters()
    {
        $injury = new Injury();

        $injury->setVictimid(1);
        $injury->setReporterid(2);
        $injury->setDescription('Test Injury');
        $injury->setActionsTaken('None');

        $this->assertEquals(1, $injury->getVictimid());
        $this->assertEquals(2, $injury->getReporterid());
        $this->assertContains('Injury', $injury->getDescription());
        $this->assertEquals('None', $injury->getActionsTaken());
    }
}
