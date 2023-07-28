<?php
/**
 * The file that handles the creation of the admin menu
 *
 * This is used to create an admin menu item for the plugin.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/admin
 */

if (!class_exists('Logestechs_Admin_Menu')) {

    class Logestechs_Admin_Menu {

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {
            add_action('admin_menu', array($this, 'create_plugin_menu'));
        }

        /**
         * Creates the plugin's admin menu item.
         *
         * @since    1.0.0
         */
        public function create_plugin_menu() {
            // Creates a new top-level menu section
            // add_menu_page(
            //    __('Logestechs Settings', 'logestechs'), 
            //    __('Logestechs', 'logestechs'), 
            //    'manage_options', 
            //    'logestechs', 
            //    array($this, 'display_plugin_settings_page'), 
            //    'dashicons-cart'
            //);
        }

        /**
         * Display the HTML output of the admin menu page.
         *
         * @since    1.0.0
         */
        public function display_plugin_settings_page() {
            // Include the view for this page
            // require_once(LOGESTECHS_PLUGIN_PATH . 'views/admin-page-view.php');
        }

    }

}
