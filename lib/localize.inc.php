<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 6/24/18
 * Time: 8:18 PM
 */

/**
 * Function to localize our site
 * @param $config Manager\Config the configuration object
 */
return function(Manager\Config $config) {
    // Set the time zone
    date_default_timezone_set('America/Detroit');

    $config->setEmail('chad@chadkrause.com');
    $config->setRoot('/');
    $config->dbConfigure('mysql:dbname=msushrim_team;host=chadkrause.com;charset=utf8',
        'msushrim_pattrn',       // Database user
        '%m8Q%ym?Rrw4',     // Database password
        '',
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        )
    );            // Table prefix
};