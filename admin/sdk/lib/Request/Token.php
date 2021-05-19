<?php

namespace Payments;

use Payments\Logger\Logger;

class RequestToken extends Request{
    public function execute($callback = NULL, $result_from_prev = array()){
        try {
            foreach ($result_from_prev as $k => $v) {
                $this->_data[$k] = $v;
            }
            $data = $this->validate();
            return $this->_exec_post(Config::$SessionTokenRequestUrl, $data, $callback);
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
}
