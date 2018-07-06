<?php
/**
 * @file
 * Support for the standard JSON API
 * Thank you Dr. Charles Owen
 */

namespace Manager\Helpers;

/**
 * Support for the standard JSON API
 */
class JsonAPI

{
    public function __construct($data = null) {
        if($data !== null) {
            if(isset($data['errors'])) {
                $this->errors = $data['errors'];
            }

            if(isset($data['data'])) {
                $this->data = $data['data'];
            }
        }
    }

    /**
     * Add an error to the response.
     * @param $title string Text associated with the error
     * @param null $code Optional error code. See APIException for valid codes
     * @param int $status HTTP status value
     */
    public function add_error($title, $code=null, $status=400) {
        $error = ['status'=>$status, 'title'=>$title];
        if($code !== null) {
            $error['code'] = $code;
        }
        $this->errors[] = $error;
    }

    /**
     * Add standard database select error message
     */
    public function database_select_error() {
        $this->add_error("Unable to select database", self::UNABLE_TO_SELECT_DATABASE);
    }

    /**
     * Add data to an API response
     * @param $data array an associative array of data
     */
    public function setData($data) {
        if($data != null) {
            $this->data = $data;
        }
    }

    /**
     * Get all data of a given type.
     * @param $type string Type to search for (like "post")
     * @return array Array of items.
     */
    public function get_data($type) {
        $ret = [];
        foreach($this->data as $data) {
            if($data['type'] === $type) {
                $ret[] = $data;
            }
        }

        return $ret;
    }

    /**
     * Encode response into JSON
     * @return string JSON
     */
    public function encode() {
        $json = [];
        if(count($this->errors) > 0) {
            $json['errors'] = $this->errors;
        }

        if($this->data !== null) {
            $json['data'] = $this->data;
        }

        return json_encode($json);
    }

    private $errors = array();
    private $data = array();

}