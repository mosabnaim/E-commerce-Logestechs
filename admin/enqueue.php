<?php

/**
 * The file that handles enqueuing of styles and scripts for the plugin
 *
 * This class manages all the JavaScript and CSS that's needed for the
 * plugin's admin section, including the dashboard and WooCommerce Order page.
 * It takes care of enqueuing all scripts and styles and also handles dynamic
 * CSS generation based on configuration values.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/admin
 */

if ( ! class_exists( 'Logestechs_Enqueue' ) ) {

    class Logestechs_Enqueue {

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {
            add_action( 'admin_enqueue_scripts', [$this, 'enqueue_assets'] );
        }

        /**
         * Register and enqueue the stylesheets and scripts for the admin area.
         *
         * @since    1.0.0
         */
        public function enqueue_assets( $hook ) {
            global $post_type;
            $slug = Logestechs_Config::MENU_SLUG;

            if ( ! $this->is_valid_hook( $hook, $post_type ) ) {
                return; // Return early if not on the target pages.
            }

            $script_array = $this->get_script_data(); // Common script data

            $this->enqueue_common_assets(); // Enqueue common styles and scripts

            // Enqueue specific assets based on the current page.
            if ( $hook == 'toplevel_page_' . $slug ) {
                $this->enqueue_logestechs_page_assets(); // Assets for toplevel page
            }

            if ( 'post.php' === $hook && 'shop_order' === $post_type ) {
                $this->enqueue_woocommerce_page_assets(); // Assets for WooCommerce Order page
            }

            $this->localize_scripts( $script_array ); // Localize scripts with common data
        }

        /**
         * Validate if the current hook should execute specific actions.
         *
         * @since    1.0.0
         *
         * @param string $hook      The current page hook.
         * @param string $post_type The current post type.
         *
         * @return bool Returns true if the hook is valid, false otherwise.
         */
        private function is_valid_hook( $hook, $post_type ) {
            $slug = Logestechs_Config::MENU_SLUG;
            if ( ! in_array( $hook, ['toplevel_page_' . $slug, $slug . '_page_settings', 'edit.php', 'post.php', 'post-new.php'] ) ) {
                return false;
            }

            if ( in_array( $hook, ['edit.php', 'post.php', 'post-new.php'] ) && ! in_array( $post_type, ['shop_order', $_GET['post_type'] ?? null, $_POST['post_type'] ?? null] ) ) {
                return false;
            }

            return true;
        }

        /**
         * Enqueue common styles and scripts used across admin pages.
         *
         * @since    1.0.0
         */
        private function enqueue_common_assets() {
            wp_enqueue_style( 'logestechs-style', logestechs_asset( 'css/style.css' ), [], '1.0', 'all' );
            wp_enqueue_script( 'logestechs-admin', logestechs_asset( 'js/main.js' ), ['jquery'], null, true );
            wp_enqueue_script( 'sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', [], null, true );
            wp_enqueue_script( 'rive', 'https://unpkg.com/@rive-app/canvas@2.1.0', [], null, true );
        }

        /**
         * Enqueue styles and scripts specific to Logestechs main page.
         *
         * @since    1.0.0
         */
        private function enqueue_logestechs_page_assets() {
            wp_enqueue_script( 'daterangepicker_moment', logestechs_asset( 'js/rangepicker/moment.min.js' ), ['jquery'], null, true );
            wp_enqueue_style( 'daterangepicker_style', logestechs_asset( 'js/rangepicker/daterangepicker.css' ) );
            wp_enqueue_script( 'daterangepicker_script', logestechs_asset( 'js/rangepicker/daterangepicker.js' ), ['jquery'], null, true );
            wp_enqueue_style( 'logestechs-page-style', logestechs_asset( 'css/logestechs-page.css' ), [], '1.0', 'all' );
            wp_enqueue_script( 'logestechs-page-script', logestechs_asset( 'js/logestechs-page.js' ), ['jquery'], null, true );
        }

        /**
         * Enqueue styles and scripts specific to the WooCommerce Order page.
         *
         * @since    1.0.0
         */
        private function enqueue_woocommerce_page_assets() {
            wp_enqueue_style( 'logestechs-woocommerce-order-style', logestechs_asset( 'css/woocommerce-order.css' ), [], '1.0', 'all' );
        }

        /**
         * Generate dynamic CSS based on configuration values.
         *
         * @since    1.0.0
         *
         * @return string CSS string
         */
        private function dynamic_css() {
            $css_vars    = Logestechs_Config::PLUGIN_STYLES;
            $dynamic_css = ":root {\n";
            foreach ( $css_vars as $var => $value ) {
                $dynamic_css .= "  $var: $value;\n";
            }
            $dynamic_css .= '}';

            return $dynamic_css;
        }

        /**
         * Localize scripts with the provided script array data.
         *
         * @since    1.0.0
         *
         * @param array $script_array The array containing data to localize.
         */
        private function localize_scripts( $script_array ) {
            // Add dynamic CSS
            wp_add_inline_style( 'logestechs-style', $this->dynamic_css() );
            wp_add_inline_style( 'logestechs-page-style', $this->dynamic_css() );

            // Localize scripts with common data
            wp_localize_script( 'logestechs-admin', 'logestechs_global_data', $script_array );
            wp_localize_script( 'logestechs-page-script', 'logestechs_global_data', $script_array );
            wp_localize_script( 'logestechs-woocommerce-order-script', 'logestechs_global_data', $script_array );
        }

        /**
         * Get data to be localized in scripts.
         *
         * @since    1.0.0
         *
         * @return array The data array
         */
        private function get_script_data() {
            return [
                'ajax_url'     => admin_url( 'admin-ajax.php' ),
                'security'     => wp_create_nonce( 'logestechs-security-nonce' ),
                'images'       => [
                    'logo'   => logestechs_image( 'logo.jpeg' ),
                    'trash'  => logestechs_image( 'trash.svg' ),
                    'edit'   => logestechs_image( 'edit.svg' ),
                    'loader' => logestechs_image( 'logestechs-loader.riv' )
                ],
                'completed_status_array' => Logestechs_Config::COMPLETED_STATUS,
                'localization' => [
                    'transfer_order' => __( 'Transfer Order', 'logestechs' ),
                    'request_pickup' => __( 'Request Pickup', 'logestechs' ),
                    'company_logo_alt' => __( 'company logo', 'logestechs' ),
                    'oops' => __( 'Oops...', 'logestechs' ),
                    'something_went_wrong' => __( 'Something went wrong.', 'logestechs' ),
                    'please_fill_out_all_fields' => __( 'Please fill out all fields.', 'logestechs' ),
                    'company_updated_successfully' => __( 'Company updated successfully!', 'logestechs' ),
                    'company_added_successfully' => __( 'Company added successfully!', 'logestechs' ),
                    'are_you_sure' => __( 'Are you sure?', 'logestechs' ),
                    'about_to_delete_company' => __( 'You are about to delete this company. This action cannot be undone.', 'logestechs' ),
                    'yes_delete_it' => __( 'Yes, delete it!', 'logestechs' ),
                    'no_keep_it' => __( 'No, keep it!', 'logestechs' ),
                    'deleted' => __( 'Deleted!', 'logestechs' ),
                    'company_deleted' => __( 'The company has been deleted.', 'logestechs' ),
                    'kept' => __( 'Kept', 'logestechs' ),
                    'order_not_cancelled' => __( 'The order has not been cancelled.', 'logestechs' ),
                    'cancel_order_warning' => __( 'You are about to cancel the order. This action cannot be undone.', 'logestechs' ),
                    'yes_cancel_it' => __( 'Yes, cancel it!', 'logestechs' ),
                    'cancelled' => __( 'Cancelled!', 'logestechs' ),
                    'order_cancelled' => __( 'The order has been cancelled.', 'logestechs' ),
                    'failed_to_download_pdf' => __( 'Failed to download the PDF.', 'logestechs' ),
                    'length_error' => __( 'Please write at least 2 characters.', 'logestechs' ),
                    'loading' => __( 'loading...', 'logestechs' ),
                    'no_matches_found' => __( 'No matches found.', 'logestechs' ),
                    'downloading' => __( 'Downloading...', 'logestechs' ),
                    'print_invoice' => __( 'Print Invoice', 'logestechs' ),
                    'bulk_print_error' => __( 'All selected orders must belong to the same company to enable bulk printing.', 'logestechs' ),
                    'bulk_transfer_error' => __( 'All selected orders must be assignable to enable bulk transfer.', 'logestechs' ),
                ]
            ];
        }

    }

}