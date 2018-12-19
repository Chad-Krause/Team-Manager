<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 11/23/18
 * Time: 2:55 PM
 */

use Manager\Controllers\Controller;
use PHPUnit\Framework\TestCase;
use \Manager\Models\User;
use \Manager\Models\Users;
use PHPUnit\DbUnit\DataSet\YamlDataSet;

require_once 'DatabaseTest.php';

class ControllerTest extends DatabaseTest
{
    protected function getDataSet()
    {
        return new YamlDataSet(dirname(__FILE__) . '/Datasets/user.yaml');
    }

    public function testGetResponse()
    {

    }

    public function test__construct()
    {

    }

    public function testHasPermission() {
        $config = self::$config;
        $controller = new class($config, null) extends Controller {
            public function __construct(\Manager\Config $config, $user = null, array $request = [])
            {
                parent::__construct($config, $user, $request);
            }

            public function getResponse()
            {

            }

            public function getUser() {
                return $this->user;
            }
            public function setUser(User $user) {
                $this->user = $user;
            }
        };

        $users = new \Manager\Models\Users(self::$config);

        $controller->setUser($users->get(1)); // Admin
        $permissions = [User::SAME_USER, User::ADMIN, User::MENTOR];

        $this->assertTrue($controller->hasPermission($permissions));

        $controller->setUser($users->get(2)); // Mentor
        $this->assertTrue($controller->hasPermission($permissions));

        $permissions = [User::ADMIN, User::SAME_USER];
        $this->assertFalse($controller->hasPermission($permissions));
        $this->assertTrue($controller->hasPermission($permissions, 2));

        $permissions = [User::ADMIN, User::SAME_USER];
        $this->assertFalse($controller->hasPermission($permissions));

        $controller->setUser($users->get(3)); // Student
        $this->assertFalse($controller->hasPermission($permissions));
        $this->assertFalse($controller->hasPermission($permissions, 2));
        $this->assertTrue($controller->hasPermission($permissions, 3));
    }
}
