<?php

namespace Payments;

class RequestCapture extends RequestRefund {

    public function __construct($values = array()) {
        parent::__construct();
        $this->_token_request = new RequestTokenCapture($values);
        $this->_action_request = new RequestActionCapture($values);
    }

}
