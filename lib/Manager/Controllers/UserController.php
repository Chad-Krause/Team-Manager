<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 6/29/18
 * Time: 8:20 PM
 */

//TODO: Refactor all of this code to use the better method using the Server class

namespace Manager\Controllers;


use Manager\Helpers\APIException;
use Manager\Config;
use Manager\Helpers\Authenticator;
use Manager\Helpers\JsonAPI;
use Manager\Models\User;
use Manager\Models\Users;

class UserController extends Controller
{

    public function __construct(Config $config, $user, array $request)
    {
        parent::__construct($config, $user, $request);
    }

    public function getResponse() {
        $response = new JsonAPI();

        $path = $this->request['path'];
        $data = null;

        try {

            switch ($path[0]) {
                case 'login': // /user/login
                    $data = $this->_login();
                    break;

                case 'logout':
                    $data = $this->_logout();
                    break;

                case 'get': // /user/get/##
                    if(!isset($path[1]) || !is_numeric($path[1])) {
                        throw new APIException(APIException::NO_USERID, APIException::USERID_NOT_FOUND);
                    }
                    $data = $this->_getUserInformation($path[1]);
                    break;

                default:
                    $data = new JsonAPI();
                    $data->add_error(
                        APIException::INVALID_REQUEST,
                        APIException::NOT_FOUND
                    );
                    break;
            }

        } catch (APIException $e) {
            $response->add_error($e->getMessage(), $e->getCode());
        }

        if($data !== null) {
            $response->setData($data);
        }

        return $response->encode();
    }

    /**
     * Logs in a user using the request variable. Must have email and password set in the request
     * @throws APIException if email or password isn't set
     * @throws APIException if email and password doesn't match
     * @throws APIException if the user hasn't been confirmed yet
     * @return array
     */
    private function _login() {
        if(!isset($this->request['email']) || !isset($this->request['password'])) {
            throw new APIException(APIException::EMAIL_PASSWORD_NOT_SET, APIException::EMAIL_PASSWORD_NOT_FOUND);
        }

        $users = new Users($this->config);
        $user = $users->login($this->request['email'], $this->request['password']);

        if($user === null) {
            throw new APIException(APIException::INCORRECT_LOGIN, APIException::EMAIL_PASSWORD_WRONG);
        }

        if(!$user->isConfirmed()) {
            throw new APIException(APIException::UNCONFIRMED_USER, APIException::USER_NOT_CONFIRMED);
        }

        $auth = new Authenticator($this->config);

        $jwt = $auth->mintToken($user, $this->time);

        // Bad practice to use superglobals inside classes but I don't know of a better way to do it.
        $_COOKIE[Config::AUTH_COOKIE] = $jwt;

        return ['success' => true];
    }

    private function _logout() {
        $_COOKIE[Config::AUTH_COOKIE] = null;
        return ['success' => true];
    }

    private function _getUserInformation($userid) {

        // make sure you get an integer for the userid
        $userid = intval($userid);
        $permissions = [User::MENTOR, User::ADMIN];

        $auth = new Authenticator($this->config);
        $jwtid = $auth->getUserIdFromToken($_COOKIE[Config::AUTH_COOKIE]);

        $users = new Users($this->config);
        $user = $users->get($jwtid);

        // If the user is an admin, mentor, or themselves, they can access the information
        if(!in_array($user->getRole(), $permissions)) {
            if($user->getId() != $userid) {
                throw new APIException(APIException::INELIGIBLE_USER,APIException::AUTHENTICATION_ERROR);
            }
        }

        // At this point, whoever wants the request has the right permissions to get the information.

        $users = new Users($this->config);
        $user = $users->get($userid);

        return $user->toArray();
    }
}