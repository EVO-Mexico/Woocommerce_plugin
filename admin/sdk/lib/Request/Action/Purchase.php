<?php

namespace Payments;

class RequestActionPurchase extends RequestActionAuth {

    public function __construct() {
        parent::__construct();
        $this->_data["action"] = Payments::ACTION_PURCHASE;
    }

}
