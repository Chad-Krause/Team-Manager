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
use Manager\Helpers\Email;
use Manager\Helpers\JsonAPI;
use Manager\Helpers\Server;
use Manager\Models\PunchCards;
use Manager\Models\Tidbits;
use Manager\Models\User;
use Manager\Models\Users;
use Manager\Models\Validators;
use PHPUnit\Util\Json;

class UserController extends Controller
{

    public function __construct(Config $config, User $user = null, array $request = [])
    {
        parent::__construct($config, $user, $request);
    }

    public function getResponse()
    {
        $response = new JsonAPI();
        $path = $this->request;
        try {

            switch ($path[0]) {
                case 'login': // /user/login
                    $response = $this->_login();
                    break;
                case 'checkEmail':
                    $response = $this->_checkEmail();
                    break;
                case 'get':
                    $response = $this->_getUserInformation();
                    break;
                case 'createUser':
                    $response = $this->_addUser();
                    break;
                case 'resetPassword':
                    $response = $this->_resetPassword();
                    break;
                case 'resetPasswordWithValidator':
                    $response = $this->_resetPasswordWithValidator();
                    break;
                case 'updateUser':
                    $response = $this->_updateUser();
                    break;
                default:
                    $response->add_error(
                        APIException::INVALID_REQUEST,
                        APIException::NOT_FOUND
                    );
                    break;
            }

        } catch (APIException $e) {
            $response->add_error($e->getMessage(), $e->getCode());
        }

        return $response;
    }

    //GET
    /**
     * Returns a json object stating whether the email is already in use in the system
     * @return JsonAPI
     */
    private function _checkEmail()
    {
        $json = new JsonAPI();
        $server = new Server();
        $get = $server->get;

        if(!Server::ensureKeys($get, ['email'])) {
            $json->add_error(
                APIException::REQUIRED_KEYS_ERROR_MSG,
                APIException::VALIDATION_ERROR
            );
            return $json;
        }

        $users = new Users($this->config);
        $email = trim($get['email']);

        $json->setData(['exists' => $users->exists($email)]);

        return $json;
    }

    //POST
    /**
     * Creates an initial user, unconfirmed
     * @return JsonAPI
     */
    private function _addUser()
    {
        $json = new JsonAPI();
        $server = new Server();

        $post = $server->post;

        if(!Server::ensureKeys($post, ['email', 'password', 'confirmPassword', 'firstname', 'lastname'])) {
            $json->add_error(
                APIException::REQUIRED_KEYS_ERROR_MSG,
                APIException::VALIDATION_ERROR
            );
            return $json;
        }

        $email = strtolower(strip_tags($post['email']));
        $password = $post['password'];
        $confirmPassword = $post['confirmPassword'];
        $firstname = strip_tags($post['firstname']);
        $lastname = strip_tags($post['lastname']);

        $date = new \DateTime();
        $date->setTimestamp($server->getRequestTime());

        if($password != $confirmPassword) {
            $json->add_error(
                APIException::PASSWORD_CONFIRMATION_FAIL_MSG,
                APIException::PASSWORD_CONFIRMATION_FAIL
            );
            return $json;
        }

        $users = new Users($this->config);
        try {
            $id = $users->createUser($email, $password, $firstname, $lastname, $date);
        } catch (APIException $e) {
            $json->add_error($e->getMessage(), $e->getCode());
            return $json;
        }

        $json->setData(['userid' => $id]);
        return $json;
    }

    //POST
    /**
     * Logs in a user using the request variable. Must have email and password set in the request
     * @throws APIException if email or password isn't set
     * @throws APIException if email and password doesn't match
     * @throws APIException if the user hasn't been confirmed yet
     * @return JsonAPI
     */
    private function _login() {
        $json = new JsonAPI();
        $server = new Server();
        $post = $server->post;

        if(!Server::ensureKeys($post, ['email', 'password'])) {
            $json->add_error(
                APIException::REQUIRED_KEYS_ERROR_MSG,
                APIException::VALIDATION_ERROR
            );
            return $json;
        }

        $users = new Users($this->config);
        $user = $users->login($post['email'], $post['password']);

        if($user === null) {
            throw new APIException(APIException::INCORRECT_LOGIN, APIException::EMAIL_PASSWORD_WRONG);
        }

        if(!$user->isConfirmed()) {
            throw new APIException(APIException::UNCONFIRMED_USER, APIException::USER_NOT_CONFIRMED);
        }

        $auth = new Authenticator($this->config);

        $jwt = $auth->mintToken($user, $server->getRequestTime());

        $json->setData(['token' => $jwt, 'user' => $user->toArray()]);

        return $json;
    }

