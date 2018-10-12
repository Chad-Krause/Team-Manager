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
 * 1. Refresh authentication token if it is available
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

$request = ['path' => ['getTotalHours']];

$controller = new TimesheetsController($config, time(), $request);
return $controller->getResponse();

$request_url = strtok($_SERVER['REQUEST_URI'], "/");

// Mint a new token if the user is logged in
Authenticator::refreshAuthToken();

// Get user from request (if authenticated)
$user = Authenticator::GetUser($config);

$result = null;
switch ($request_url[0]) {
    case 'user':
        break;
    case 'punch':
        //TODO: Remove test code
        $controller = new \Manager\TimesheetsController($config, time());
        return $controller->getResponse();
        break;
    case 'injury':

        break;
    default:
        $json = new JsonAPI();
        $json->add_error(
            APIException::NOT_FOUND
        );
        $result = $json->encode();
}

return $result;
