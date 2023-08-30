<?php
/**
 * The file that defines the core plugin class for handling missing WooCommerce.
 * This class takes care of showing an admin notice if WooCommerce is not active,
 * and also handles dismissing the notice.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/core
 */

if ( ! class_exists( 'Logestechs_Missing_Woocommerce' ) ) {

    class Logestechs_Missing_Woocommerce {

        /**
         * Define the core functionality of the plugin.
         *
         * @since    1.0.0
         */
        public function __construct() {
            // Code that runs during plugin initialization
        }

        /**
         * Initialization function to check WooCommerce availability and set up hooks.
         *
         * @since    1.0.0
         */
        public function init() {
            if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
                add_action( 'admin_notices', [$this, 'show_wc_missing_notice'] );
                add_action( 'admin_enqueue_scripts', [$this, 'enqueue_dismiss_notice_script'] );
                add_action( 'wp_ajax_dismiss_wc_missing_notice', [$this, 'dismiss_wc_missing_notice'] );
            }
        }

        /**
         * Display an admin notice if WooCommerce is missing.
         *
         * @since    1.0.0
         */
        public function show_wc_missing_notice() {
            // Only show the notice if it hasn't been dismissed
            if ( get_option( 'wc_missing_notice_dismissed' ) != 1 ) {
                ?>
                <div class="error notice is-dismissible" id="wc-missing-notice">
                    <p><?php _e( 'The Logestechs plugin requires WooCommerce to be active. Please activate WooCommerce for the Logestechs plugin to work properly.', 'logestechs' );?></p>
                </div>
                <?php
}
        }

        /**
         * Enqueue script for handling notice dismissal.
         *
         * @since    1.0.0
         */
        public function enqueue_dismiss_notice_script() {
            wp_register_script( 'dismiss-notice-script', logestechs_asset( 'js/plugins-page.js' ), ['jquery'], '1.0', true );
            wp_localize_script( 'dismiss-notice-script', 'logestechs_global_data', [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'security' => wp_create_nonce( 'logestechs-security-nonce' )
            ] );
            wp_enqueue_script( 'dismiss-notice-script' );
        }

        /**
         * AJAX handler for dismissing the WooCommerce missing notice.
         *
         * @since    1.0.0
         */
        public function dismiss_wc_missing_notice() {
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( __( 'You do not have permission to perform this action.', 'logestechs' ) );
            }

            update_option( 'wc_missing_notice_dismissed', 1 );
            wp_die();
        }

    }

}