<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 6/24/18
 * Time: 8:17 PM
 */

/**
 * @file
 * A file loaded for all pages on the site.
 */

require __DIR__ . "/../vendor/autoload.php";

$localize = require 'localize.inc.php';
$config = new Manager\Config();

if(is_callable($localize)) {
    $localize($config);
}