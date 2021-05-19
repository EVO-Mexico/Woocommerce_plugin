<?php

namespace Payments;

class RequestActionAuth extends RequestAction {

    protected $_params = array(
        "merchantId" => array("type" => "mandatory"),
        "token" => array("type" => "mandatory"),
        "specinCreditCardCVV" => array(
            "type" => "conditional",
            "mandatory" => array(
                "paymentMethod" => "CreditCard",
                "channel" => "ECOM"
            ),
        ),
        "freeText" => array("type" => "optional"),
    );

}
