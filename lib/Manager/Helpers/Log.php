<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/2/2018
 * Time: 4:40 PM
 */

namespace Manager\Helpers;


use Manager\Config;
use Manager\Models\Table;

class Log extends Table
{
    public function __construct(Config $config)
    {
        parent::__construct($config, "log");
    }
}