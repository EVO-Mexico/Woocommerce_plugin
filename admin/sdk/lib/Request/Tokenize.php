<?php

namespace Payments;

class RequestTokenize extends Request {

    public function __construct($values = array()) {
        parent::__construct();
        $this->_token_request = new RequestTokenTokenize($values);
        $this->_action_request = new RequestActionTokenize($values);
    }

}
