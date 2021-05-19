<?php

namespace Payments;

class RequestTokenTokenize extends RequestToken {

    protected $_params = array(
        "action" => array(
            "type" => "mandatory",
            "values" => array(Payments::ACTION_TOKENIZE),
        ),
        "merchantId" => array("type" => "mandatory"),
        "password" => array("type" => "mandatory"),
        "timestamp" => array("type" => "mandatory"),
        "allowOriginUrl" => array("type" => "mandatory"),
        "customerId"  => array("type" => "optional"),
    );

    public function __construct() {
        parent::__construct();
        $this->_data["action"] = Payments::ACTION_TOKENIZE;
    }

}
