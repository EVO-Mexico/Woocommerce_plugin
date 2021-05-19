<?php

namespace Payments;

class RequestVoid extends RequestRefund {

    public function __construct($values = array()) {
        parent::__construct();
        $this->_token_request = new RequestTokenVoid($values);
        $this->_action_request = new RequestActionVoid($values);
    }

}
