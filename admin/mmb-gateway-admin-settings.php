<?php

/**
 * Provides settings inputs for admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 *
 * @package    MMB_Gateway_Woocommerce
 * @subpackage MMB_Gateway_Woocommerce/admin
 */
if (!defined('ABSPATH')) {
    exit;
}
/*Define the control parameter value to determine whether the LOG functionality show or not */
$show_log_field = '1';
/*Define the control parameter value to determine whether the AUTH functionality show or not */
$show_auth_fields = '1';
/*Define the control parameter value to determine whether the UI fields show or not */
$show_url_fields_sandbox = '0';
$show_url_fields_live = '0';
//Define the $integration_modes,specifies whether integration mode should be shown or not, 1 means to show, 0 means not

$integration_show_iframe = '1';
$integration_show_redirect = '1';
$integration_show_hostedpay = '1';

$api_test_token_url = 'https://apiuat.test.evomexico.mx/token';
$api_test_payments_url = 'https://apiuat.test.evomexico.mx/payments';
$api_test_js_url = 'https://cashierui-apiuat.test.evomexico.mx/js/api.js';
$api_test_cashier_url = 'https://cashierui-apiuat.test.evomexico.mx/ui/cashier';
$api_token_url = 'https://api.evomexico.mx/token';
$api_payments_url = 'https://api.evomexico.mx/payments';
$api_js_url = 'https://cashierui-api.evomexico.mx/js/api.js';
$api_cashier_url = 'https://cashierui-api.evomexico.mx/ui/cashier';

/*
 * The index of the mode:
 * iframe:0
 * redirect:1
 * hostedpay: 2
 */
//specifies the default index of integration mode 
$default_integration_mode = '2';
// $brand = 'EVOMexico';


$admin_fields_array = array();
$admin_fields_array['enabled'] = array(
    'title' => __('Enable/Disable', 'mmb-gateway-woocommerce'),
    'type' => 'checkbox',
    'label' => __('Enable EVOMexico Gateway', 'mmb-gateway-woocommerce'),
    'description' => __('Enable or disable the gateway.', 'mmb-gateway-woocommerce'),
    'desc_tip' => false,
    'default' => 'yes'
);
$admin_fields_array['title'] =  array(
    'title' => __('Title', 'mmb-gateway-woocommerce'),
    'type' => 'text',
    'description' => __('This controls the title which the user sees during checkout.', 'mmb-gateway-woocommerce'),
    'desc_tip' => true,
    'default' => __('EVOMexico', 'mmb-gateway-woocommerce')
);

$admin_fields_array['description'] =  array(
    'title' => __('Description', 'mmb-gateway-woocommerce'),
    'type' => 'textarea',
    'description' => __('This controls the description which the user sees during checkout.', 'mmb-gateway-woocommerce'),
    'default' => __("Pay via EVOMexico", 'mmb-gateway-woocommerce')
);

$admin_fields_array['testmode'] =  array(
    'title' => __('EVOMexico Test Mode', 'mmb-gateway-woocommerce'),
    'type' => 'checkbox',
    'label' => __('Enable Test Mode', 'mmb-gateway-woocommerce'),
    'description' => __('Enable or disable the test mode for the gateway to test the payment method.', 'mmb-gateway-woocommerce'),
    'desc_tip' => false,
    'default' => 'yes'
);
$admin_fields_array['advanced'] =  array(
    'title' => __('Advanced options', 'mmb-gateway-woocommerce'),
    'type' => 'title',
    'description' => '',
);
$admin_fields_array['api_merchant'] = array(
    'title' => __('Merchant data', 'mmb-gateway-woocommerce'),
    'type' => 'title',
    'description' => __('In this section You can set up your merchant data for EVOMexico system.', 'mmb-gateway-woocommerce')
);
$admin_fields_array['api_merchant_id'] = array(
    'title' => __('Merchant ID', 'mmb-gateway-woocommerce'),
    'type' => 'text',
    'description' => '',
    'desc_tip' => false,
    'default' => ''
);
$admin_fields_array['api_password'] = array(
    'title' => __('Password', 'mmb-gateway-woocommerce'),
    'type' => 'password',
    'description' => '',
    'desc_tip' => false,
    'default' => ''
);
$admin_fields_array['api_brand_id'] = array(
    'title' => __('Brand ID', 'mmb-gateway-woocommerce'),
    'type' => 'text',
    'description' => '',
    'desc_tip' => false,
    'default' => ''
);
if($integration_show_iframe || $integration_show_redirect || $integration_show_hostedpay){
    $admin_fields_array['api_payment_modes'] = array(
        'title' => __('Payment mode', 'mmb-gateway-woocommerce'),
        'type' => 'select',
        'description' => '',
        'desc_tip' => false,
        'default' => $default_integration_mode
    );
    $admin_fields_array['api_payment_modes']['options'] = array();
    if($integration_show_iframe){
        $option =  __('iframe', 'mmb-gateway-woocommerce');
        $admin_fields_array['api_payment_modes']['options'][0] = $option;
    }
    if($integration_show_iframe){
        $option =  __('redirect', 'mmb-gateway-woocommerce');
        $admin_fields_array['api_payment_modes']['options'][1] = $option;
    }
    if($integration_show_iframe){
        $option =  __('hostedPayPage', 'mmb-gateway-woocommerce');
        $admin_fields_array['api_payment_modes']['options'][2] = $option;
    }
}


