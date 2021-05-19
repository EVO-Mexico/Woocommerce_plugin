<?php

namespace Payments;

class RequestAvailablePaymentSolution extends Request {

    public function __construct($values = array()) {
        parent::__construct();
        $this->_token_request = new RequestTokenAvailablePaymentSolution($values);
        $this->_action_request = new RequestActionAvailablePaymentSolution($values);
    }

}
