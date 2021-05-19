<?php

namespace Payments;

class RequestActionAvailablePaymentSolution extends RequestAction {

    protected $_params = array(
        "merchantId" => array("type" => "mandatory"),
        "token" => array("type" => "mandatory"),
        "action" => array(
            "type" => "mandatory",
            "values" => array(Payments::ACTION_AVAILABLE_PAYMENT_SOLUTION),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->_data["action"] = Payments::ACTION_AVAILABLE_PAYMENT_SOLUTION;
    }

}
