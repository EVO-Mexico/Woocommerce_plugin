<?php

namespace Payments;

class RequestTokenRefund extends RequestToken {

    protected $_params = array(
        "merchantId" => array("type" => "mandatory"),
        "originalMerchantTxId" => array("type" => "mandatory"),
        "password" => array("type" => "mandatory"),
        "action" => array(
            "type" => "mandatory",
            "values" => array(Payments::ACTION_REFUND, Payments::ACTION_CAPTURE),
        ),
        "timestamp" => array("type" => "mandatory"),
        "allowOriginUrl" => array("type" => "mandatory"),
        "amount" => array("type" => "mandatory"),
        "originalTxId" => array("type" => "optional"),
        "agentId" => array("type" => "optional"),
    );

    public function __construct() {
        parent::__construct();
        $this->_data["action"] = Payments::ACTION_REFUND;
    }

}
