<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 7/2/18
 * Time: 12:45 AM
 */

namespace Manager\Controllers;

use Manager\Config;
use Manager\Helpers\APIException;
use Manager\Models\User;

abstract class Controller
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var array
     */
    protected $request;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Controller constructor.
     * @param Config $config
     * @param array $request
     * @param int $userid
     */
    public function __construct(Config $config, $user, $request = [])
    {
        $this->user = $user;
        $this->request = $request;
        $this->config = $config;
    }

    abstract public function getResponse();

    /**
     * Returns true if the user has permission
     * @param array $permissions
     * @param $accessUserId int userid of the data the request needs to access
     * @return bool
     */
    public function hasPermission(array $permissions, $accessUserId = null) {
        // If the user is an admin, mentor, or themselves, they can access the information
        return !$this->isUserNull() && ($this->userHasPermission($permissions) || $this->isSameUser($accessUserId));
    }

    private function isUserNull()
    {
        return $this->user == null;
    }

    private function isSameUser($accessUserId) {
        return !is_null($accessUserId) && $this->user->getId() == $accessUserId;
    }

    private function userHasPermission(array $permissions)
    {
        return in_array($this->user->getRole(), $permissions);
    }
}