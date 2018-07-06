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

require "lib/config.inc.php";
use Manager\Config;

$request_url = $_SERVER['REQUEST_URI'];
$time = time();


$request_url = strtok($request_url, "/");


/*
 * If the user is authenticated, mint a new token
 */
if(isset($_COOKIE[Config::AUTH_COOKIE]))
{

}