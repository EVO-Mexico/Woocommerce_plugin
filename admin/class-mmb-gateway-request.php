<?php
/**
 * Fired during gateway request
 *
 * @since      1.0.0
 *
 * @package    MMB_Gateway_Woocommerce
 * @subpackage MMB_Gateway_Woocommerce/admin
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generates requests to send to MMB.
 *
 * This class defines all code necessary for gateway request.
 *
 * @since      1.0.0
 * @package    MMB_Gateway_Woocommerce
 * @subpackage MMB_Gateway_Woocommerce/admin
 */
class MMB_Gateway_Request
{
    /**
     * Pointer to gateway making the request.
     *
     * @since    1.0.0
     * @access   protected
     * @var      MMB_Gateway_Gateway $gateway Gateway instance
     */
    protected $gateway;

    /**
     * Endpoint for requests from MMB.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $notify_url Endpoint URL
     */
    protected $notify_url;
    /**
     * parameters to initiate the SDK payment.
     *
     */
    protected $environment_params;
    /**
     * Initialize the class and set its properties.
     *
     * @since   1.0.0
     * @param    MMB_Gateway_Gateway $gateway
     */
    public function __construct($gateway)
    {
        $this->gateway = $gateway;
        $this->notify_url = WC()->api_request_url('EVOMexico');
        //include the gateway sdk and init the class
        include_once('sdk/payments.php');
        $this->environment_params['merchantId'] =  $this->gateway->api_merchant_id;
        $this->environment_params['password'] =  $this->gateway->api_password;
        $testmode = $this->gateway->testmode;
        if ($testmode){
            $this->environment_params['tokenURL'] = $this->gateway->api_test_token_url;
            $this->environment_params['paymentsURL'] = $this->gateway->api_test_payments_url;
            $this->environment_params['baseUrl'] = $this->gateway->api_test_cashier_url;
            $this->environment_params['jsApiUrl'] = $this->gateway->api_test_js_url;
        }else{
            $this->environment_params['tokenURL'] = $this->gateway->api_token_url;
            $this->environment_params['paymentsURL'] = $this->gateway->api_payments_url;
            $this->environment_params['baseUrl'] = $this->gateway->api_cashier_url;
            $this->environment_params['jsApiUrl'] = $this->gateway->api_js_url;
        }
    }

    /**
     * Get the token URL
     *
     * @since    1.0.0
     * @param    bool $sandbox
     * @return   string
     */
    public function get_token_url($sandbox = false)
    {
        if ($sandbox) {
            return $this->gateway->api_test_token_url;
        }
        return $this->gateway->api_token_url;
    }

    /**
     * Get the payment URL
     *
     * @since    1.0.0
     * @param    bool $sandbox
     * @return   string
     */
    public function get_payment_url($sandbox = false)
    {
        if ($sandbox) {
            return $this->gateway->api_test_payments_url;
        }
        return $this->gateway->api_payments_url;
    }

    /**
     * Get the cashier URL
     *
     * @since    1.0.0
     * @param    bool $sandbox
     * @return   string
     */
    private function get_cashier_url($sandbox = false)
    {
        if ($sandbox) {
            return $this->gateway->api_test_cashier_url;
        }
        return $this->gateway->api_cashier_url;
    }

