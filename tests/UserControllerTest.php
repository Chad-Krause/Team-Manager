<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/6/2018
 * Time: 3:13 PM
 */

use Manager\Controllers\UserController;
use PHPUnit\DbUnit\DataSet\YamlDataSet;
require_once 'DatabaseTest.php';

class UserControllerTest extends DatabaseTest
{
    protected function getDataSet()
    {
        return new YamlDataSet(dirname(__FILE__) . '/Datasets/user.yaml');
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

    public function testGetUser()
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
        $mentor = $users->get(2);
        $jwt = $auth->mintToken($mentor, $time, 3600);
        $_COOKIE[\Manager\Config::AUTH_COOKIE] = $jwt;
        $person = $users->get(1);

        $json = new \Manager\Helpers\JsonAPI();
        $json->setData($person->toArray());
        $expectedJson = $json->encode();

        // Should be getting user #1 - Chad Krause
        $actualJson = $control->getResponse();

        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testLogin() {
        $time = time();
        $success = new \Manager\Helpers\JsonAPI();
        $success->setData(['success' => true]);
        $successJson = $success->encode();
        $auth = new \Manager\Helpers\Authenticator(self::$config);
        $users = new \Manager\Models\Users(self::$config);

        /*
         * Log in with correct username and password
         */

        $request = array(
            'path' => ['login'],
            'email' => 'eddie@vanhalen.com',
            'password' => 'Panama'
        );

        $control = new UserController(self::$config, $time, $request);
        $response = $control->getResponse();

        // Create identical JWT to compare the one set in the header
        $evh = $users->get(3);
        $jwt = $auth->mintToken($evh, $time);

        print_r($evh);

        // Response must be ['success' => true]
        $this->assertEquals($successJson, $response);
        // Tokens must be the same
        $this->assertEquals($jwt, $_COOKIE[\Manager\Config::AUTH_COOKIE]);


        /*
         * Log in with incorrect credentials
         */
        $_COOKIE[\Manager\Config::AUTH_COOKIE] = null;
        $request = array(
            'path' => ['login'],
            'email' => 'eddie@vanhalen.com',
            'password' => 'WRONG PASSWORD'
        );

        $BadPasswordJsonAPI = new \Manager\Helpers\JsonAPI();
        $BadPasswordJsonAPI->add_error($control::INCORRECT_LOGIN, \Manager\Helpers\APIException::EMAIL_PASSWORD_WRONG);
        $BadPasswordJson = $BadPasswordJsonAPI->encode();

        $control = new UserController(self::$config, $time, $request);
        $response = $control->getResponse();

        // No cookie should be set
        $this->assertNull($_COOKIE[\Manager\Config::AUTH_COOKIE]);
        // JSON Response with Error set
        $this->assertEquals($BadPasswordJson, $response);

        /*
         * Log in with unconfirmed user
         */
        /*$_COOKIE[\Manager\Config::AUTH_COOKIE] = null;
        $request = array(
            'path' => ['login'],
            'email' => 'eddie@vanhalen.com',
            'password' => 'WRONG PASSWORD'
        );

        $unconfirmedJsonAPI = new \Manager\Helpers\JsonAPI();
        $unconfirmedJsonAPI->add_error($control::INCORRECT_LOGIN, \Manager\Helpers\APIException::EMAIL_PASSWORD_WRONG);
        $BadPasswordJson = $BadPasswordJsonAPI->encode();

        $control = new UserController(self::$config, $time, $request);
        $response = $control->getResponse();

        // No cookie should be set
        $this->assertNull($_COOKIE[\Manager\Config::AUTH_COOKIE]);
        // JSON Response with Error set
        $this->assertEquals($BadPasswordJson, $response);*/

    }
}
