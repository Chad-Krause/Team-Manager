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
use Manager\Models\User;

class Authenticator
{
    private $token;
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function mintToken(User $user, $time = null, $expiration = null, $additional_payload = null)
    {
        $token = array(
            'iss' => $this->config->getDomain(),
            'aud' => 'waverlyrobotics.org',
            'iat' => $time,
            'nbf' => $time,
            'exp' => $time + 86400,
            'data' => [
                'userid' => $user->getId()
            ]
        );

        $jwt = JWT::encode($token, $this->config::PRIVATEKEY,'RS256');

        return $jwt;
    }

    //public function verifyToken(User)
}