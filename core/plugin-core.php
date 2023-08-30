<?php
/**
 * The file that defines the core plugin class
 *
 * This is used to define internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/core
 */
if ( ! class_exists( 'Logestechs_Plugin_Core' ) ) {

    class Logestechs_Plugin_Core {

        /**
         * Define the core functionality of the plugin.
         *
         * @since    1.0.0
         */
        public function __construct() {}

        /**
         * Register the init action.
         *
         * @since    1.0.0
         */
        public function run() {
            add_action( 'init', [$this, 'init'] );
        }

        /**
         * Initializes the plugin, loads dependencies and sets locale if user can manage options.
         *
         * @since    1.0.0
         */
        public function init() {
            if ( current_user_can( 'manage_options' ) ) {
                // Load dependencies
                $this->load_dependencies();
                // Set locale for internationalization
                $this->set_locale();
            }
        }

        /**
         * Load the required dependencies for this plugin.
         *
         * @since    1.0.0
         */
        private function load_dependencies() {
            new Logestechs_Enqueue();
            new Logestechs_Admin_Page();
            new Logestechs_Popup_Handler();
            new Logestechs_Order_Metabox();
            new Logestechs_Order_Handler();

            $check_for_woocommerce = new Logestechs_Missing_Woocommerce();
            $check_for_woocommerce->init();

            $logestechs_error_handler = new Logestechs_Api_Error_Handler();
            $logestechs_error_handler->init();
        }

        /**
         * Define the locale for this plugin for internationalization.
         *
         * @since    1.0.0
         */
        private function set_locale() {
            // Load text domain for localization
            load_plugin_textdomain( 'logestechs', false, LOGESTECHS_PLUGIN_BASENAME . '/languages/' );
        }
    }

}
