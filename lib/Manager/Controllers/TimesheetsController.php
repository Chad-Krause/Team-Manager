<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 10/7/18
 * Time: 3:10 PM
 */

namespace Manager\Controllers;

use Manager\Config;
use Manager\Helpers\JsonAPI;
use Manager\Models\PunchCard;
use Manager\Models\PunchCards;
use Manager\Models\UserHours;
use Manager\Helpers\APIException;

class TimesheetsController extends Controller
{
    public function __construct(Config $config, $time, array $request = [])
    {
        parent::__construct($config, $time, $request);
    }

    public function getResponse()
    {
        $response = new JsonAPI();

        $path = $this->request['path'];
        $data = null;

        try {

            switch ($path[0]) {
                case 'getTotalHours':
                    $data = $this->_getUserHours();
                    break;
                default:
                    $data = null;
                    throw new APIException(
                        APIException::INVALID_REQUEST,
                        APIException::NOT_FOUND
                    );
            }
            //TODO: Remove test code
            return $data;

        } catch (APIException $e) {
            $response->add_error($e->getMessage(), $e->getCode());
        }

        if($data !== null) {
            $response->setData($data);
        }

        return $response->encode();
    }

    private function _getUserHours()
    {
        //TODO: Remove test code

        $json = new JsonAPI();
        $punchcards = new PunchCards($this->config);
        $hours = $punchcards->getTotalHours(null);
        $json->setData(['hours' => $hours]);
        return $json->encode();
    }



}