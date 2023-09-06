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
            $order_handler = new Logestechs_Order_Handler();
            $statuses = $order_handler->get_unique_order_statuses();

            $is_logged_in = false;
            if( Logestechs_Config::COMPANY_DOMAIN ) {
                $db = Logestechs_Credentials_Storage::get_instance();
                $first_record = $db->get_first_record();
                if($first_record) {
                    $is_logged_in = true;
                    $email = $first_record->email;
                }
            }
            
            $logestechs_page = new Logestechs_Admin_Page_View();
            $logestechs_page->render([
                'statuses' => $statuses,
                'is_logged_in' => $is_logged_in,
                'email' => $email ?? null
            ]);
        }
    }

}
