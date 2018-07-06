<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 6/29/18
 * Time: 8:20 PM
 */

namespace Manager\Controllers;


use Manager\Config;
use Manager\Controllers\Controller;

class UserController extends Controller
{
    public function __construct(Config $config, array $request, int $userid)
    {
        parent::__construct($config, $request, $userid);
    }
}