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

    $config->setEmail('noreply@waverlyrobotics.org');
    $config->setRoot('/');
    $config->dbConfigure('mysql:dbname=TeamManagement;host=localhost;charset=utf8',
        'root',       // Database user
        'root',     // Database password
        '');/*,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        )
    );  */          // Table prefix
    $config->setDomain('localhost:4200');
    $config->setServerDomain('http://localhost');
};