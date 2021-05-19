<?php

namespace Payments;

class Response extends Configurable {

    protected $_info;
    protected $_request = array();

//
    public function __construct($response, $info = array()) {
        $this->_data = $response;
        $this->_info = new ResponseInfo($info);
    }

    public function get_info($name = null) {
        if (!is_null($name)) {
            if (isset($this->info->{$name})) {
                return $this->info->{$name};
            }
            return NULL;
        }
        return $this->info;
    }

    public function __debugInfo() {
        $data = array();
        if ((is_array($this->_request)) && (count($this->_request) > 0)) {
            $data["request"] = $this->_request;
        }
        $data["response"] = $this->_data;
        $data["additionalInfo"] = $this->_info;

        return $data;
    }

}
