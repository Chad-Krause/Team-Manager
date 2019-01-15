<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 10/7/18
 * Time: 3:10 PM
 */

namespace Manager\Controllers;

use Manager\Config;
use Manager\Helpers\Authenticator;
use Manager\Helpers\JsonAPI;
use Manager\Helpers\Server;
use Manager\Models\Addresses;
use Manager\Models\PunchCard;
use Manager\Models\PunchCards;
use Manager\Models\UserHours;
use Manager\Helpers\APIException;
use Manager\Models\Users;
use Manager\Models\User;

class TimesheetsController extends Controller
{
    public function __construct(Config $config, $user, array $request = [])
    {
        parent::__construct($config, $user, $request);
    }

    public function getResponse()
    {
        $response = new JsonAPI();

        $path = $this->request;

        try {

            switch ($path[0]) {
                case 'getTotalHours':
                    $response = $this->_getUserHours();
                    break;
                case 'in':
                    $response = $this->_punch(PunchCards::IN);
                    break;
                case 'out':
                    $response = $this->_punch(PunchCards::OUT);
                    break;
                case 'getAllUsers':
                    $response = $this->_getAllUsers();
                    break;
                case 'hoursLogged':
                    $response = $this->_getHoursLogged();
                    break;
                case 'validatePin':
                    $response = $this->_validatePin();
                    break;
                case 'login':
                    $response = $this->_login();
                    break;
                case 'AllUsersOut':
                    $response = $this->_punchAllUsersOut();
                    break;
                default:
                    $response->add_error(
                        APIException::INVALID_REQUEST,
                        APIException::NOT_FOUND
                    );
            }

        } catch (APIException $e) {
            $response->add_error($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            $response->add_error($e->getMessage(), $e->getCode(), 500);
        }

        return $response;
    }

    private function _getUserHours()
    {
        $json = new JsonAPI();

        $punchcards = new PunchCards($this->config);
        $hours = $punchcards->getTotalHours(null);

        $json->setData(['hours' => $hours]);
        return $json;
    }

    /**
     * Clocks a user in or out
     * 1. Ensures the keys exist in the post
     * 2. Verifies the pin is correct
     * 3. Clocks in or out the user if the user is eligible
     * @return JsonAPI
     */
    private function _punch($type)
    {
        $json = new JsonAPI();
        $server = new Server();

        $post = $server->post;

        if(!Server::ensureKeys($post, ['userid', 'pin'])) {
            $json->add_error(
                APIException::REQUIRED_KEYS_ERROR_MSG,
                APIException::VALIDATION_ERROR
            );
            return $json;
        }

        $permissions = [User::ADMIN, User::MENTOR];

        if(!$this->hasPermission($permissions)) {
            $json->add_error(
                APIException::INELIGIBLE_USER,
                APIException::AUTHENTICATION_ERROR
            );
            return $json;
        }

        $ip = $server->getRequestIP();
        $userid = $post['userid'];
        $pin = $post['pin'];
        $time = Server::getRequestDatetime();

        $users = new Users($this->config);

        if(!$users->verifyPin($userid, $pin)){
            $json->add_error(
                APIException::INVALID_PIN_MSG,
                APIException::INVALID_PIN
            );
            return $json;
        }

        $punchcards = new PunchCards($this->config);

        if($type == PunchCards::IN) {
            $result = $punchcards->punchIn($userid, $time, $server->getRequestIP());
        } else {
            $result = $punchcards->punchOut($userid, $time);
        }

        if(!$result) {
            $json->add_error(
                APIException::UNABLE_TO_PUNCH_MSG,
                APIException::UNABLE_TO_PUNCH
            );
            return $json;
        }


        $json->setSuccess(true);
        return $json;
    }

    private function _getHoursLogged()
    {
        $json = new JsonAPI();
        $server = new Server();
        $get = $server->get;

        $permissions = [User::SAME_USER, User::ADMIN, User::MENTOR];

        if (!$server->ensureKeys($get, ['userid'])) {
            $json->add_error(
                APIException::REQUIRED_KEYS_ERROR_MSG,
                APIException::VALIDATION_ERROR
            );
            return $json;
        }

        $date = null;

        if(isset($get['date'])) {
            $date = $get['date'];
        }

        if (!$this->hasPermission($permissions, $get['userid'])) {
            $json->add_error(
                APIException::INELIGIBLE_USER,
                APIException::AUTHENTICATION_ERROR
            );
            return $json;
        }

        $timesheets = new PunchCards($this->config);
        $row = $timesheets->getUserHours($get['userid'], $date);

        $json->setData(['userid' => $row['userid'], 'totalTimeLogged' => $row['totalTimeLogged']]);
        return $json;
    }

    //POST
    /**
     * Login for Admins/Mentors only. Shorter JWT Time
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

        if(!($user->getRole() == User::ADMIN || $user->getRole() == User::MENTOR)) {
            throw new APIException(APIException::LOGIN_FOR_ADMIN_MENTOR_MSG, APIException::LOGIN_FOR_ADMIN_MENTOR);
        }

        $auth = new Authenticator($this->config);

        $exp = 60*30; // 60s x 30mins
        $jwt = $auth->mintToken($user, $server->getRequestTime(), $exp);

        $json->setData(['token' => $jwt, 'user' => $user->toArray()]);

        return $json;
    }

    //GET
    /**
     * Gets a list of all users, including
     * @return JsonAPI
     */
    private function _getAllUsers()
    {
        $json = new JsonAPI();
        $permissions = [User::MENTOR, User::ADMIN];


        if(!$this->hasPermission($permissions)) {
            $json->add_error(
                APIException::INELIGIBLE_USER,
                APIException::AUTHENTICATION_ERROR
            );
            return $json;
        }

        $users = new Users($this->config);
        $allUsers = $users->getAllUsersForTimesheets();

        $allUsersArray = [];
        foreach($allUsers as $user) {
            $allUsersArray[] = $user->toArray();
        }

        $json->setData($allUsersArray);
        return $json;
    }

    //POST
    /**
     * Punches all users that are punched in, out, and also
     * sets the 'auto_logout' flag in the database
     * @return JsonAPI
     */
    private function _punchAllUsersOut()
    {
        $json = new JsonAPI();
        $server = new Server();
        $permissions = [User::MENTOR, User::ADMIN];
        $post = $server->post;

        if(!Server::ensureKeys($post, ['userid', 'pin'])) {
            $json->add_error(
                APIException::REQUIRED_KEYS_ERROR_MSG,
                APIException::VALIDATION_ERROR
            );
            return $json;
        }

        if(!$this->hasPermission($permissions)) {
            $json->add_error(
                APIException::INELIGIBLE_USER,
                APIException::AUTHENTICATION_ERROR
            );
            return $json;
        }

        $userid = $post['userid'];
        $pin = $post['pin'];

        $users = new Users($this->config);
        if(!$users->verifyPin($userid, $pin)){
            $json->add_error(
                APIException::INVALID_PIN_MSG,
                APIException::INVALID_PIN
            );
            return $json;
        }

        $punchcards = new PunchCards($this->config);
        $count = $punchcards->punchAllUsersOut($server::getRequestDatetime());

        $json->setData(['numUsers' => $count]);
        return $json;
    }
}