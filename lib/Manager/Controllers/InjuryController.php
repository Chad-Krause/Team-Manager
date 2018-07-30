<?php
/**
 * Created by PhpStorm.
 * User: ckrause
 * Date: 7/17/2018
 * Time: 11:37 AM
 */

namespace Manager\Controllers;

use Manager\Config;


class InjuryController extends Controller
{

    public function __construct(Config $config, $time, array $request)
    {
        parent::__construct($config, $time, $request);
    }

    public function getResponse()
    {
        // TODO: Implement getResponse() method.
    }

}