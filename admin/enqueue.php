<?php
/**
 * The file that handles enqueuing of styles and scripts for the plugin
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/admin
 */

if ( ! class_exists( 'Logestechs_Enqueue' ) ) {

    if ( ! class_exists( 'Logestechs_Enqueue' ) ) {

        class Logestechs_Enqueue {

            /**
             * Initialize the class and set its properties.
             */
            public function __construct() {
                add_action( 'admin_enqueue_scripts', [$this, 'enqueue_assets'] );
            }

            /**
             * Register and enqueue the stylesheets and scripts for the admin area.
             */
            public function enqueue_assets( $hook ) {
                global $post_type;
                $slug = Logestechs_Config::MENU_SLUG;
                
                $debugger = new Logestechs_Debugger;
                $debugger->clear()->log([$hook])->write();
                // Check if we are on the Logestechs page, WooCommerce > Orders page, or WooCommerce > Single Order page.
                if ( ! in_array( $hook, ['toplevel_page_' . $slug, $slug. '_page_settings', 'edit.php', 'post.php', 'post-new.php'] ) ) {
                    return;
                }

                // Only run this on WooCommerce > Orders page or WooCommerce > Single Order page.
                if ( in_array( $hook, ['edit.php', 'post.php', 'post-new.php'] ) ) {
                    // Get the post_type from GET or POST superglobals if available, otherwise set a default value
                    $get_post_type  = $_GET['post_type'] ?? null;
                    $post_post_type = $_POST['post_type'] ?? null;

                    if ( ! in_array( $post_type, ['shop_order', $get_post_type, $post_post_type] ) ) {
                        return;
                    }
                }

                // Localize the script with new data
                $script_data_array = [
                    'ajax_url'     => admin_url( 'admin-ajax.php' ),
                    'security'     => wp_create_nonce( 'logestechs-security-nonce' ),
                    'images'       => [
                        'logo'  => logestechs_image( 'logo.jpeg' ),
                        'trash' => logestechs_image( 'trash.svg' ),
                        'edit'  => logestechs_image( 'edit.svg' ),
                        'loader'  => logestechs_image( 'logestechs-loader.riv' )
                    ],
                    'localization' => [
                        'update' => __( 'Update Company', 'logestechs' )
                    ]
                ];
                wp_enqueue_style( 'logestechs-style', logestechs_asset( 'css/style.css' ), [], '1.0', 'all' );
                wp_enqueue_script( 'logestechs-admin', logestechs_asset( 'js/main.js' ), ['jquery'], null, true );
                wp_enqueue_script( 'sweetalert2','https://cdn.jsdelivr.net/npm/sweetalert2@11', [], null, true );
                wp_enqueue_script( 'rive','https://unpkg.com/@rive-app/canvas@2.1.0', [], null, true );

                if ( $hook == 'toplevel_page_' . $slug ) {
                    wp_enqueue_script('daterangepicker_moment', logestechs_asset( 'js/rangepicker/moment.min.js' ), ['jquery'], null, true);
                    wp_enqueue_style('daterangepicker_style', logestechs_asset( 'js/rangepicker/daterangepicker.css' ));
                    wp_enqueue_script('daterangepicker_script', logestechs_asset( 'js/rangepicker/daterangepicker.js' ), ['jquery'], null, true);
                    wp_enqueue_style( 'logestechs-page-style', logestechs_asset( 'css/logestechs-page.css' ), [], '1.0', 'all' );
                    wp_enqueue_script( 'logestechs-page-script', logestechs_asset( 'js/logestechs-page.js' ), ['jquery'], null, true );
                }
                if ( $hook == $slug. '_page_settings' ) {
                    wp_enqueue_style( 'logestechs-page-style', logestechs_asset( 'css/logestechs-page.css' ), [], '1.0', 'all' );
                    wp_enqueue_script( 'logestechs-page-script', logestechs_asset( 'js/logestechs-page.js' ), ['jquery'], null, true );
                }
                if ( 'post.php' === $hook && 'shop_order' === $post_type ) {
                    // Enqueue the additional stylesheet for the WooCommerce Order page
                    wp_enqueue_style( 'logestechs-woocommerce-order-style', logestechs_asset( 'css/woocommerce-order.css' ), [], '1.0', 'all' );
                    wp_enqueue_script( 'logestechs-woocommerce-order-script', logestechs_asset( 'js/woocommerce-order.js' ), ['jquery'], null, true );
                }

                wp_add_inline_style( 'logestechs-style', $this->dynamic_css() );
                wp_add_inline_style( 'logestechs-page-style', $this->dynamic_css() );
                wp_add_inline_style( 'logestechs-woocommerce-order-script', $this->dynamic_css() );

                wp_localize_script( 'logestechs-admin', 'logestechs_global_data', $script_data_array );
                wp_localize_script( 'logestechs-page-script', 'logestechs_global_data', $script_data_array );
                wp_localize_script( 'logestechs-woocommerce-order-script', 'logestechs_global_data', $script_data_array );
            }

            private function dynamic_css() {
                $css_vars = Logestechs_Config::PLUGIN_STYLES;

                $dynamic_css = ":root {\n";
                foreach ( $css_vars as $var => $value ) {
                    $dynamic_css .= "  $var: $value;\n";
                }
                $dynamic_css .= '}';

                return $dynamic_css;
            }

        }

    }

}
