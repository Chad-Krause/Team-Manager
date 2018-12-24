<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 6/28/2018
 * Time: 11:31 AM
 */

/**
 * Function to localize the testing environment
 * @param $config Manager\Config the configuration for the site
 */
return function(\Manager\Config $config)
{
    date_default_timezone_set('America/Detroit');

    $config->setEmail('chad@chadkrause.com');
    $config->setRoot('/');
    $config->dbConfigure('mysql:dbname=TeamManagement;host=localhost:3306;charset=utf8',
        'root',       // Database user
        'root',     // Database password
        'ztest_');
    $config->setDomain('localhost:4200');
    $config->setServerDomain('http://localhost');
};