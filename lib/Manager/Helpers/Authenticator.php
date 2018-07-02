<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/2/2018
 * Time: 4:34 PM
 */

namespace Manager\Helpers;

use \Firebase\JWT\JWT;
use Manager\Config;

class Authenticator
{
    private $token;
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }
}