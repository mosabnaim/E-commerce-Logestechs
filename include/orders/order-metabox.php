<?php
/**
 * The file that handles custom order metaboxes
 *
 * This file is used to handle custom order metaboxes for Logestechs interactions such as transfer order button and tracking info.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/include/orders
 */

if ( ! class_exists( 'Logestechs_Order_Metabox' ) ) {

    class Logestechs_Order_Metabox {

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {
            add_action( 'add_meta_boxes', [$this, 'add_logestechs_metabox'], 10 );
        }

        /**
         * Add Logestechs metabox.
         *
         * @since    1.0.0
         */
        public function add_logestechs_metabox() {
            global $pagenow;

            if ( 'post.php' !== $pagenow || 'shop_order' !== get_post_type() ) {
                return; // Return early if it's not the edit page of shop_order post type
            }

            add_meta_box(
                'logestechs_order_metabox',             // Unique ID
                __( 'Logestechs Actions', 'logestechs' ), // Box title
                [$this, 'logestechs_metabox_html'],     // Content callback
                'shop_order',                           // post type
                'normal',                               // The context within the screen where the box should display.
                'high'                                  // The priority within the context where the box should show.
            );
        }

        /**
         * Display the Logestechs metabox.
         *
         * @param WP_Post $post The object for the current post/page.
         * @since    1.0.0
         */
        public function logestechs_metabox_html( $post ) {
            $api             = new Logestechs_Api_Handler();
            $response        = $api->track_order( $post->ID );
            $currency_symbol = html_entity_decode( get_woocommerce_currency_symbol() );
            $details_to_display = [
                'id' => $post->ID
            ];

            if ( isset($response['id']) ) {
                $details_to_display = [
                    'id'                     => $post->ID,
                    'order_id'               => $response['id'],
                    'package_number'         => '#' . $response['barcode'],
                    'cost'                  => $response['cost'] . ' ' . $currency_symbol, // Price from WooCommerce
                    // 'price'                  => $order->get_total() . ' ' . $currency_symbol, // Price from WooCommerce
                    'reservation_date' => ! empty( $response['createdDate'] ) ? date( 'd/m/Y', strtotime( $response['createdDate'] ) ) : 'N/A',
                    'shipment_type'          => $response['shipmentType'],
                    'recipient'              => $response['receiverFirstName'] . ' ' . $response['receiverLastName'],
                    'package_weight'         => ! empty( $response['weight'] ) ? $response['weight'] : 'N/A',
                    'expected_delivery_date' => ! empty( $response['expectedDeliveryDate'] ) ? date( 'd/m/Y', strtotime( $response['expectedDeliveryDate'] ) ) : 'N/A',
                    'phone_number'           => $response['receiverPhone'],
                    'status'                 => get_post_meta( $post->ID, '_logestechs_order_status', true )
                ];
            }

            $view = new Logestechs_Woocommerce_Metabox_View( $details_to_display );
            $view->render();
        }

        public function format_order_meta( $order_meta ) {
            foreach ( $order_meta as $key => $value ) {
                if ( strpos( $key, '_' ) === 0 ) {
                    $new_key              = ltrim( $key, '_' ); // remove underscore from the start of the string
                    $order_meta[$new_key] = ( is_array( $value ) && count( $value ) === 1 ) ? $value[0] : $value;
                    unset( $order_meta[$key] ); // remove the old key-value pair
                } elseif ( is_array( $value ) && count( $value ) === 1 ) {
                    $order_meta[$key] = $value[0];
                }
            }

            return $order_meta;
        }
    }
}