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

if (!class_exists('Logestechs_Plugin_Core')) {

    class Logestechs_Plugin_Core {

        private $classes_to_load = array(
            "admin" => array(
                "Logestechs_Admin_Menu",
                "Logestechs_Admin_Page",
                "Logestechs_Enqueue",
            ),
            "api" => array(
                "Logestechs_Api_Error_Handler",
                "Logestechs_Api",
            ),
            "include" => array(
                "Logestechs_Error_Handler",
            ),
            "include/credentials" => array(
                "Logestechs_Credentials_Manager",
                "Logestechs_Credentials_Storage",
            ),
            "include/orders" => array(
                "Logestechs_Order_Handler",
                "Logestechs_Order_Metabox",
                "Logestechs_Order_Tracker",
            ),
            "security" => array(
                "Logestechs_Data_Encryption",
                "Logestechs_Data_Validator",
                "Logestechs_Input_Sanitizer",
                "Logestechs_Security_Manager",
            ),
            "views" => array(
                "Logestechs_Admin_Page_View",
                "Logestechs_Order_Transfer_Popup_View",
                "Logestechs_Tracking_Details_Popup_View",
                "Logestechs_Woocommerce_List_View",
            ),
        );

        /**
         * Define the core functionality of the plugin.
         *
         * @since    1.0.0
         */
        public function __construct() {
            // Code that runs during plugin initialization
        }

        /**
         * Runs the plugin.
         *
         * @since    1.0.0
         */
        public function run() {
            // This is where you can hook into WordPress actions and filters,
            // initialize your classes, or run any code needed for your plugin.

            // function to automatically include class files as they're needed.
            spl_autoload_register(array($this, 'autoload'));

            // Example: Load dependencies, define locale, and hook into actions and filters
            // $this->load_dependencies();
            // $this->set_locale();
            // $this->define_admin_hooks();
            // $this->define_public_hooks();
        }

        /**
         * Loads required files and dependencies for the plugin
         *
         * @since    1.0.0
         */
        private function load_dependencies() {
            // Require any files or classes that are needed for your plugin.
            // require_once(LOGESTECHS_PLUGIN_PATH . 'include/some-file.php');
        }

        /**
         * Define the locale for this plugin for internationalization.
         *
         * @since    1.0.0
         */
        private function set_locale() {
            // Load text domain for localization
            // load_plugin_textdomain('logestechs', false, LOGESTECHS_PLUGIN_PATH . '/languages/');
        }

        /**
         * Register all of the hooks related to the admin area functionality
         * of the plugin.
         *
         * @since    1.0.0
         */
        private function define_admin_hooks() {
            // Hook into WordPress actions and filters here for the admin area
            // Example: add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');
        }

        /**
         * Register all of the hooks related to the public-facing functionality
         *
         * @since    1.0.0
         */
        private function define_public_hooks() {
            // Hook into WordPress actions and filters here for the public-facing part of the site
            // Example: add_action('wp_enqueue_scripts', 'enqueue_public_scripts');
        }

        public function autoload($class_name) {
            foreach ($this->classes_to_load as $folder => $classes) {
                if (in_array($class_name, $classes)) {
                    require_once plugin_dir_path(__FILE__) . "../$folder/$class_name.php";
                    break;
                }
            }
        }

    }

}