<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 6/25/18
 * Time: 9:46 PM
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

require "lib/config.inc.php";
use Manager\Config;

$request_url = $_SERVER['REQUEST_URI'];