    /**
     * Get the API js
     *
     * @since    1.0.0
     * @param    bool $sandbox
     * @return   string
     */
    private function get_api_js($sandbox = false)
    {
        if ($sandbox) {
            return $this->gateway->api_test_js_url;
        }
        return $this->gateway->api_js_url;
    }
    private function get_payment_action()
    {
        if($this->gateway->api_payment_action){
            return 'AUTH';
        }else{
            return 'PURCHASE';
        }
    }
    /**
     * @since    1.0.0
     * @param    WC_Order $order
     * @param    bool $sandbox
     * @return   array
     */
    private function get_mmb_gateway_token_args($order, $sandbox = false)
    {
       
        // to get only the site's domain url name to assign to the parameter allowOriginUrl . 
        //otherwise it will encounter a CORS issue when wordpress deployed inside a subfolder of the web server.
       
        $parse_result = parse_url(site_url());
        if(isset($parse_result['port'])){
            $allowOriginUrl = $parse_result['scheme']."://".$parse_result['host'].":".$parse_result['port'];
        }else{
            $allowOriginUrl = $parse_result['scheme']."://".$parse_result['host'];
        }
        
        //         if($this->gateway->api_payment_modes == '0'){
        //             //api_payment_modes : array('Iframe','Redirect','HostedPayPage')
        //             $paymentSolutionId = 500;//500 means for Credit Card option when it's with Iframe Mode
        //         }else{
        //             $paymentSolutionId = '';
        //         }
        $paymentSolutionId = ''; 
        $shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );
        if(empty($shop_page_url)){
            $shop_page_url = site_url();
        }
        $ip = $order->get_customer_ip_address();
        if($ip == '::1'){
            $ip = '127.0.0.1';
        }
        return array(
            'brandId' => $this->gateway->api_brand_id,
            'merchantTxId' => $order->get_order_number(),
            'action' => $this->get_payment_action(),
            'amount' => $order->get_total(),
            'currency' => get_woocommerce_currency(),
            'country' => wc_get_base_location()['country'],
            'language' => substr(get_locale(), 0,2), //to get the first 2 letters of the current language code
            'paymentSolutionId' => $paymentSolutionId,
            'channel' => 'ECOM',
            'allowOriginUrl' => $allowOriginUrl,
            "merchantNotificationUrl" => $this->notify_url.'?order_id='. $order->get_id(),
            "merchantLandingPageUrl" =>  $shop_page_url . "?wcapi=evomexico&order_id=" . $order->get_id(),
            "customerFirstName" => $order->get_billing_first_name(),
            "customerLastName" => $order->get_billing_last_name(),
            "customerEmail" => $order->get_billing_email(),
            "customerPhone" => $order->get_billing_phone(),
            "customerId" => strval($order->get_customer_id()),
            "userDevice" => 'DESKTOP',
            "userAgent" => $order->get_customer_user_agent(),
            "customerIPAddress" => $ip,
            "customerAddressHouseName" => substr($order->get_billing_address_1(),0,45),
            "customerAddressStreet" => substr($order->get_billing_address_1(),0,45),
            "customerAddressCity" => $order->get_billing_city(),
            "customerAddressPostalCode" => $order->get_billing_postcode(),
            "customerAddressCountry" => $order->get_billing_country(),
            "customerAddressState" => substr($order->get_billing_state(),0,3),
//             "customerAddressPhone" => $order->get_billing_phone(),
            "merchantChallengeInd" => '01',
            "merchantDecReqInd" => 'N',
            "merchantLandingPageRedirectMethod" => 'GET'
        );
    }
    /**
     * only for redirect Page payment check request, show the message to the customer, no updating the order
     *s
     * @since    1.0.0
     * @param    WC_Order $order
     * @param    generated merchantTxId $merchantTxId
     * @param    bool $sandbox
     * @return string
     */
    public function redirect_generate_check_request_form($order, $merchantTxId, $sandbox = false)
    {
        if ( $this->gateway->api_merchant_id === null || $this->gateway->api_merchant_id === ''
            || $this->gateway->api_password === null || $this->gateway->api_password === '' ) {
                return false;
            }
            
            $data = $this->get_mmb_gateway_check_token_args($order, $merchantTxId);
            try {
                $payments = (new Payments\Payments())->environmentUrls($this->environment_params);
                $status_check = $payments->status_check();
                $status_check->merchantTxId($data['merchantTxId'])->
                allowOriginUrl($data['allowOriginUrl']);
                $mmb_check_request_data = $status_check->execute();
            } catch (Exception $e) {
                return false;
            }
            unset($data);
            $order_id = $order->get_id();
            if( $mmb_check_request_data->result !== 'success' ) {
                
                $mmb_message = array(
                    'message' =>  __( 'Card payment failed.', 'mmb-gateway-woocommerce' ).__( 'Order ID:', 'mmb-gateway-woocommerce' ) . $order->get_id() . '.'.__( 'Transaction ID:', 'mmb-gateway-woocommerce' ).  $merchantTxId,
                    'message_type' => 'error'
                );
                update_post_meta($order_id, '_mmb_gateway_message', $mmb_message);
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
            }else{
                
                if($mmb_check_request_data->status == "NOT_SET_FOR_CAPTURE" || $mmb_check_request_data->status == "SET_FOR_CAPTURE" || $mmb_check_request_data->status == "CAPTURED"){
                    // Empty cart
                    if( function_exists('WC') ){
                        WC()->cart->empty_cart();
                    }
                    
                    $message = __('Thank you for shopping with us.<br />Your transaction was successful, payment was received.<br />Your order is currently being processed.', 'mmb-gateway-woocommerce');
                    $message .= '<br />'.__( 'Order ID:', 'mmb-gateway-woocommerce' ).$order->get_id() . '. '.__( 'Transaction ID:', 'mmb-gateway-woocommerce' ) . $merchantTxId;
                    $message_type = 'success';
                    $mmb_message = array(
                        'message' => $message,
                        'message_type' => $message_type
                    );
                    update_post_meta($order_id, '_mmb_gateway_message', $mmb_message);
                    header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
                    
                }else{
                    $mmb_message = array(
                        'message' =>  __( 'Card payment failed.', 'mmb-gateway-woocommerce' ).__( 'Order ID:', 'mmb-gateway-woocommerce' ) . $order->get_id() . '.'.__( 'Transaction ID:', 'mmb-gateway-woocommerce' ).  $merchantTxId,
                        'message_type' => 'error'
                    );
                    update_post_meta($order_id, '_mmb_gateway_message', $mmb_message);
                    header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
                }
            }
    }
    /**
     * Generate MMB payment check request
     *
     * @since    1.0.0
     * @param    WC_Order $order
     * @param    generated merchantTxId $merchantTxId
     * @param    bool $sandbox
     * @return string
     */
    public function generate_check_request_form($order, $merchantTxId, $sandbox = false)
    {
        if ( $this->gateway->api_merchant_id === null || $this->gateway->api_merchant_id === ''
            || $this->gateway->api_password === null || $this->gateway->api_password === '' ) {
            return false;
        }

        $data = $this->get_mmb_gateway_check_token_args($order, $merchantTxId);
        try {
            $payments = (new Payments\Payments())->environmentUrls($this->environment_params);
            $status_check = $payments->status_check();
            $status_check->merchantTxId($data['merchantTxId'])->
            allowOriginUrl($data['allowOriginUrl']);
            $mmb_check_request_data = $status_check->execute();
        } catch (Exception $e) {
            return false;
        }
        unset($data);
        $order_id = $order->get_id();
        if( $mmb_check_request_data->result !== 'success' ) {
            $order_status = $order->get_status();
            if($order_status != 'failed'){
                $order->update_status( 'failed', sprintf( __( 'Card payment failed.', 'mmb-gateway-woocommerce' ) ) );
            }
            $mmb_message = array(
                'message' =>  __( 'Card payment failed.', 'mmb-gateway-woocommerce' ).__( 'Order ID:', 'mmb-gateway-woocommerce' ) . $order->get_id() . '.'.__( 'Transaction ID:', 'mmb-gateway-woocommerce' ).  $merchantTxId,
                'message_type' => 'error'
            );
            update_post_meta($order_id, '_mmb_gateway_message', $mmb_message);
            $order->save();
            header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
            return;
        }else{
            
            if($mmb_check_request_data->status == "NOT_SET_FOR_CAPTURE"){
                $order_status = $order->get_status();
                if( $order_status != 'on-hold'){
                    //Auth transaction
                    update_post_meta( $order->get_id(), '_payment_status', 'on-hold');
                    $order->update_status( 'on-hold', sprintf( __( 'Card payment authorized.', 'mmb-gateway-woocommerce' ) ) );
                    
                    // Reduce stock levels
                    wc_reduce_stock_levels($order_id);
                }
            }else if($mmb_check_request_data->status == "SET_FOR_CAPTURE" || $mmb_check_request_data->status == "CAPTURED"){
                $order_status = $order->get_status();
                if($order_status != 'completed' && $order_status != 'processing'){
                    //Purchase transaction
                    update_post_meta( $order->get_id(), '_payment_status', 'completed');
                    $order->payment_complete();
                    if($this->gateway->api_success_status == '1'){
                        $order->update_status( 'completed', sprintf( __( 'Card payment completed.', 'mmb-gateway-woocommerce' ) ) );
                    }
                    //                 do_action( 'woocommerce_payment_complete', $order_id);
                    
                    // Reduce stock levels
                    wc_reduce_stock_levels($order_id);
                }
            }else{
                if($mmb_check_request_data->status == "STARTED" || $mmb_check_request_data->status == "WAITING_RESPONSE" || $mmb_check_request_data->status == "INCOMPLETE"){
                    $message = __('Thank you for shopping with us.<br />Your payment is in progress.', 'mmb-gateway-woocommerce');
                    $message_type = 'success';
                    $mmb_message = array(
                        'message' => $message,
                        'message_type' => $message_type
                    );
                    // Empty cart
                    if( function_exists('WC') ){
                        WC()->cart->empty_cart();
                    }
                    update_post_meta($order_id, '_mmb_gateway_message', $mmb_message);
                    //Do not handle these order status in the plugin system
                    header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
                    return;
                }
                $order_status = $order->get_status();
                if($order_status != 'failed'){
                    $order->update_status( 'failed', sprintf( __( 'Card payment failed.', 'mmb-gateway-woocommerce' ) ) );
                }
                $mmb_message = array(
                    'message' =>  __( 'Card payment failed.', 'mmb-gateway-woocommerce' ).__( 'Order ID:', 'mmb-gateway-woocommerce' ) . $order->get_id() . '.'.__( 'Transaction ID:', 'mmb-gateway-woocommerce' ).  $merchantTxId,
                    'message_type' => 'error'
                );
                update_post_meta($order_id, '_mmb_gateway_message', $mmb_message);
                $order->save();
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
                return;
            }
        }
        
        
        // Empty cart
        if( function_exists('WC') ){
            WC()->cart->empty_cart();
        }
        
        //save the EVO transaction ID into the database
        update_post_meta( $order->get_id(), '_transaction_id', $merchantTxId );
        
        $message = __('Thank you for shopping with us.<br />Your transaction was successful, payment was received.<br />Your order is currently being processed.', 'mmb-gateway-woocommerce');
        $message .= '<br />'.__( 'Order ID:', 'mmb-gateway-woocommerce' ).$order->get_id() . '. '.__( 'Transaction ID:', 'mmb-gateway-woocommerce' ) . $merchantTxId;
        $message_type = 'success';
        $mmb_message = array(
            'message' => $message,
            'message_type' => $message_type
        );
        update_post_meta($order_id, '_mmb_gateway_message', $mmb_message);
        $order->save();
        header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200);
    }

    /**
     * @since    1.0.0
     * @param    WC_Order $order
     * @param    bool $sandbox
     * @return   array
     */
    private function get_mmb_gateway_cashier_args($order, $sandbox = false, $token)
    {

        return apply_filters('woocommerce_mmb_gateway_args',
            array(
                'containerId' => "mmbCashierDiv",
                'token' => $token,
                'merchantId' => $this->gateway->api_merchant_id,
                'language' => substr(get_locale(), 0,2), //to get the first 2 letters of the current language code
                'integrationMode' => 'iframe'
//                 'successCallback' => 'handleResult',
//                 'failureCallback' => 'handleResult',
//                 'cancelCallback' => 'handleResult'
            )
            , $order);
    }

    /**
     * Generate MMB payment form
     *
     * @since    1.0.0
     * @param    WC_Order $order
     * @param    bool $sandbox
     * @return   string
     */
    public function generate_mmb_gateway_form($order, $sandbox = false)
    {
        if ( $this->gateway->api_merchant_id === null || $this->gateway->api_merchant_id === ''
            || $this->gateway->api_password === null || $this->gateway->api_password === '' ) {
            return false;
        }

        $post_data = $this->get_mmb_gateway_token_args($order, $sandbox);
        
        try {
            $payments = (new Payments\Payments())->environmentUrls($this->environment_params);
            $payment_eservice_pay_action = $this->gateway->api_payment_action;
            if ($payment_eservice_pay_action){
                $post_data['action'] = "AUTH";
                $payments_request = $payments->auth();
            }else {
                $post_data['action'] = "PURCHASE";
                $payments_request = $payments->purchase();
            }
            
            $payments_request->
            brandId($post_data['brandId'])->
            merchantTxId($post_data['merchantTxId'])->
            action($post_data['action'])->
            allowOriginUrl($post_data['allowOriginUrl'])->
            merchantLandingPageUrl($post_data['merchantLandingPageUrl'])->
            merchantNotificationUrl($post_data['merchantNotificationUrl'])->
            channel($post_data['channel'])->
            language($post_data['language'])->
            amount($post_data['amount'])->
            paymentSolutionId($post_data['paymentSolutionId'])->
            currency($post_data['currency'])->
            country($post_data['country'])->
            customerFirstName($post_data['customerFirstName'])->
            customerLastName($post_data['customerLastName'])->
            customerEmail($post_data['customerEmail'])->
            customerPhone($post_data['customerPhone'])->
            customerId($post_data['customerId'])->
            userDevice($post_data['userDevice'])->
            userAgent($post_data['userAgent'])->
            customerIPAddress($post_data['customerIPAddress'])->
            customerAddressHouseName($post_data['customerAddressHouseName'])->
            customerAddressStreet($post_data['customerAddressStreet'])->
            customerAddressCity($post_data['customerAddressCity'])->
            customerAddressPostalCode($post_data['customerAddressPostalCode'])->
            customerAddressCountry($post_data['customerAddressCountry'])->
            customerAddressState($post_data['customerAddressState'])->
//             customerAddressPhone($post_data['customerAddressPhone'])->
            merchantChallengeInd($post_data['merchantChallengeInd'])->
            merchantDecReqInd($post_data['merchantDecReqInd'])->
            merchantLandingPageRedirectMethod($post_data['merchantLandingPageRedirectMethod']);
            $token_request_data = $payments_request->token();
        } catch (Exception $e) {
            return false;
        }

        if( $token_request_data->result !== 'success' ) {
            return false; // Bail early
        }
        $shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );
        if(empty($shop_page_url)){
            $shop_page_url = site_url();
        }
        switch($this->gateway->api_payment_modes){
            //api_payment_modes : array('Iframe','Redirect','HostedPayPage')
            case '0':
                $mmb_cashier_args = $this->get_mmb_gateway_cashier_args( $order, $sandbox, $token_request_data->token );
                
                $mmb_form[] = '<form id="mmbForm" action="'.$shop_page_url;
                $mmb_form[] = '" method="get"><input type="hidden" name="wcapi" value="evomexico"/><input type="hidden" name="order_id" value="'.$order->get_id().'"/><input type="hidden" id="merchantTxId" name="merchantTxId" value=""/></form>';
                $mmb_form[] = '<div id="mmbCashierDiv"></div>';
                $mmb_form[] = '<script type="text/javascript" src="'. $this->get_api_js($sandbox) . '"></script>';
                $mmb_form[] = '<script type="text/javascript">';
                $mmb_form[] = 'function handleResult(result,data) {';
                $mmb_form[] = 'if(result == "success"){';
                $mmb_form[] = 'document.getElementById("merchantTxId").value = data.merchantTxId;';
                $mmb_form[] = 'document.getElementById("mmbForm").submit();}';
                $mmb_form[] = '}';
                $mmb_form[] = 'var cashier = com.myriadpayments.api.cashier();';
                $mmb_form[] = 'cashier.init({ baseUrl:"' . $this->get_cashier_url($sandbox) . '" } );';
                $mmb_form[] = 'cashier.show({';
                foreach ($mmb_cashier_args as $key => $value) {
                    $mmb_form[] = esc_attr($key) . ' : "' . esc_attr($value) . '",';
                }
                $mmb_form[] = 'successCallback:handleResult,';
                $mmb_form[] = 'failureCallback:handleResult,';
                $mmb_form[] = 'cancelCallback:handleResult';
                $mmb_form[] = '});';
                $mmb_form[] = '</script>';
                
                return implode('', $mmb_form);
            case '1':
                return $this->get_mmb_gateway_redirect_mode($sandbox,$token_request_data->token);
            default:
                return $this->get_mmb_gateway_hostedpaypage_mode($sandbox,$token_request_data->token);
        }
        
    }
    /**
     * To process with MMB Redirect Payment mode
     * @param    WC_Order $order
     * @param    bool $sandbox
     * @return   string
     */
    private function get_mmb_gateway_redirect_mode($sandbox = false,$token){
        $data = array();
        $data['token'] = $token; 
        $data['merchantId'] =  $this->gateway->api_merchant_id;
        $data['integrationMode'] = 'standalone';
        $form_html = '';
        $form_html .= '<form action="'.$this->get_cashier_url($sandbox).' " method="post">';
        foreach ($data as $a => $b) {
            $form_html .= "<input type='hidden' name='" . htmlentities($a) . "' value='" . htmlentities($b) . "'>";
        }
        $form_html .= '<button type="submit" class="button alt">'.__( 'Pay with EVOMexico', 'mmb-gateway-woocommerce' ).'</button> </form>';
        return $form_html;
    }
    /**
     * To process with MMB hostedpaypage Payment mode
     * @param    WC_Order $order
     * @param    bool $sandbox
     * @return   string
     */
    private function get_mmb_gateway_hostedpaypage_mode($sandbox = false,$token){
        $data = array();
        $data['token'] = $token;
        $data['merchantId'] =  $this->gateway->api_merchant_id;
        $data['integrationMode'] = 'hostedPayPage';
        $form_html = '';
        $form_html .= '<form action="'.$this->get_cashier_url($sandbox).' " method="post">';
        foreach ($data as $a => $b) {
            $form_html .= "<input type='hidden' name='" . htmlentities($a) . "' value='" . htmlentities($b) . "'>";
        }
        $form_html .= '<button type="submit" class="button alt">'.__( 'Pay with EVOMexico', 'mmb-gateway-woocommerce' ).'</button> </form>';
        return $form_html;
    }
    
    private function get_mmb_gateway_check_request_args($order, $token, $merchantTxId)
    {

        return array(
            'token' => $token,
            'merchantId' => $this->gateway->api_merchant_id,
            'action' => 'GET_STATUS',
            'merchantTxId' => $merchantTxId
        );
    }

    private function get_mmb_gateway_check_token_args($order, $merchantTxId)
    {
        //in some old web server, the PHP precision is 12, this is to make a work around for the conversion
        $number = round(microtime(true) * 1000);
        $milliseconds = sprintf('%1.0f', $number);
        return array(
            "merchantId" => $this->gateway->api_merchant_id,
            "password" => $this->gateway->api_password,
            "allowOriginUrl" => site_url(),
            "action" => "GET_STATUS",
            "timestamp" => $milliseconds,
            "merchantTxId" => $merchantTxId
        );
    }

    public function get_available_payment_solutions($sandbox = false) {

        include_once('class-mmb-gateway-request.php');

        if ( $this->gateway->api_merchant_id === null || $this->gateway->api_merchant_id === ''
            || $this->gateway->api_password === null || $this->gateway->api_password === '' ) {
            return array();
        }


        $mmb_token_args = $this->get_mmb_gateway_paysol_token_args();

        $token_request = wp_remote_post( $this->get_token_url( $sandbox ), array(
            'method'      => 'POST',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'body'        => $mmb_token_args,
            'cookies'     => array()
        ) );

        if( is_wp_error( $token_request ) ) {
            return array(); // Bail early
        }

        $token_request_body = wp_remote_retrieve_body( $token_request );
        $token_request_data = json_decode( $token_request_body);

        if( $token_request_data->result !== 'success' ) {
            return array(); // Bail early
        }

        $mmb_paysol_args = $this->get_mmb_gateway_paysol_args($token_request_data->token);

        $paysol_request = wp_remote_post( $this->get_payment_url($sandbox), array(
            'method'      => 'POST',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'body'        => $mmb_paysol_args,
            'cookies'     => array()
        ) );

        if( is_wp_error( $paysol_request ) ) {
            return array(); // Bail early
        }

        $paysol_request_body = wp_remote_retrieve_body( $paysol_request );
        $paysol_request_data = json_decode( $paysol_request_body);

        if( $paysol_request_data->result !== 'success' ) {
            return array(); // Bail early
        }

        return $this->buildPaySolList($paysol_request_data->data);
    }

    private function buildPaySolList($data) {
        $map = array();

        foreach ($data as $item) {
            $map[$item->ID] = $item->NAME;
        }

        return $map;
    }

    private function get_mmb_gateway_paysol_args($token) {
        return array(
            "merchantId" => $this->gateway->api_merchant_id,
            "token" => $token
        );
    }

    private function get_mmb_gateway_paysol_token_args() {
        return array(
            'merchantId' => $this->gateway->api_merchant_id,
            'password' => $this->gateway->api_password,
            'allowOriginUrl' => get_site_url(),
            'action' => 'GET_AVAILABLE_PAYSOLS',
            'timestamp' => round(microtime(true) * 1000),
            'currency' => get_woocommerce_currency(),
            'country' => wc_get_base_location()['country'],
        );
    }
    /**
     * Do the EVO  payment refund process
     *
     * @since    1.0.0
     * @param    bool $sandbox
     * @param    WC_Order $order
     * @param    merchantTxId
     */
    public function do_evo_refund_process($sandbox = false,$order, $merchantTxId,$amount) {
        
        
        if ( $this->gateway->api_merchant_id === null || $this->gateway->api_merchant_id === ''
            || $this->gateway->api_password === null || $this->gateway->api_password === '' ) {
                return new WP_Error( 'invalid_order', 'miss merchant configuration info' );
            }
            
            $data = array();
            $data['merchantId'] = $this->gateway->api_merchant_id;
            $data['password'] = $this->gateway->api_password;
            $data['amount'] = $amount;
            $data['originalMerchantTxId'] = $merchantTxId;
            $data['allowOriginUrl'] = site_url();
            try {
                $payments = (new Payments\Payments())->environmentUrls($this->environment_params);
                $refund = $payments->refund();
                $refund->originalMerchantTxId($data['originalMerchantTxId'])->
                amount($data['amount'])->
                allowOriginUrl($data['allowOriginUrl']);
                $result = $refund->execute();
            } catch (Exception $e) {
                return new WP_Error( 'invalid_order', 'refund error: '.$e->getMessage());
            }
            if($result->result == 'success' && $result->status == 'SET_FOR_REFUND' ) {
                return true;
            }else if(!is_array($result->errors) && strpos($result->errors, 'Transaction not refundable: Original transaction not SUCCESS') !== false){
                //if the order was authorized + captured, the status in the Gateway system is still showing NOT_SET_FOR_CAPTURE, the refund can not be excuted
                return new WP_Error( 'invalid_order', 'The order is in capture process queue, can not refund now!' );
            }else{
                return new WP_Error( 'invalid_order', json_encode($result->errors) );
            }
    }
    //Do the payment capture process
    public function do_capture_process($sandbox = false,$order, $merchantTxId,$amount){
        if ( $this->gateway->api_merchant_id === null || $this->gateway->api_merchant_id === ''
            || $this->gateway->api_password === null || $this->gateway->api_password === '' ) {
                return new WP_Error( 'invalid_order', 'miss merchant configuration info' );
            }
            
            $data = array();
            $data['merchantId'] = $this->gateway->api_merchant_id;
            $data['password'] = $this->gateway->api_password;
            $data['amount'] = $amount;
            $data['originalMerchantTxId'] = $merchantTxId;
            $data['allowOriginUrl'] = site_url();
            
            try {
                $payments = (new Payments\Payments())->environmentUrls($this->environment_params);
                $capture = $payments->capture();
                $capture->originalMerchantTxId($data['originalMerchantTxId'])->
                amount($data['amount'])->
                allowOriginUrl($data['allowOriginUrl']);
                $result = $capture->execute();
            } catch (Exception $e) {
                $order->add_order_note( sprintf( __( 'Capture error!' )) );
                return;
            }
            
            if($result->result == 'success' && $result->status == 'SET_FOR_CAPTURE' ) {
                $order->add_order_note( sprintf( __( 'Capture charge complete (Amount: %s)' ), $amount ) );
                $order->update_meta_data( '_payment_status', 'completed' );
                $order->payment_complete();
                if($this->gateway->api_success_status == '1'){
                    $order->update_status( 'completed', sprintf( __( 'Card payment completed.', 'mmb-gateway-woocommerce' ) ) );
                }
                $order_id = $order->get_id();
                do_action( 'woocommerce_payment_complete', $order_id);
                $order->save();
                return true;
            }else{
                $order->add_order_note( sprintf( __( 'Capture error!' )) );
                return;
            }
    }
    //Do the payment VOID process
    public function do_void_process($sandbox = false,$order, $merchantTxId){
        if ( $this->gateway->api_merchant_id === null || $this->gateway->api_merchant_id === ''
            || $this->gateway->api_password === null || $this->gateway->api_password === '' ) {
                return new WP_Error( 'invalid_order', 'miss merchant configuration info' );
            }
            
            $data = array();
            $data['merchantId'] = $this->gateway->api_merchant_id;
            $data['password'] = $this->gateway->api_password;
            $data['originalMerchantTxId'] = $merchantTxId;
            $data['allowOriginUrl'] = site_url();
            
            
            try {
                $payments = (new Payments\Payments())->environmentUrls($this->environment_params);
                $void = $payments->void();
                $void->originalMerchantTxId($data['originalMerchantTxId'])->
                allowOriginUrl($data['allowOriginUrl']);
                $result = $void->execute();
            } catch (Exception $e) {
                $order->add_order_note( sprintf( __( 'Void error!' )) );
                return;
            }
            
            
            if($result->result == 'success' && $result->status == 'VOID' ) {
                $order->update_meta_data( '_payment_status', 'cancelled' );
                $order->update_status( 'cancelled', sprintf( __( 'Payment void complete', 'mmb-gateway-woocommerce' ) ) );
                $order->save();
                return true;
            }else{
                $order->add_order_note( sprintf( __( 'Void error!' )) );
                return;
            }
            
    }
    
}
