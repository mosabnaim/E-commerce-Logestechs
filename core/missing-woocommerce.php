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

        public function init() {
            if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
                add_action( 'admin_notices', [$this, 'show_wc_missing_notice'] );
                add_action('admin_enqueue_scripts', [$this, 'enqueue_dismiss_notice_script'] );
                add_action( 'wp_ajax_dismiss_wc_missing_notice', [$this, 'dismiss_wc_missing_notice'] );
            }
        }

        public function show_wc_missing_notice() {
            // Only show the notice if it hasn't been dismissed
            if ( get_option( 'wc_missing_notice_dismissed' ) != 1 ) {
                ?>
                <div class="error notice is-dismissible" id="wc-missing-notice">
                    <p> The Logestechs plugin requires WooCommerce to be active. Please activate WooCommerce for the Logestechs plugin to work properly. </p>
                </div>
                <?php
            }
        }

        public function enqueue_dismiss_notice_script() {
            wp_register_script( 'dismiss-notice-script',logestechs_asset('js/plugins-page.js'), [ 'jquery' ], '1.0', true );
            wp_localize_script( 'dismiss-notice-script', 'logestechs_global_data', [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'security' => wp_create_nonce( 'logestechs-security-nonce' )
            ] );
            wp_enqueue_script( 'dismiss-notice-script' );
        }

        public function dismiss_wc_missing_notice() {
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( 'You do not have permission to perform this action.' );
            }

            update_option( 'wc_missing_notice_dismissed', 1 );

            wp_die();
        }

    }

}
