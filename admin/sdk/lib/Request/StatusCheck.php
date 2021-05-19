<?php

namespace Payments;

class RequestStatusCheck extends Request {

    public function __construct($values = array()) {
        parent::__construct();
        $this->_token_request = new RequestTokenStatusCheck($values);
        $this->_action_request = new RequestActionStatusCheck($values);
    }

}
