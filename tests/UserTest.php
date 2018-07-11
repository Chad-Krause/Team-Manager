<?php

use PHPUnit\Framework\TestCase;
use Manager\Models\User;



class UserTest extends TestCase
{
    public function test__construct() {
        $row = array(
            'id' => 12,
            'firstname' => 'Chad',
            'lastname' => 'Krause',
            'email' => 'chad@chadkrause.com',
            'roleid' => 1
        );

        $user = new User($row);

        $this->assertEquals(12, $user->getId());
        $this->assertEquals('Chad', $user->getFirstname());
        $this->assertEquals('Krause', $user->getLastname());
        $this->assertEquals('chad@chadkrause.com', $user->getEmail());
        $this->assertEquals(1, $user->getRole());
    }

    public function testGettersAndSetters()
    {
        $user = new User();

        $user->setFirstname("Chad");
        $user->setLastname("Krause");
        $user->setEmail("chad@chadkrause.com");
        $user->setRole(1);
        $user->setBirthday("1996-01-27");
        $user->setYearjoined(2017);
        $user->setGraduationyear(2020);
        $user->setConfirmed(true);

        $this->assertEquals("Chad", $user->getFirstname());
        $this->assertEquals("Krause", $user->getLastname());
        $this->assertEquals("chad@chadkrause.com", $user->getEmail());
        $this->assertEquals(1, $user->getRole());
        $this->assertEquals("1996-01-27", $user->getBirthday());
        $this->assertEquals(2017, $user->getYearjoined());
        $this->assertEquals(2020,$user->getGraduationyear());
        $this->assertTrue($user->isConfirmed());
    }

    // Tiny easter egg to put a * in the name on their birthday
    public function testBirthday()
    {
        $row = array(
            'id' => 7226,
            'firstname' => 'Waverly',
            'lastname' => 'Robotics',
            'email' => 'dev@waverlyrobotics.org',
            'roleid' => 1,
            'birthday' => date('Y-m-d')
        );

        $user = new User($row);
        $this->assertContains('🎁', $user->getFirstname());

        $row = array(
            'id' => 7226,
            'firstname' => 'Waverly',
            'lastname' => 'Robotics',
            'email' => 'dev@waverlyrobotics.org',
            'roleid' => 1,
            'birthday' => '0000-00-00'
        );

        $user = new User($row);
        $this->assertNotContains('🎁', $user->getFirstname());
    }
}