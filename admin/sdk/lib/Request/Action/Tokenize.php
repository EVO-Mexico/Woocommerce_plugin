<?php

namespace Payments;

class RequestActionTokenize extends RequestAction {

    protected $_params = array(
        "merchantId" => array("type" => "mandatory"),
        "token" => array("type" => "mandatory"),
        "number" => array("type" => "mandatory"),
        "nameOnCard" => array("type" => "mandatory"),
        "expiryMonth" => array("type" => "mandatory"),
        "expiryYear" => array("type" => "mandatory"),
        "startMonth" => array("type" => "optional"),
        "startYear" => array("type" => "optional"),
        "issueNumber" => array("type" => "optional"),
        "cardDescription" => array("type" => "optional"),
    );
}
