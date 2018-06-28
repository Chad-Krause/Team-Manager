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
    $config->dbConfigure(
        'mysql:host=chadkrause.com;dbname=msushrim_team',
        'msushrim_pattrn',
        '%m8Q%ym?Rrw4',
        'ztest_'
    );
};