<?php

require __DIR__ . "/../vendor/autoload.php";
use PHPUnit\Framework\TestCase;
use Manager\Models\User;



class UserTest extends TestCase
{
    public function test_construct() {
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
}