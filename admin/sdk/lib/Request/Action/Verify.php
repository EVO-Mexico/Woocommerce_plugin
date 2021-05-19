<?php

namespace Payments;

class RequestActionVerify extends RequestActionAuth {

    public function __construct() {
        parent::__construct();
        $this->_data["action"] = Payments::ACTION_VERIFY;
    }

}
