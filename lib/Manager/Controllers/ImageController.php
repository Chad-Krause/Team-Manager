<?php
/**
 * Created by PhpStorm.
 * User: ChadKrause
 * Date: 10/29/18
 * Time: 10:04 PM
 */

namespace Manager\Controllers;
use Manager\Config;
use Manager\Helpers\APIException;
use Manager\Helpers\JsonAPI;
use Manager\Helpers\Server;
use Manager\Models\Images;


class ImageController extends Controller
{

    public function __construct(Config $config, $user, array $request = [])
    {
        parent::__construct($config, $user, $request);
    }


    /**
     *
     */
    public function getResponse()
    {
        $response = new JsonAPI();

        $path = $this->request;

        try {

            if($path[0] == 'upload') {          // Upload image
                $result = $this->upload()->encode();
            } elseif (is_numeric($path[0])) {   // Get image
                $result = $this->get($path[0]);
            } else {                            // Error
                throw new APIException();
            }

        } catch (APIException $e) {
            header('Content-Type: application/json');
            $response->add_error($e->getMessage(), $e->getCode());
            return $response->encode();
        }

        return $result;

    }

    private function upload()
    {
        $server = new Server();

        if($this->user == null) {
            throw new APIException(
                APIException::INELIGIBLE_USER,
                APIException::AUTHENTICATION_ERROR
            );
        }

        if($server->files == array()) {
            $json = new JsonAPI();
            $json->add_error('No supplied file');
            return $json;
        }

        try{
            $file = $server->files["upload"];
            $type = $file['type'];
        } catch (\Exception $e) {
            $json = new JsonAPI();
            $json->add_error('No supplied file');
            return $json;
        }


        if ($file["error"] > 0 || $file["tmp_name"] == "") {
            // Error return
            $json = new JsonAPI();
            $json->add_error('No supplied file');
            return $json;
        }

        $name = $file["name"];
        $sepext = explode('.', strtolower($name));
        $fp = $file["tmp_name"];

        $fs = new Images($this->config);
        $res = $fs->writeFile(
            1,
            $name,
            $fp,
            $type,
            date(DATE_ISO8601, $server->server['REQUEST_TIME'])
        );

        $json = new JsonAPI();
        if($res) {
            $json->setData(['success' => true, 'id' => $res]);
            return $json;
        } else {
            $json->add_error('Bad Upload');
            return $json;
        }
    }

    private function get($id)
    {
        $images = new Images($this->config);
        $server = new Server();

        if ($this->user == null) {
            $server->header('Content-Type: application/json');
            $json = new JsonAPI();
            $json->add_error(APIException::NOT_LOGGED_IN_MSG, APIException::AUTHENTICATION_ERROR);
            http_response_code(404);
            return $json->encode();
        }


        $file = $images->readFileId($id);

        if (is_null($file)) {
            $server->header('Content-Type: application/json');
            $json = new JsonAPI();
            $json->add_error(APIException::IMAGE_NOT_FOUND, APIException::NOT_FOUND);
            http_response_code(404);
            return $json->encode();
        } else {
            $server->header('Content-Type: ' . $file['type']);
            $server->header("Content-Transfer-Encoding: Binary");
            $server->header("Content-disposition: attachment; filename=\"" . $file['name'] . "\"");
            //print_r('file: ' . $file['image']);
            return $file['image'];
        }
    }

    private function getThumbnail($id) {

    }
}