<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link
 * @since      1.0.0
 *
 * @package    MMB_Woocommerce
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}
delete_option('woocommerce_evomexico_settings');