if($show_auth_fields){
    //Purchase or Auth
    $admin_fields_array['api_payment_action'] = array(
        'title' => __('Payment action', 'mmb-gateway-woocommerce'),
        'type' => 'select',
        'description' => '',
        'desc_tip' => false,
        'default' => 0
    );
    $admin_fields_array['api_payment_action']['options'] = array();
    $option =  __('PURCHASE', 'mmb-gateway-woocommerce');
    $admin_fields_array['api_payment_action']['options'][0] = $option;
    $option =  __('AUTH', 'mmb-gateway-woocommerce');
    $admin_fields_array['api_payment_action']['options'][1] = $option;
}

if($show_url_fields_sandbox){
    $admin_fields_array['api_urls'] = array(
        'title' => __('URLs', 'mmb-gateway-woocommerce'),
        'type' => 'title',
        'description' => __('In this section You can set up EVOMexico system URLs.', 'mmb-gateway-woocommerce')
    );
    $admin_fields_array['api_test_urls'] = array(
        'title' => __('Test URLs', 'mmb-gateway-woocommerce'),
        'type' => 'title',
        'description' => __('In this section You can set up EVOMexico test system URLs.', 'mmb-gateway-woocommerce')
    );
    $admin_fields_array['api_test_token_url'] = array(
        'title' => __('Test Token URL', 'mmb-gateway-woocommerce'),
        'type' => 'text',
        'description' => '',
        'desc_tip' => false,
        'default' => $api_test_token_url
    );
    $admin_fields_array['api_test_payments_url'] = array(
        'title' => __('Test Payments URL', 'mmb-gateway-woocommerce'),
        'type' => 'text',
        'description' => '',
        'desc_tip' => false,
        'default' => $api_test_payments_url
    );
    $admin_fields_array['api_test_js_url'] = array(
        'title' => __('Test JavaScript URL', 'mmb-gateway-woocommerce'),
        'type' => 'text',
        'description' => '',
        'desc_tip' => false,
        'default' => $api_test_js_url
    );
    $admin_fields_array['api_test_cashier_url'] = array(
        'title' => __('Test Cashier URL', 'mmb-gateway-woocommerce'),
        'type' => 'text',
        'description' => '',
        'desc_tip' => false,
        'default' => $api_test_cashier_url
    );
}
if($show_url_fields_live){
    $admin_fields_array['api_system_urls'] = array(
        'title' => __('URLs', 'mmb-gateway-woocommerce'),
        'type' => 'title',
        'description' => __('In this section You can set up EVOMexico system URLs.', 'mmb-gateway-woocommerce')
    );
    $admin_fields_array['api_token_url'] = array(
        'title' => __('Token URL', 'mmb-gateway-woocommerce'),
        'type' => 'text',
        'description' => '',
        'desc_tip' => false,
        'default' => $api_token_url
    );
    $admin_fields_array['api_payments_url'] = array(
        'title' => __('Payments URL', 'mmb-gateway-woocommerce'),
        'type' => 'text',
        'description' => '',
        'desc_tip' => false,
        'default' => $api_payments_url
    );
    $admin_fields_array['api_js_url'] = array(
        'title' => __('JavaScript URL', 'mmb-gateway-woocommerce'),
        'type' => 'text',
        'description' => '',
        'desc_tip' => false,
        'default' => $api_js_url
    );
    $admin_fields_array['api_cashier_url'] = array(
        'title' => __('Cashier URL', 'mmb-gateway-woocommerce'),
        'type' => 'text',
        'description' => '',
        'desc_tip' => false,
        'default' => $api_cashier_url
    );
}
//choose the default paid order status
$admin_fields_array['api_success_status'] = array(
    'title' => __('Success Status', 'mmb-gateway-woocommerce'),
    'type' => 'select',
    'description' => '',
    'desc_tip' => false,
    'default' => 0
);
$admin_fields_array['api_success_status']['options'] = array();
$option = 'processing';
$admin_fields_array['api_success_status']['options'][0] = $option;
$option = 'completed';
$admin_fields_array['api_success_status']['options'][1] = $option;

if($show_log_field){
    $admin_fields_array['log_mode'] =  array(
        'title' => __('Logging', 'mmb-gateway-woocommerce'),
        'type' => 'checkbox',
        'label' => __('Enable Log Debug', 'mmb-gateway-woocommerce'),
        'description' => __('Log payment events, such as gateway transaction callback, if enabled, log file will be found inside: wp-content/uploads/wc-logs', 'mmb-gateway-woocommerce'),
        'desc_tip' => false,
        'default' => 'no'
    );
}


return $admin_fields_array;
