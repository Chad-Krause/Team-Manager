<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 6/25/18
 * Time: 9:46 PM
 */


/* Main entry point for APIs
 *
 * The data flow is as follows:
 * 1. Get user from JWT if available
 * 2. Send post data to the controller
 * 3. Return the JSON from the controller by calling getResponse()
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
//require __DIR__ . '/vendor/autoload.php';

require "lib/config.inc.php";
use Manager\Config;
use Manager\Helpers\Authenticator;
use Manager\Helpers\JsonAPI;
use Manager\Helpers\APIException;
use Manager\Controllers\TimesheetsController;

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

if (isset($_SERVER['HTTP_ORIGIN'])) {
    // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
    // you want to allow, and if so:
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        // may also be using PUT, PATCH, HEAD etc
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

try {
    //$request_url = explode("/", $_SERVER['REQUEST_URI']);
    $request_url = preg_split('/([\/&\?@])/', $_SERVER['REQUEST_URI']);
    $request = array_slice($request_url, 3);
} catch (\Exception $e) {
    $json = new JsonAPI();
    $json->add_error(
        APIException::INVALID_REQUEST . $e->getMessage(),
        APIException::NOT_FOUND
    );
    return $json->encode();
}


// Get user from request (if authenticated)
$user = Authenticator::GetUser($config);


$result = null;

//print_r($request_url);

switch ($request_url[2]) {

    case 'image':
        if($request_url[3] == 'upload') {
            header('Content-Type: application/json; charset=utf-8');
        }

        $controller = new \Manager\Controllers\ImageController(
            $config,
            $user,
            $request
        );

        $result = $controller->getResponse();
        break;

    case 'punch':
        $controller = new TimesheetsController(
            $config,
            $user,
            $request
        );
        header('Content-Type: application/json; charset=utf-8');
        $result = $controller->getResponse()->encode();

        break;

    case 'injury':

        break;

    case 'user':
        $controller = new \Manager\Controllers\UserController(
            $config,
            $user,
            $request
        );
        header('Content-Type: application/json; charset=utf-8');
        $result = $controller->getResponse()->encode();
        break;

    case 'tidbits':
        $controller = new \Manager\Controllers\TidbitsController(
            $config,
            $user,
            $request
        );
        header('Content-Type: application/json; charset=utf-8');
        $result = $controller->getResponse()->encode();
        break;

    default:
        $json = new JsonAPI();
        $json->add_error(
            APIException::NOT_FOUND_MSG,
            APIException::NOT_FOUND
        );
        $result = $json->encode();
}

echo $result;