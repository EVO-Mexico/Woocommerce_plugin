<?php

namespace Payments;

class RequestTokenPurchase extends RequestTokenAuth {

    public function __construct() {
        parent::__construct();
        $this->_data["action"] = Payments::ACTION_PURCHASE;
    }

}
