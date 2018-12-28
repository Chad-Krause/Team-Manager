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

    $server = 1; // 1 is production, 0 is localhost development

    /*
     * Waverly Robotics Production Server
     */

    if($server == 1) {
        $config->setEmail('noreply@waverlyrobotics.org');
        $config->setRoot('/');
        $config->dbConfigure('mysql:dbname=TeamManagement;host=127.0.0.1:3306;charset=utf8',
            'TeamManagementUser',       // Database user
            '%22N3MVbi7Qshd3M',     // Database password
            '');/*,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        )
    );  */          // Table prefix
        $config->setDomain('team.waverlyrobotics.org');
        $config->setServerDomain('https://api.waverlyrobotics.org');
    } else {
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
    }

};