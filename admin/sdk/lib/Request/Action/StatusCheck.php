<?php

namespace Payments;

class RequestActionStatusCheck extends RequestAction {

    protected $_params = array(
        "merchantId" => array("type" => "mandatory"),
        "token" => array("type" => "mandatory"),
        "action" => array(
            "type" => "mandatory",
            "values" => array(Payments::ACTION_STATUS_CHECK),
        ),
        "txId" => array("type" => "optional"),
        "merchantTxId" => array("type" => "optional"),
    );

    public function __construct() {
        parent::__construct();
        $this->_data["action"] = Payments::ACTION_STATUS_CHECK;
    }

}
