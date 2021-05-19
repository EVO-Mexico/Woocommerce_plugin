<?php

namespace Payments;

class RequestActionCapture extends RequestActionRefund {

    public function __construct() {
        parent::__construct();
        $this->_data["action"] = Payments::ACTION_CAPTURE;
    }

}
