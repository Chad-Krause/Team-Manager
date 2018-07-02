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
        $controller = new Controller();

        $this->assertInstanceOf(Controller::class, $controller);
    }

    public function testPrivileges()
    {
        $controller = new Controller([User::ADMIN, User::MENTOR]);

        $admin = new User(null);
        $student = new User(null);
        $mentor = new User(null);


        $admin->setRole(User::ADMIN);
        $student->setRole(User::STUDENT);
        $mentor->setRole(User::MENTOR);

        $this->assertTrue($controller->hasPermission($admin));
        $this->assertTrue($controller->hasPermission($mentor));
        $this->assertFalse($controller->hasPermission($student));
        $this->assertFalse($controller->hasPermission());

        $controller = new Controller();

        $this->assertTrue($controller->hasPermission($admin));
        $this->assertTrue($controller->hasPermission($mentor));
        $this->assertTrue($controller->hasPermission($student));
        $this->assertTrue($controller->hasPermission());
    }

}
