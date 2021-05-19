<?php

namespace Payments;

class RequestTokenVerify extends RequestTokenAuth {

    public function __construct() {
        parent::__construct();
        $this->_data["action"] = Payments::ACTION_VERIFY;
    }

}
