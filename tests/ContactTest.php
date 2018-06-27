<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 6/27/2018
 * Time: 2:05 PM
 */

require __DIR__ . "/../vendor/autoload.php";
use Manager\Models\Contact;

class ContactTest extends \PHPUnit\Framework\TestCase
{
    public function test__construct()
    {
        $row1 = [
            'id' => 1,
            'firstname' => 'Mom',
            'lastname' => 'Robotics',
            'phone' => '5175550000',
            'email' => 'roboticsmom@waverlyrobotics.org',
            'streetaddress' => '160 Snow Road',
            'city' => 'Lansing',
            'zip' => 48823,
            'state' => 'MI',
            'notes' => 'Awesome Mom'
        ];

        $row2 = [
            'id' => 2,
            'firstname' => 'Dad',
            'lastname' => 'Football',
            'phone' => '9994440000'
        ];

        $contact1 = new Contact($row1);
        $contact2 = new Contact($row2);

        $this->assertNotNull($contact1);
        $this->assertNotNull($contact2);

        $this->assertEquals(1, $contact1->getId());
        $this->assertEquals('Mom', $contact1->getFirstname());
        $this->assertEquals('Robotics', $contact1->getLastname());
        $this->assertEquals('5175550000', $contact1->getPhone());
        $this->assertEquals('roboticsmom@waverlyrobotics.org', $contact1->getEmail());
        $this->assertEquals('160 Snow Road', $contact1->getStreetaddress());
        $this->assertEquals('Lansing', $contact1->getCity());
        $this->assertEquals(48823, $contact1->getZip());
        $this->assertEquals('MI', $contact1->getState());
        $this->assertEquals('Awesome Mom', $contact1->getNotes());

        $this->assertEquals(2, $contact2->getId());
        $this->assertEquals('Dad', $contact2->getFirstname());
        $this->assertEquals('Football', $contact2->getLastname());
        $this->assertEquals('9994440000', $contact2->getPhone());
        $this->assertNull($contact2->getEmail());
        $this->assertNull($contact2->getStreetaddress());
        $this->assertNull($contact2->getCity());
        $this->assertNull($contact2->getZip());
        $this->assertNull($contact2->getState());
        $this->assertNull($contact2->getNotes());
    }

    public function testSetters()
    {
        $row = [
            'id' => 2,
            'firstname' => 'Dad',
            'lastname' => 'Football',
            'phone' => '9994440000'
        ];

        $contact = new Contact($row);

        $contact->setFirstname('Chad');
        $contact->setLastname('Krause');
        $contact->setPhone('2485550000');
        $contact->setEmail('Chad@chadkrause.com');
        $contact->setStreetaddress('160 Snow Road');
        $contact->setCity('Lansing');
        $contact->setZip(99999);
        $contact->setState('MI');
        $contact->setNotes('Crazy!');

        $this->assertEquals('Chad', $contact->getFirstname());
        $this->assertEquals('Krause', $contact->getLastname());
        $this->assertEquals('2485550000', $contact->getPhone());
        $this->assertEquals('chad@chadkrause.com', $contact->getEmail());
        $this->assertEquals('160 Snow Road', $contact->getStreetaddress());
        $this->assertEquals('Lansing', $contact->getCity());
        $this->assertEquals(99999, $contact->getZip());
        $this->assertEquals('MI', $contact->getState());
        $this->assertEquals('Crazy!', $contact->getNotes());

    }

    public function testGetAddress()
    {
        $row = [
            'id' => 1,
            'firstname' => 'Mom',
            'lastname' => 'Robotics',
            'phone' => '5175550000',
            'email' => 'roboticsmom@waverlyrobotics.org',
            'streetaddress' => '160 Snow Road',
            'city' => 'Lansing',
            'zip' => 48823,
            'state' => 'MI',
            'notes' => 'Awesome Mom'
        ];

        $contact = new Contact($row);

        $this->assertEquals('160 Snow Road, Lansing, MI 48823', $contact->getAddress());
    }
}
