<?php

namespace Payments;

class RequestTokenCapture extends RequestTokenRefund {

    public function __construct() {
        parent::__construct();
        $this->_data["action"] = Payments::ACTION_CAPTURE;
    }

}
