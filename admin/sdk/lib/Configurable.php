<?php

namespace Payments;

class Configurable implements \ArrayAccess, \Iterator, \Serializable {

    protected $_data = array();
    protected $_rc;

    const ACTION_TOKENIZE = "TOKENIZE";
    const ACTION_AUTH = "AUTH";
    const ACTION_CAPTURE = "CAPTURE";
    const ACTION_VOID = "VOID";
    const ACTION_PURCHASE = "PURCHASE";
    const ACTION_REFUND = "REFUND";
    const ACTION_STATUS_CHECK = "GET_STATUS";
    const ACTION_AVAILABLE_PAYMENT_SOLUTION = "GET_AVAILABLE_PAYSOLS";
    const ACTION_VERIFY = "VERIFY";
    const CHANNEL_ECOM = "ECOM";
    const CHANNEL_MOTO = "MOTO";
    const USER_DEVICE_MOBILE = "MOBILE";
    const USER_DEVICE_DESKTOP = "DESKTOP";
    const USER_DEVICE_UNKNOWN = "UNKNOWN";
    const DOCUMENT_TYPE_PASSPORT = "PASSPORT";
    const DOCUMENT_TYPE_NATIONAL_ID = "NATIONAL_ID";
    const DOCUMENT_TYPE_DRIVING_LICENSE = "DRIVING_LICENSE";
    const DOCUMENT_TYPE_UNIQUE_TAXPAYER_REFERENCE = "UNIQUE_TAXPAYER_REFERENCE";
    const DOCUMENT_TYPE_OTHER = "OTHER";
    const SEX_M = "M";
    const SEX_F = "F";

    public function __construct() {
        $this->_rc = new \ReflectionClass($this);
    }

    public function __call($name, $value) {
        try {
            if (($this->_request instanceof Request) and ( $this->_request->_rc->hasMethod("_{$name}"))) {
                return call_user_func_array(array($this->_request, "_{$name}"), $value);
            }
            switch (strtolower($name)) {
                case "auth":
                case "capture":
                case "purchase":
                case "refund":
                case "statuscheck":
                case "status_check":
                case "void":
                case "get_available_paysols":
                case "verify":
                case "tokenize":
                    return call_user_func_array(array($this->_request, $name), $this->_data);
                    break;
                case "merchantid":
                    Config::$MerchantId = $value[0];
                    break;
                case "password":
                    Config::$Password = $value[0];
                    break;
            }
            if (!isset($value[0])) {
                if (isset($this->_data[$name])) {
                    return $this->_data[$name];
                } else {
                    return NULL;
                }
            } else {
                if (is_array($value[0])) {
                    foreach ($value[0] as $k => $v) {
                        $this->_data[$k] = $v;
                    }
                } else {
                    $this->_data[$name] = $value[0];
                }
            }
            return $this;
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function _set($data = array()) {
        try {
            if ((is_array($data)) and ( count($data) > 0)) {
                foreach ($data as $name => $value) {
                    if (is_array($value)) {
                        call_user_func_array(array($this, "_set"), $value);
                    } else {
                        switch (strtolower($name)) {
                            case "merchantid":
                                Config::$MerchantId = $value;
                                break;
                            case "password":
                                Config::$Password = $value;
                                break;
                            default:
                                $this->_data[$name] = $value;
                                break;
                        }
                    }
                }
            }
            return $this;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function __set($name, $value) {
        switch (strtolower($name)) {
            case "merchantid":
                Config::$MerchantId = $value;
                break;
            case "password":
                Config::$Password = $value;
                break;
            default:
                $this->_data[$name] = $value;
                break;
        }
    }

    public function __get($name) {
        return isset($this->_data[$name]) ? $this->_data[$name] : null;
    }

    public function __isset($name) {
        return isset($this->_data[$name]);
    }

    public function __unset($name) {
        unset($this->_data[$name]);
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->_data[] = $value;
        } else {
            $this->_data[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->_data[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->_data[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
    }

    function rewind() {
        return reset($this->_data);
    }

    function current() {
        return current($this->_data);
    }

    function key() {
        return key($this->_data);
    }

    function next() {
        return next($this->_data);
    }

    function valid() {
        return key($this->_data) !== null;
    }

    public function serialize() {
        return serialize($this->_data);
    }

    public function unserialize($data) {
        $this->_data = unserialize($data);
    }

    public function __debugInfo() {
        return $this->_data;
    }

    public function testEnvironment() {
        Config::test();
        foreach (func_get_args()[0] as $k => $v) {
            $this->$k = $v;
        }
        return $this;
    }

    public function productionEnvironment() {
        Config::production();
        foreach (func_get_args()[0] as $k => $v) {
            $this->$k = $v;
        }
        return $this;
    }
	
	public function environmentUrls() {
        foreach (func_get_args()[0] as $k => $v) {
            $this->$k = $v;
        }
        Config::setUrls($this->tokenURL, $this->paymentsURL, $this->baseUrl, $this->jsApiUrl);
        return $this;
    }
	
    public function baseUrl() {
        return Config::$BaseUrl;
    }

    public function javaScriptUrl() {
        return Config::$JavaScriptUrl;
    }

    public function mobileUrl() {
      return Config::$BaseUrl;
    }

}
