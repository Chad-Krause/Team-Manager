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
header('Access-Control-Allow-Origin: *');
//require __DIR__ . '/vendor/autoload.php';

require "lib/config.inc.php";
use Manager\Config;
use Manager\Helpers\Authenticator;
use Manager\Helpers\JsonAPI;
use Manager\Helpers\APIException;
use Manager\Controllers\TimesheetsController;

// respond to preflights
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // return only the headers and not the content
    header('Access-Control-Allow-Headers: *');
    exit;
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
            header('Content-Type: application/json');
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
        header('Content-Type: application/json');
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
        header('Content-Type: application/json');
        $result = $controller->getResponse()->encode();
        break;

    case 'tidbits':
        $controller = new \Manager\Controllers\TidbitsController(
            $config,
            $user,
            $request
        );
        header('Content-Type: application/json');
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