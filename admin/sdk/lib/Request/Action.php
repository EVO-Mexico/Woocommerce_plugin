<?php

namespace Payments;

use Payments\Logger\Logger;

class RequestAction extends Request{

    public function execute($callback = NULL, $result_from_prev = array()){
        try {
            foreach ($result_from_prev as $k => $v) {
                $this->_data[$k] = $v;
            }
            $data = $this->validate();
            return $this->_exec_post(Config::$PaymentOperationActionUrl, $data, $callback);
        } catch (PaymentsExceptionConfigurationEndpointNotSet $e) {
            Logger::error($e);
            throw new TurnkeyValidationException();
        } catch (PaymentsExceptionProcessDataNotSet $e) {
            Logger::error($e);
            throw new TurnkeyValidationException();
        } catch (PaymentsExceptionExecuteNetworkError $e) {
            Logger::error($e);
            throw new TurnkeyCommunicationException();
        } catch (\Exception $e) {
            Logger::error($e);
            throw new TurnkeyInternalException();
        }
    }

    public function _get_data_from_prev($result_from_prev){
        $this->_data["merchantId"] = $result_from_prev["merchantId"];
        $this->_data["token"] = $result_from_prev["token"];
    }
}