    //GET
    /**
     * Gets all the user's information given a userid
     * @permission User::MENTOR, User::Admin
     * @keys id
     * @return JsonAPI
     */
    private function _getUserInformation() {
        $server = new Server();
        $get = $server->get;
        $json = new JsonAPI();

        if(!$server->ensureKeys($get, ['id'])) {
            $json->add_error(
                APIException::REQUIRED_KEYS_ERROR_MSG,
                APIException::VALIDATION_ERROR
            );
            return $json;
        }

        // make sure you get an integer for the userid
        $userid = intval($get['id']);
        $permissions = [User::MENTOR, User::ADMIN, User::SAME_USER];

        if(!$this->hasPermission($permissions, $userid)) {
            $json->add_error(
                APIException::INELIGIBLE_USER,
                APIException::AUTHENTICATION_ERROR
            );
            return $json;
        }

        $users = new Users($this->config);
        $user = $users->get($userid);

        $tidbits = new Tidbits($this->config);
        $userTidbits = $tidbits->getTidbitsByUserId($userid);

        $punch = new PunchCards($this->config);
        $totalHoursLogged = $punch->getUserHours($userid);

        $json->setData([
            'user' => $user->toArray(),
            'tidbits' => $userTidbits,
            'totalHoursLogged' => $totalHoursLogged
        ]);

        return $json;
    }

    //POST
    /**
     * @keys userid
     * @return JsonAPI
     */
    private function _resetPassword()
    {
        $server = new Server();
        $json = new JsonAPI();
        $post = $server->post;

        if(!$server->ensureKeys($post, ['email'])) {
            $json->add_error(
                APIException::REQUIRED_KEYS_ERROR_MSG,
                APIException::VALIDATION_ERROR
            );
            return $json;
        }

        $users = new Users($this->config);

        $users->createResetPasswordValidator($post['email'], new Email());

        $json->setSuccess(true);
        return $json;
    }

    //POST
    /**
     * @keys password, confirmPassword, validator
     * @return JsonAPI
     */
    private function _resetPasswordWithValidator()
    {
        $server = new Server();
        $post = $server->post;
        $json = new JsonAPI();

        if(!$server->ensureKeys($post, ['password', 'confirmPassword', 'validator'])) {
            $json->add_error(
                APIException::REQUIRED_KEYS_ERROR_MSG,
                APIException::VALIDATION_ERROR
            );
            return $json;
        }

        $users = new Users($this->config);

        $validator = trim($post['validator']);
        $password = trim($post['password']);
        $confirmPassword = trim($post['confirmPassword']);

        if($password != $confirmPassword) {
            throw new APIException(
                APIException::PASSWORD_CONFIRMATION_FAIL_MSG,
                APIException::PASSWORD_CONFIRMATION_FAIL
            );
        }

        $users->resetPasswordWithValidator($validator, $password);

        $json->setSuccess(true);
        return $json;
    }

    private function _updateUser()
    {
        $server = new Server();
        $post = $server->post;
        $json = new JsonAPI();

        if(!$server->ensureKeys($post, ['id'])) {
            $json->add_error(
                APIException::REQUIRED_KEYS_ERROR_MSG,
                APIException::VALIDATION_ERROR
            );
            return $json;
        }

        $id = $post['id'];

        $users = new Users($this->config);
        $user = $users->get($id);
        $permissions = [User::MENTOR, User::ADMIN, User::SAME_USER];

        if(!$this->hasPermission($permissions, $id)) {
            $json->add_error(
                APIException::INELIGIBLE_USER,
                APIException::AUTHENTICATION_ERROR
            );
            return $json;
        }

        /*
         * This will look for keys and determine if they are set.
         * If they are set, that field will update.
         */

        if(isset($post['firstName'])) {
            $user->setFirstname(strip_tags($post['firstName']));
        }

        if(isset($post['lastName'])) {
            $user->setLastname(strip_tags($post['lastName']));
        }

        if(isset($post['nickname'])) {
            $user->setNickname(strip_tags($post['nickname']));
        }

        if(isset($post['email'])) {
            $user->setEmail(strip_tags($post['email']));
        }

        if(isset($post['roleid'])) {
            $user->setRole(strip_tags($post['roleid']));
        }

        if(isset($post['birthday'])) {
            $user->setBirthday(strip_tags($post['birthday']));
        }

        if(isset($post['graduationYear'])) {
            $user->setGraduationyear(strip_tags($post['graduationYear']));
        }

        if(isset($post['profileimageid'])) {
            $user->setProfilePictureId(intval($post['profileimageid']));
        }

        $time = Server::getRequestDatetime();
        $success = $users->updateUser($user, $time);

        if($success) {
            $user = $users->get($id);
            $json->setData($user->toArray());
            return $json;
        }

        $json->setSuccess(false);
        return $json;
    }
}