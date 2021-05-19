<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 *
 * @package    MMB_Gateway_Woocommerce
 * @subpackage MMB_Gateway_Woocommerce/includes
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    MMB_Gateway_Woocommerce
 * @subpackage MMB_Gateway_Woocommerce/includes
 */
class MMB_Gateway_Woocommerce_i18n
{


    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            'mmb-gateway-woocommerce',
            false,
            dirname(plugin_basename(__DIR__)) . '/languages/'
        );

    }


}
