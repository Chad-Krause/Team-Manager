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
    $config->setRoot('/~krausec6/step8');
    $config->dbConfigure('mysql:host=mysql-user.cse.msu.edu;dbname=msushrim_team',
        'msushrim_chad',       // Database user
        'Monsters308',     // Database password
        '');            // Table prefix
};