<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 6/26/2018
 * Time: 5:39 PM
 */

require_once 'DatabaseTest.php';
use Manager\Models\Users;
use Manager\Models\User;
use PHPUnit\DbUnit\DataSet\YamlDataSet;

class UsersTest extends DatabaseTest
{
    protected function getDataSet()
    {
        return new YamlDataSet(dirname(__FILE__) . '/Datasets/master.yaml');
    }

    protected function tearDown(): void
    {
        $users = new Users(self::$config);
        $table = $users->getTableName();
        $sql = <<<SQL
delete from $table where id > 1
SQL;

        $stmt = self::$config->pdo()->prepare($sql);
        //$stmt->execute();
    }

    public function testLogin()
    {
        $users = new Users(self::$config);

        $chad = $users->login('chad@chadkrause.com', 'testpassword');

        $this->assertInstanceOf(User::class, $chad);
        $this->assertEquals(1, $chad->getId());
        $this->assertEquals('chad@chadkrause.com', $chad->getEmail());
        $this->assertEquals(2020, $chad->getGraduationyear());
    }

    public function testFailedLogin()
    {
        $users = new Users(self::$config);

        $wrong = $users->login('bademail', 'badpassword');

        $this->assertNull($wrong);

        $wrong = $users->login('chad@chadkrause.com', 'wrong');

        $this->assertNull($wrong);
    }

    public function testExists()
    {
        $users = new Users(self::$config);

        $exists = $users->exists('chad@chadkrause.com'); // does exist
        $notexists = $users->exists('dne');

        $this->assertTrue($exists);
        $this->assertFalse($notexists);

    }

    public function testAdd()
    {
        $users = new Users(self::$config);
        $mailer = new MockEmail();

        $row = [
            'firstname' => 'test',
            'lastname' => 'user',
            'email' => 'test@user.com',
            'roleid' => 1
        ];

        $user = new User($row);

        $users->add($user, $mailer);

        $table = $users->getTableName();
        $sql = <<<SQL
select * from $table where email=?
SQL;

        $stmt = $users->pdo()->prepare($sql);
        $stmt->execute([$row['email']]);

        $this->assertEquals(1, $stmt->rowCount());
        $this->assertEquals($row['email'], $mailer->to);

        $duplicateUser = new User($row);

        $this->expectException(\Exception::class);

        $users->add($duplicateUser, $mailer);
    }

    public function testGet()
    {
        $users = new Users(self::$config);

        $user = $users->get(1); // 1 is always Chad

        $this->assertNotNull($user);
        $this->assertEquals('Chad', $user->getFirstname());
        $this->assertEquals(1, $user->getId());

        $user = $users->get(10000); // user does not exist

        $this->assertNull($user);
    }

    public function testVerifyPin()
    {
        $users = new Users(self::$config);

        $this->assertTrue($users->verifyPin(1, '7226'));
        $this->assertTrue($users->verifyPin(1, 7226));
        $this->assertFalse($users->verifyPin(1, 9999));
    }

    public function testGetAllUsers()
    {
        $users = new Users(self::$config);
        $usersTable = $users->getTableName();

        $sql = <<<SQL
select count(*) as 'count' from $usersTable
where enabled = ? and confirmed = ?
SQL;
        $stmt = $users->pdo()->prepare($sql);
        $stmt->execute([
            User::ENABLED,
            User::CONFIRMED
        ]);
        $count = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        $all = $users->getAllUsers();

        $this->assertEquals(count($all), $count);
        $this->assertInstanceOf(User::class, $all[0]);
    }

    public function testPinVerify() {
        $users = new Users(self::$config);
        $user = $users->get(1); // pin should be 7226

        $this->assertTrue($users->verifyPin(1, 7226));


    }
}


/**
 * Class MockEmail
 * @brief makes sure the testing doesn't send real mail, while providing an easy way to check for testing
 */
class MockEmail extends Manager\Helpers\Email
{
    public $to;
    public $subject;
    public $message;
    public $headers;

    public function mail($to, $subject, $message, $headers)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->message = $message;
        $this->headers = $headers;
    }
}
