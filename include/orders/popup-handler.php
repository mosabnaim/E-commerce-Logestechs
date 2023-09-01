<?php
/**
 * The file that handles Logestechs orders.
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

        /**
         * Render popups.
         *
         * @since    1.0.0
         */
        public function render_popups() {
            $screen = get_current_screen();
            $slug   = Logestechs_Config::MENU_SLUG;

            if ( 'shop_order' == $screen->post_type || 'edit-shop_order' === $screen->id || 'shop_order' === $screen->id || 'toplevel_page_logestechs' === $screen->id ) {
                $transfer_popup = new Logestechs_Manage_Companies_Popup_View();
                if ( 'toplevel_page_' . $slug === $screen->id ) {
                    $transfer_popup->render();
                    $transfer_popup->render( false );
                } else {
                    $transfer_popup->render( false );
                }
                $tracking_popup = new Logestechs_Tracking_Details_Popup_View();
                $tracking_popup->render();
            }
        }

        /**
         * Save company credentials.
         *
         * @since    1.0.0
         */
        public function save_company() {
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( ['message' => __('You do not have permission to perform this action.', 'logestechs')] );
                wp_die();
            }

            $security_manager = new Logestechs_Security_Manager();
            $sanitizer        = $security_manager->get_sanitizer();
            $validator        = $security_manager->get_validator();

            $credentials = $sanitizer->sanitize_credentials( $_POST );
            $errors      = $validator->validate_credentials( $credentials );

            if ( ! empty( $errors ) ) {
                wp_send_json_error( ['errors' => $errors] );
                wp_die();
            }

            $credentials_manager = new Logestechs_Credentials_Manager();
            $response            = $credentials_manager->save_credentials( $credentials );

            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                wp_send_json_error( array('errors' => [$error_message]) );
                wp_die();
            }

            wp_send_json_success( $response );
            wp_die();
        }

        /**
         * Delete a company's credentials.
         *
         * @since    1.0.0
         */
        public function delete_company() {
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( __('You do not have permission to perform this action.', 'logestechs') );
            }

            $company_id = $_POST['company_id'] ? intval( $_POST['company_id'] ) : null;
            if ( ! $company_id ) {
                wp_send_json_error( __('Error while deleting this item!', 'logestechs') );
                wp_die();
            }
            $completed_statuses = Logestechs_Config::COMPLETED_STATUS;

            $credentials_manager = new Logestechs_Credentials_Manager();
            $args = [
                'post_type'      => 'shop_order',
                'meta_query'     => [
                    [
                        'key'     => '_logestechs_local_company_id',
                        'value'   => $company_id,
                        'compare' => '='
                    ],
                    [
                        'key'     => '_logestechs_order_status',
                        'value'   => $completed_statuses,
                        'compare' => 'NOT IN'
                    ]
                ],
                'post_status'    => 'any',
                'posts_per_page' => 1
            ];
            $query = new WP_Query( $args );
            if ( $query->have_posts() ) {
                wp_send_json_error( __('Cannot delete this company as it is associated with existing orders!', 'logestechs') );
                wp_die();
            }

            $response = $credentials_manager->delete_credentials( $company_id );

            if ( is_wp_error( $response ) ) {
                wp_send_json_error( __('Error while deleting this item!', 'logestechs') );
                wp_die();
            }

            wp_send_json_success();
            wp_die();
        }

        /**
         * Fetches company credentials.
         *
         * @since    1.0.0
         */
        public function fetch_companies() {
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( __('You do not have permission to perform this action.', 'logestechs') );
            }

            $credentials_manager = new Logestechs_Credentials_Manager();
            $companies           = $credentials_manager->fetch_companies();

            wp_send_json_success( $companies );

            wp_die();
        }
    }
}
