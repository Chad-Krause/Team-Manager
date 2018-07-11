<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/9/2018
 * Time: 3:18 PM
 */

require_once 'DatabaseTest.php';
use Manager\Models\User;
use Manager\Models\Users;
use PHPUnit\DbUnit\DataSet\YamlDataSet;

class ScratchpadTest extends DatabaseTest
{
    public function getDataSet()
    {
        return new YamlDataSet(dirname(__FILE__) . '/Datasets/user.yaml');
    }

    public function testSQLOutput()
    {
        $config = new \Manager\Config();
        $localize = require dirname(__DIR__) . '/lib/localize.inc.php';
        if(is_callable($localize)) {
            $localize($config);
        }

        $users = new \Manager\Models\Users($config);

        $sql = <<<SQL
update user set confirmed = 0 where id = 1
SQL;
        $stmt = $users->pdo()->prepare($sql);
        $stmt->execute();

        $chad = $users->get(1);

        $this->assertNotNull($chad->isConfirmed());

    }

    /*
    public function testAddingUserToDB()
    {
        $row = array(
            'id' => 7226,
            'firstname' => 'Waverly',
            'lastname' => 'Robotics',
            'email' => 'dev@waverlyrobotics.org',
            'roleid' => 1,
            'birthday' => '0000-00-00'
        );

        $user = new User($row);
        $users = new Users(self::$config);

        $users->add($user, new EmailMock());

        print_r($users->get(7226));
        $this->assertNotNull($users->get(7226));
    }
    */

}


class EmailMock extends Manager\Helpers\Email {
    public function mail($to, $subject, $message, $headers)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->message = $message;
        $this->headers = $headers;
    }

    public $to;
    public $subject;
    public $message;
    public $headers;
}