<?php

/**
 * Plugin Name: WooCommerce EVOMexico
 * Description: WooCommerce EVOMexico gateway integration.
 * Version: 1.1.0
 */

/**
 * Abort if the file is called directly
 */
if (!defined('WPINC')) {
    exit;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mmb-gateway-woocommerce-activator.php
 */
function activate_mmb_gateway_woocommerce()
{
    require_once plugin_dir_path(__FILE__) . 'classes/class-mmb-gateway-woocommerce-activator.php';
    MMB_Gateway_Woocommerce_Activator::activate();
}

register_activation_hook(__FILE__, 'activate_mmb_gateway_woocommerce');


/**
 * Run the plugin after all plugins are loaded
 */
add_action('plugins_loaded', 'init_mmb_gateway', 0);
function init_mmb_gateway()
{
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }
    /**
     * The core plugin class that is used to define internationalization and
     * admin-specific hooks
     */
    require plugin_dir_path(__FILE__) . 'classes/class-mmb-gateway-woocommerce.php';

    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    function run_mmb_gateway_woocommerce()
    {
        $plugin = new MMB_Gateway_Woocommerce();
        $plugin->run();
    }

    run_mmb_gateway_woocommerce();
}

// Add custom action links
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mmb_gateway_action_links' );
function mmb_gateway_action_links( $links ) {
    $plugin_links = array(
        '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=evomexico' ) . '">' . __( 'Settings', 'evomexico' ) . '</a>',
    );
    return array_merge( $plugin_links, $links );
}
