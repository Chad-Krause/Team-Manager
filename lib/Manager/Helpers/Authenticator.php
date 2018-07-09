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

    //TODO: Add user permission to the JWT, Modify tests to accommodate

    const DEFAULT_EXPIRATION_TIME = 86400;
    const INVALID_JWT = 'The login token was corrupt or non existant. Please log in.';

    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
        JWT::$leeway = 60; // $leeway in seconds
    }

    public function mintToken(User $user, $time = null, $expiration = null, $additional_payload = null)
    {
        if($time === null) {
            $time = time();
        }

        if($expiration === null) {
            $expiration = Authenticator::DEFAULT_EXPIRATION_TIME;
        }

        $token = array(
            'iss' => $this->config->getDomain(),
            'aud' => 'waverlyrobotics.org',
            'iat' => $time,
            'nbf' => $time,
            'exp' => $time + $expiration,
            'data' => [
                'userid' => $user->getId(),
                'type' => $user->getRole()
            ]
        );

        if($additional_payload !== null) {
            foreach($additional_payload as $key => $value) {
                $token['data'][$key] = $value;
            }
        }

        $jwt = JWT::encode($token, $this->config::PRIVATEKEY,'RS256');

        return $jwt;
    }

    public function getUserIdFromToken($jwt)
    {
        try {
            $token = JWT::decode($jwt, Config::PUBLICKEY, array('RS256'));
            $decoded = json_decode(json_encode($token), true);
            $id = $decoded['data']['userid'];
        } catch (\Exception $e) {
            throw new APIException(self::INVALID_JWT, APIException::AUTHENTICATION_ERROR);
        }

        return (int)$id;
    }
}