<?php
/**
 * The file that handles Logestechs orders
 *
 * This file is used to handle Logestechs orders such as transferring orders, tracking orders, and cancelling orders.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/include/orders
 */

if ( ! class_exists( 'Logestechs_Popup_Handler' ) ) {

    class Logestechs_Popup_Handler {
        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {
            add_action( 'admin_footer', [$this, 'render_popups'] );
            add_action( 'wp_ajax_logestechs_save_company', [$this, 'save_company'] );
            add_action( 'wp_ajax_logestechs_delete_company', [$this, 'delete_company'] );
            add_action( 'wp_ajax_logestechs_fetch_companies', [$this, 'fetch_companies'] );
        }

        public function render_popups() {
            // Get the current screen's information
            $screen = get_current_screen();
            $slug   = Logestechs_Config::MENU_SLUG;

            // Ensure that we're dealing with a WooCommerce order
            if ( 'shop_order' == $screen->post_type || 'edit-shop_order' === $screen->id || 'shop_order' === $screen->id || 'toplevel_page_logestechs' === $screen->id ) {
                // Here we could fetch data if needed and pass it to the View
                $transfer_popup = new Logestechs_Manage_Companies_Popup_View();
                if ( 'toplevel_page_' . $slug === $screen->id ) {
                    $transfer_popup->render();
                } else {
                    $transfer_popup->render( false );
                }
                $tracking_popup = new Logestechs_Tracking_Details_Popup_View();
                $tracking_popup->render();
            }
        }

        public function save_company() {
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( ['message' => 'You do not have permission to perform this action.'] );
                wp_die();
            }

            // Sanitize POST data
            $security_manager = new Logestechs_Security_Manager();
            $sanitizer        = $security_manager->get_sanitizer();
            $validator        = $security_manager->get_validator();

            $credentials = $sanitizer->sanitize_credentials( $_POST );
            $errors      = $validator->validate_credentials( $credentials );
            if ( ! empty( $errors ) ) {
                wp_send_json_error( ['errors' => $errors] );
                wp_die();
            }
            // If success, let's store the company details in our database.
            $credentials_manager = new Logestechs_Credentials_Manager();
            $response            = $credentials_manager->save_credentials( $credentials );

            if ( is_wp_error( $response ) ) {
                wp_send_json_error( ['message' => 'Error occurred!'] );
                wp_die();
            }

            // Send the company data back to the client.
            wp_send_json_success( $response );
            wp_die();
        }

        public function delete_company() {
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( 'You do not have permission to perform this action.' );
            }

            // Connect to Logestechs API and perform delete.
            // Assume $api to be an instance of your API handler class.
            $company_id = $_POST['company_id'] ? intval( $_POST['company_id'] ) : null;
            if ( ! $company_id ) {
                wp_send_json_error( 'Error while deleting this item!' );
                wp_die();

            }

            $credentials_manager = new Logestechs_Credentials_Manager();
            $response            = $credentials_manager->delete_credentials( $company_id );

            if ( is_wp_error( $response ) ) {
                wp_send_json_error( 'Error while deleting this item!' );
                wp_die();
            }

            wp_send_json_success();

            wp_die();
        }

        public function fetch_companies() {
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( 'You do not have permission to perform this action.' );
            }

            $credentials_manager = new Logestechs_Credentials_Manager();
            $companies           = $credentials_manager->fetch_companies();

            wp_send_json_success( $companies );

            wp_die();
        }

    }

}
