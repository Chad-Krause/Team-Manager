<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 7/2/18
 * Time: 12:45 AM
 */

namespace Manager\Controllers;

use Manager\Config;
use Manager\Models\User;
use Manager\Models\Users;

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

    protected $time;

    /**
     * Controller constructor.
     * @param Config $config
     * @param array $request
     * @param int $userid
     */
    public function __construct(Config $config, $time, $request = [])
    {
        $this->time = $time;
        $this->request = $request;
        $this->config = $config;
    }

    abstract public function getResponse();
}