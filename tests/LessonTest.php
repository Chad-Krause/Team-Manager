<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 6/27/2018
 * Time: 4:00 PM
 */

//require __DIR__ .  '../vendor/autoload.php';

use Manager\Models\Lesson;
use PHPUnit\Framework\TestCase;

class LessonTest extends TestCase
{

    public function test__construct()
    {
        $date = new DateTime();

        $row = [
            'id' => 1,
            'name' => 'CNC Basics',
            'description' => 'Learn the basics of using a CNC Mill and Lathe',
            'enabled' => true,
            'date_added' => $date,
            'date_modified' => $date
        ];

        $lesson = new Lesson($row);

        $this->assertNotNull($lesson);
        $this->assertInstanceOf(Lesson::class, $lesson);

        $this->assertEquals(1, $lesson->getId());
        $this->assertEquals('CNC Basics', $lesson->getName());
        $this->assertEquals('Learn the basics of using a CNC Mill and Lathe', $lesson->getDescription());
    }

    public function testGettersAndSetters()
    {
        $lesson = new Lesson(null);
        $date = new DateTime();

        $lesson->setName('Test Lesson');
        $lesson->setDescription('Test Description');
        $lesson->setEnabled(1);
        $lesson->setDateAdded($date);
        $lesson->setDateModified($date);

        $this->assertContains('Test', $lesson->getName());
        $this->assertContains('Description', $lesson->getDescription());
        $this->assertEquals(1, $lesson->isEnabled());
        $this->assertEquals($date, $lesson->getDateAdded());
        $this->assertEquals($date, $lesson->getDateModified());
    }


}
