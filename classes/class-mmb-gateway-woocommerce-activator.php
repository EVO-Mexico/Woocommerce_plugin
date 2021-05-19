<?php

/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 *
 * @package    MMB_Gateway_Woocommerce
 * @subpackage IMMB_Gateway_Woocommerce/includes
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    MMB_Gateway_Woocommerce
 * @subpackage MMB_Gateway_Woocommerce/includes
 */
class MMB_Gateway_Woocommerce_Activator
{

    /**
     * Check for Woocommerce version on activation
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        if (!class_exists('woocommerce')) {
            deactivate_plugins(plugin_basename(__FILE__));

            wp_die(__('EVOMexico Gateway for Woocommerce requires Woocommerce version 2.1 or higher', 'mmb-gateway-woocommerce'), __('Plugin Activation Error', 'mmb-gateway-woocommerce'), array('response' => 200, 'back_link' => TRUE));

        }
        if (version_compare(WC()->version, "2.2", '<')) {
            deactivate_plugins(plugin_basename(__FILE__));

            wp_die(__('EVOMexico Gateway for Woocommerce requires Woocommerce version 2.1 or higher', 'mmb-gateway-woocommerce'), __('Plugin Activation Error', 'mmb-gateway-woocommerce'), array('response' => 200, 'back_link' => TRUE));
        }
    }

}
