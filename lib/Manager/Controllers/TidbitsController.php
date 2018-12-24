<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 12/21/18
 * Time: 10:31 PM
 */

namespace Manager\Controllers;


use Manager\Config;
use Manager\Helpers\APIException;
use Manager\Helpers\JsonAPI;
use Manager\Helpers\Server;
use Manager\Models\Tidbits;
use Manager\Models\TidbitTypes;
use Manager\Models\User;

class TidbitsController extends Controller
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
                case 'getAllTidbitTypes':
                    $response = $this->_getAllTidbitTypes();
                    break;
                case 'addTidbit':
                    $response = $this->_addTidbit();
                    break;
                case 'editTidbit':
                    $response = $this->_editTidbit();
                    break;
                case 'deleteTidbit':
                    $response = $this->_deleteTidbit();
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
     * Returns a json object with all the tidbit types
     * @return JsonAPI
     */
    private function _getAllTidbitTypes()
    {
        $json = new JsonAPI();

        $tidbitTypes = new TidbitTypes($this->config);
        $types = $tidbitTypes->getAllTidbitTypes();

        $json->setData($types);

        return $json;
    }

    //POST
    /**
     * Returns a json object for success status
     * @return JsonAPI
     */
    private function _addTidbit()
    {
        $server = new Server();
        $post = $server->post;
        $json = new JsonAPI();

        if(!$server->ensureKeys($post, ['userid', 'tidbittypeid', 'value'])) {
            $json->add_error(
                APIException::REQUIRED_KEYS_ERROR_MSG,
                APIException::VALIDATION_ERROR
            );
            return $json;
        }

        // make sure you get an integer for the userid
        $userid = intval($post['userid']);
        $permissions = [User::ADMIN, User::SAME_USER];

        if(!$this->hasPermission($permissions, $userid)) {
            $json->add_error(
                APIException::INELIGIBLE_USER,
                APIException::AUTHENTICATION_ERROR
            );
            return $json;
        }

        $value = strip_tags($post['value']);
        $tidbittypeid = intval($post['tidbittypeid']);

        $tidbits = new Tidbits($this->config);
        $success = $tidbits->createTidbit($userid, $tidbittypeid, $value);

        $json->setSuccess($success);

        return $json;
    }

    //POST
    /**
     * Returns a json object for success status
     * @return JsonAPI
     */
    private function _editTidbit()
    {
        $server = new Server();
        $post = $server->post;
        $json = new JsonAPI();

        if(!$server->ensureKeys($post, ['userid', 'tidbittypeid', 'value'])) {
            $json->add_error(
                APIException::REQUIRED_KEYS_ERROR_MSG,
                APIException::VALIDATION_ERROR
            );
            return $json;
        }

        // make sure you get an integer for the userid
        $userid = intval($post['userid']);
        $permissions = [User::ADMIN, User::SAME_USER];

        if(!$this->hasPermission($permissions, $userid)) {
            $json->add_error(
                APIException::INELIGIBLE_USER,
                APIException::AUTHENTICATION_ERROR
            );
            return $json;
        }

        $tidbitTypeId = intval($post['tidbittypeid']);
        $value = strip_tags($post['value']);

        $tidbits = new Tidbits($this->config);
        $success = $tidbits->editTidbit($userid, $tidbitTypeId, $value);

        $json->setSuccess($success);
        return $json;
    }

    //POST
    /**
     * Returns a json object for success status
     * @return JsonAPI
     */
    private function _deleteTidbit()
    {
        $server = new Server();
        $post = $server->post;
        $json = new JsonAPI();

        if(!$server->ensureKeys($post, ['userid', 'tidbittypeid'])) {
            $json->add_error(
                APIException::REQUIRED_KEYS_ERROR_MSG,
                APIException::VALIDATION_ERROR
            );
            return $json;
        }

        // make sure you get an integer for the userid
        $userid = intval($post['userid']);
        $permissions = [User::ADMIN, User::SAME_USER];

        if(!$this->hasPermission($permissions, $userid)) {
            $json->add_error(
                APIException::INELIGIBLE_USER,
                APIException::AUTHENTICATION_ERROR
            );
            return $json;
        }

        $tidbitTypeId = intval($post['tidbittypeid']);

        $tidbits = new Tidbits($this->config);
        $success = $tidbits->deleteTidbit($userid, $tidbitTypeId);

        $json->setSuccess($success);

        return $json;
    }

}