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

        }

        public function render_popups() {
            // Get the current screen's information
            $screen = get_current_screen();

            // Ensure that we're dealing with a WooCommerce order
            if ( 'shop_order' == $screen->post_type || 'edit-shop_order' === $screen->id || 'shop_order' === $screen->id || 'toplevel_page_logestechs' === $screen->id ) {
                // Here we could fetch data if needed and pass it to the View
                $transfer_popup = new Logestechs_Manage_Companies_Popup_View();
                if ( 'toplevel_page_logestechs' === $screen->id ) {
                    $transfer_popup->render();
                } else {
                    $transfer_popup->render( false );
                }
                $tracking_popup = new Logestechs_Tracking_Details_Popup_View();
                $tracking_popup->render();
            }
        }
    }
}
