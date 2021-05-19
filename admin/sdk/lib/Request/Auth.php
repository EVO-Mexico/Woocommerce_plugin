<?php

namespace Payments;

class RequestAuth extends Request {

    public function __construct($values = array()) {
        parent::__construct();
        $this->_token_request = new RequestTokenAuth($values);
        $this->_action_request = new RequestActionAuth($values);
    }

}
