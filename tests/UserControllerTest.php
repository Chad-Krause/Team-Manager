<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/6/2018
 * Time: 3:13 PM
 */

use Manager\Controllers\UserController;
use PHPUnit\Framework\TestCase;
require_once 'DatabaseTest.php';

class UserControllerTest extends DatabaseTest
{
    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/Datasets/user.xml');
    }

    public function test__construct()
    {
        $request = array(
            'path' => ['get', 1]
        );
        $time = time();
        $config = new \Manager\Config();
        $control = new UserController($config,$time,$request);

        $this->assertInstanceOf(UserController::class, $control);
    }

    public function testGetResponse()
    {
        $time = time();
        $users = new \Manager\Models\Users(self::$config);
        $expecteduser = $users->get(1);

        // Set up the authentication cookie
        $auth = new \Manager\Helpers\Authenticator(self::$config);
        $jwt = $auth->mintToken($expecteduser, $time, 3600);
        $_COOKIE[\Manager\Config::AUTH_COOKIE] = $jwt;


        /*
         * Test getting the user information
         * 1. Get the same user
         */
        $request = array(
            'path' => ['get', 1]
        );
        $config = self::$config;
        $control = new UserController($config,$time,$request);
        $actualJson = $control->getResponse();

        $json = new \Manager\Helpers\JsonAPI();
        $json->setData($expecteduser->toArray());
        $expectedJson = $json->encode();

        $this->assertEquals($expectedJson, $actualJson);

        /*
         * 2. Test failing to get another user without permission (Student getting someone else)
         */
        // This gets Mike Rowe, who is a student
        $student = $users->get(3);
        $jwt = $auth->mintToken($student, $time, 3600);
        $_COOKIE[\Manager\Config::AUTH_COOKIE] = $jwt;

        $json = new \Manager\Helpers\JsonAPI();
        $json->add_error(UserController::INELIGIBLE_USER, \Manager\Helpers\APIException::AUTHENTICATION_ERROR);
        $expectedJson = $json->encode();

        $actualJson = $control->getResponse();

        $this->assertEquals($expectedJson, $actualJson);

        /*
         * 3. Test Getting a user while not logged in
         */

        $_COOKIE[\Manager\Config::AUTH_COOKIE] = null;
        $actualJson = $control->getResponse();

        $json = new \Manager\Helpers\JsonAPI();
        $json->add_error(\Manager\Helpers\Authenticator::INVALID_JWT, \Manager\Helpers\APIException::AUTHENTICATION_ERROR);
        $expectedJson = $json->encode();

        $this->assertEquals($expectedJson, $actualJson);

        /*
         * 4. Test getting a user while logged into another account with privileges
         */
        // Get Eddie Van Halen, who is a mentor
        $student = $users->get(2);
        $jwt = $auth->mintToken($student, $time, 3600);
        $_COOKIE[\Manager\Config::AUTH_COOKIE] = $jwt;

        // Should be getting user #1 - Chad Krause
    }
}
