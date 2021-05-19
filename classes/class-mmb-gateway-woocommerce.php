<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
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
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    MMB_Gateway_Woocommerce
 * @subpackage MMB_Gateway_Woocommerce/includes
 */
class MMB_Gateway_Woocommerce {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      MMB_Gateway_Woocommerce_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->plugin_name = 'mmb-gateway-woocommerce';
        $this->version = '1.4.0';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - MMB_Gateway_Woocommerce_Loader. Orchestrates the hooks of the plugin.
     * - MMB_Gateway_Woocommerce_i18n. Defines internationalization functionality.
     * - MMB_Gateway_Woocommerce_Admin. Defines all hooks for the admin area.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'classes/class-mmb-gateway-woocommerce-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'classes/class-mmb-gateway-woocommerce-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-mmb-gateway.php';

        $this->loader = new MMB_Gateway_Woocommerce_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the MMB_Gateway_Woocommerce_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new MMB_Gateway_Woocommerce_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new EVOMexico();

        $this->loader->add_filter('woocommerce_payment_gateways', $plugin_admin, 'add_new_gateway');
        add_action('woocommerce_update_options_payment_gateways_' . $plugin_admin->id, array($plugin_admin, 'process_admin_options'));
        add_action('woocommerce_receipt_evomexico', array($plugin_admin, 'receipt_page'));
        add_action('wp', array($plugin_admin, 'mmb_response_handler'));
        add_action('wp', array($plugin_admin, 'set_wc_notice'));
        add_action( 'woocommerce_admin_order_data_after_shipping_address', array($plugin_admin, 'show_evo_transaction_id') );
        add_action( 'woocommerce_api_evomexico', array($plugin_admin, 'handle_call_back') );
        //         add_action( 'woocommerce_order_status_on-hold_to_processing', array( $plugin_admin, 'capture_payment' ) );
        //         add_action( 'woocommerce_order_status_on-hold_to_completed', array( $plugin_admin, 'capture_payment' ) );
        //         add_action( 'woocommerce_order_status_on-hold_to_cancelled', array( $this, 'cancel_payment' ) );
        //         add_action( 'woocommerce_order_status_on-hold_to_refunded', array( $this, 'cancel_payment' ) );
        
        add_filter( 'woocommerce_order_actions', array( $plugin_admin, 'add_capture_charge_order_action' ) );//add the Capture Online Order actions
        add_action( 'woocommerce_order_action_evomexico_capture_charge', array( $plugin_admin, 'maybe_capture_charge' ) );
        add_filter( 'woocommerce_order_actions', array( $plugin_admin, 'add_void_charge_order_action' ) );//add the VOID Online Order actions
        add_action( 'woocommerce_order_action_evomexico_void_charge', array( $plugin_admin, 'maybe_void_charge' ) );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    IMMB_Gateway_Woocommerce_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
