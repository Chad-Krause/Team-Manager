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

$site = new Manager\Config();
$localize = require 'localize.inc.php';

if(is_callable($localize)) {
    $localize($site);
}