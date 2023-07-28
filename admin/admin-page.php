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

if (!class_exists('Logestechs_Admin_Page')) {

    class Logestechs_Admin_Page {

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {
            add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
        }

        /**
         * Register the administration menu for the plugin into the WordPress Dashboard menu.
         *
         * @since    1.0.0
         */
        public function add_plugin_admin_menu() {
            // Add a settings page for this plugin to the Settings menu
            // $this->plugin_screen_hook_suffix = add_options_page(
            //    __('Logestechs Settings', 'logestechs'),
            //    __('Logestechs', 'logestechs'),
            //    'manage_options',
            //    'logestechs',
            //    array($this, 'display_plugin_admin_page')
            //);
        }

        /**
         * Render the settings page for this plugin.
         *
         * @since    1.0.0
         */
        public function display_plugin_admin_page() {
            // Include the view for this page
            // require_once(LOGESTECHS_PLUGIN_PATH . 'views/admin-page-view.php');
        }
        
    }

}
