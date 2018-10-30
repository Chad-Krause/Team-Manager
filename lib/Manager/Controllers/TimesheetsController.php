<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 10/7/18
 * Time: 3:10 PM
 */

namespace Manager\Controllers;

use Manager\Config;
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
                    $data = $this->_getUserHours();
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
                default:
                    $response->add_error(
                        APIException::INVALID_REQUEST,
                        APIException::NOT_FOUND
                    );
            }

        } catch (APIException $e) {
            $response->add_error($e->getMessage(), $e->getCode());
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

        if(!Server::ensureKeys($post, ['userid', 'pin', 'time'])) {
            $json->add_error(
                APIException::REQUIRED_KEYS_ERROR_MSG,
                APIException::VALIDATION_ERROR
            );
            return $json;
        }

        $post = $server->post;
        $ip = $server->getRequestIP();
        $userid = $post['userid'];
        $pin = $post['pin'];
        $time = $post['time'];

        $address = new Addresses($this->config);
        $ipid = $address->get($ip);



        $users = new Users($this->config);

        if($users->verifyPin($userid, $pin)){
            $json->add_error(
                APIException::INVALID_PIN_MSG,
                APIException::INVALID_PIN
            );
            return $json;
        }



        $punchcards = new PunchCards($this->config);

        if($type == PunchCards::IN) {
            $result = $punchcards->punchIn($userid, $time, $ipid);
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
        $json->setData(['success' => true]);
        return $json;
    }

    private function _getAllUsers()
    {
        $users = new Users($this->config);
        $all = $users->getAllUsers();

        $usersArray = [];

        foreach($all as $user) {
            $usersArray[] = $user->toArray();
        }

        $json = new JsonAPI();
        $json->setData(['users' => $usersArray]);
        return $json;
    }



}