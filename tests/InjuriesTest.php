<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/11/2018
 * Time: 6:38 PM
 */

use Manager\Models\Injuries;
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\DataSet\YamlDataSet;
use Manager\Models\Injury;
use Manager\Models\User;
use Manager\Models\Users;

require_once 'DatabaseTest.php';

class InjuriesTest extends DatabaseTest
{
    public function getDataSet()
    {
        return new YamlDataSet(dirname(__FILE__) . '/Datasets/injury.yaml');
    }

    public function tearDown(): void
    {
        $sql = <<<SQL
delete from ztest_injury
SQL;
        $table = new \Manager\Models\Table(self::$config, 'injury');
        $stmt = $table->pdo()->prepare($sql);
        //$stmt->execute();
    }

    public function test__construct()
    {
        $injuries = new Injuries(self::$config);

        $this->assertInstanceOf(Injuries::class, $injuries);
    }

    public function testGet()
    {
        $injuries = new Injuries(self::$config);

        $injury1 = $injuries->get(1);

        $this->assertInstanceOf(Injury::class, $injury1);
        $this->assertContains('dirty job', $injury1->getDescription());

        $injury2 = $injuries->get(-1);

        $this->assertNull($injury2);
    }

    public function testAdd()
    {
        $injuries = new Injuries(self::$config);
        $date = date('Y-m-d');

        $row = array(
            'date_added'        => $date,
            'date_occurred'     => $date,
            'reporterid'        => 1, // chad
            'victimid'          => 1,
            'description'       => 'Chad got hurt because he wasn\'t wearing safety glasses',
            'actionstaken'      => 'Was guilted him into wearing safety glasses'
        );

        $id1 = $injuries->add(new Injury($row));
        $this->assertNotNull($id1);

        $injury1 = $injuries->get($id1);
        $this->assertInstanceOf(Injury::class, $injury1);
        $this->assertContains('got hurt because', $injury1->getDescription());
        $this->assertEquals($date, substr($injury1->getDateAdded(), 0,10));
    }

    public function testUpdate()
    {
        $injuries = new Injuries(self::$config);
        $date = date('Y-m-d');

        $row = array(
            'date_added'        => $date,
            'date_occurred'     => $date,
            'reporterid'        => 1, // chad
            'victimid'          => 1,
            'description'       => 'Chad got hurt because he wasn\'t wearing safety glasses',
            'actionstaken'      => 'Was guilted him into wearing safety glasses'
        );

        $id1 = $injuries->add(new Injury($row));

        $row['reporterid'] = 2;
        $row['id'] = $id1;

        $result = $injuries->update(new Injury($row));

        $this->assertTrue($result);
        $injury1 = $injuries->get($id1);

        $this->assertEquals(2, $injury1->getReporterid());
    }

    public function testDelete()
    {
        $injuries = new Injuries(self::$config);
        $date = date('Y-m-d');
        $row = array(
            'date_added'        => $date,
            'date_occurred'     => $date,
            'reporterid'        => 1, // chad
            'victimid'          => 1,
            'description'       => 'Chad got hurt because he wasn\'t wearing safety glasses',
            'actionstaken'      => 'Was guilted him into wearing safety glasses'
        );

        $ids = [];

        // Add 5 injuries to the log
        for($i = 0; $i < 5; $i++) {
            $ids[] = $injuries->add(new Injury($row));
        }

        // Assert all the injuries made it to the database
        foreach($ids as $id) {
            $this->assertNotNull($injuries->get($id));
        }

        // Delete all of those logs, it will iterate through an array
        $injuries->delete($ids);

        // Assert those logs no longer exist
        foreach($ids as $id) {
            $this->assertNull($injuries->get($id));
        }

        // Do the same thing, but make sure passing an integer in works
        $id = $injuries->add(new Injury($row));
        $this->assertNotNull($injuries->get($id));
        $injuries->delete($id);
        $this->assertNull($injuries->get($id));
    }

    /**
     * Gets all injuries that are associated with the person
     * Admin/Mentor gets all, Students get if victim or reporter
     * Future: Emergency contacts get to see the victim's
     */
    public function testGetAllAssociated()
    {
        $this->setUp();
        $users = new Users(self::$config);
        $admin = $users->get(1);
        $student = $users->get(3);
        $injuries = new Injuries(self::$config);

        $reports = $injuries->getAllAssociated($admin);

        $this->assertNotNull($reports);
        $this->assertEquals(5, count($reports));

        // There are 3 where this student is involved
        $reports = $injuries->getAllAssociated($student);

        $this->assertNotNull($reports);
        $this->assertEquals(3, count($reports));
    }


}
