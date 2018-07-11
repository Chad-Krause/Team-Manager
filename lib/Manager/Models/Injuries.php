<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/10/2018
 * Time: 3:11 PM
 */

namespace Manager\Models;


use Manager\Config;

class Injuries extends Table
{
    public function __construct(Config $config)
    {
        parent::__construct($config, "injury");
    }

}