<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/6/2018
 * Time: 1:19 PM
 */

use PHPUnit\Framework\TestCase;
use \Firebase\JWT\JWT;
use \Manager\Config;

class JWTTest extends TestCase
{

    public function testJWT()
    {
        $time = time();

        $token = array(
            'iat' => $time,
            'exp' => $time + 86400,
            'data' => [
                'userid' => 7226
            ]
        );

        $jwt = JWT::encode($token, Config::PRIVATEKEY, 'RS256');

        $decoded = JWT::decode($jwt, Config::PUBLICKEY, array('RS256'));
        $decoded = json_decode(json_encode($decoded), true);

        $this->assertEquals($token, $decoded);
    }
}
