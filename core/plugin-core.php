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
            // Example: Load dependencies, define locale, and hook into actions and filters
            // $this->load_dependencies();
            // $this->set_locale();
            // $this->define_admin_hooks();
            // $this->define_public_hooks();
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

    }

}
