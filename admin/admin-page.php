<?php
/**
 * The file that defines the plugin's admin page
 *
 * This is used to add a new page under the admin menu in WordPress and to define
 * the form for the plugin settings.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/admin
 */

if ( ! class_exists( 'Logestechs_Admin_Page' ) ) {

    class Logestechs_Admin_Page {
        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {
            add_action( 'admin_menu', [$this, 'create_plugin_menu'] );
        }

        /**
         * Creates the plugin's admin menu item.
         *
         * @since    1.0.0
         */
        public function create_plugin_menu() {
            // Creates a new top-level menu section
            add_menu_page(
                Logestechs_Config::PLUGIN_NAME, // page title
                Logestechs_Config::MENU_TITLE,  // menu title
                'manage_options',               // capability
                Logestechs_Config::MENU_SLUG,   // menu slug
                [$this, 'render_page'],
                Logestechs_Config::PLUGIN_ICON, // menu icon
                55                              // position
            );
        }

        /**
         * Display the HTML output of the admin menu page.
         *
         * @since    1.0.0
         */
        public function render_page() {
            // Check user capabilities
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( __( 'You do not have sufficient permissions to access this page.', 'logestechs' ) );
            }

            $logestechs_page = new Logestechs_Admin_Page_View();
            $logestechs_page->render();
        }
    }

}
