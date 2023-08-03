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
            add_action( 'save_post', [$this, 'save_logestechs_metabox'] );
        }

        /**
         * Add Logestechs metabox.
         *
         * @since    1.0.0
         */
        public function add_logestechs_metabox() {
            add_meta_box(
                'logestechs_order_metabox', // Unique ID
                __( 'Logestechs Actions', 'logestechs' ), // Box title
                [$this, 'logestechs_metabox_html'], // Content callback
                'shop_order', // post type
                'normal', // The context within the screen where the box should display.
                'high' // The priority within the context where the box should show.
            );
        }

        /**
         * Display the Logestechs metabox.
         *
         * @param WP_Post $post The object for the current post/page.
         * @since    1.0.0
         */
        public function logestechs_metabox_html( $post ) {
            $order          = wc_get_order( $post->ID );
            $order_data     = $order->get_data(); // The Order's data
            $order_meta     = get_post_meta( $post->ID, '', true );
            $formatted_meta = $this->format_order_meta( $order_meta );
        
            $order_details = array_merge( $order_data, $formatted_meta );
        
            $keep_keys_order_data = array_flip( [
                'id', 
                'logestechs_order_id', 
                'logestechs_order_status', 
                'shipping_company',
            ] );
            
            // Add non-existing keys with null value
            $prepared_data = array_merge(array_fill_keys(array_flip($keep_keys_order_data), null), $order_details);
            
            // Use intersection to maintain the original order of keys in $keep_keys_order_data
            $prepared_data = array_intersect_key($prepared_data, $keep_keys_order_data);
        
            $view = new Logestechs_Woocommerce_Metabox_View( $prepared_data );
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

        /**
         * Save Logestechs metabox.
         *
         * @param int $post_id The ID of the post being saved.
         * @since    1.0.0
         */
        public function save_logestechs_metabox( $post_id ) {
            // Save the metabox data
            // Do some operations related to saving metabox data
        }
    }
}