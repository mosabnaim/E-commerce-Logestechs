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

if ( ! class_exists( 'Logestechs_Order_Handler' ) ) {

    class Logestechs_Order_Handler {

        private $api; // instance of Logestechs_API
        private $woocommerce_list;

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {
            // Initialize the API
            $this->api              = new Logestechs_Api_Handler();
            $this->woocommerce_list = new Logestechs_Woocommerce_List_View();
            add_filter( 'manage_edit-shop_order_columns', [$this->woocommerce_list, 'add_custom_column_header'], 20 );
            add_action( 'manage_shop_order_posts_custom_column', [$this->woocommerce_list, 'add_custom_column_data'], 20, 2 );

            // Add an action to hook into the order status 'processing'
            // add_action( 'woocommerce_order_status_processing', [$this, 'handle_processing_order'] );

            add_filter( 'woocommerce_order_actions', [$this, 'add_cancel_order_action'] );
            add_action( 'woocommerce_order_action_logestechs_cancel_order', [$this, 'handle_cancel_order_action'] );

            // If you want to allow non-logged in users to perform the action, use wp_ajax_nopriv_{action} instead.
            add_action( 'wp_ajax_logestechs_transfer_order', [ $this, 'assign_company' ] );
            add_action( 'wp_ajax_logestechs_cancel_order', [ $this, 'cancel_order' ] );
        }


        

        public function get_transferred_orders() {
            $args = [
                'post_type'   => 'shop_order',
                'post_status' => array_keys( wc_get_order_statuses() ),
                'meta_query'  => [
                    [
                        'key'   => 'logestechs_order_status',
                        'value' => ['transferred', 'cancelled']
                    ]
                ]
            ];

            $orders = get_posts( $args );

            return $orders;
        }

        private function prepare_order_data( $order ) {
            // Convert the WooCommerce order to the format required by Logestechs API
            // This method needs to be implemented based on the specific requirements of the Logestechs API
            return $order;
        }

        // public function handle_processing_order( $order_id ) {
        //     // Get the order object
        //     $order = wc_get_order( $order_id );
        //     // Transfer the order to Logestechs
        //     $this->transfer_order( $order );
        // }

        // Handle the custom order action
        public function handle_cancel_order_action( $order ) {
            // Instantiate your order handler
            $order_handler = new Logestechs_Order_Handler();

            // Cancel the order on Logestechs
            $order_handler->cancel_order( $order->get_id() );
        }

        // Add a custom order action to WooCommerce's order actions dropdown
        public function add_cancel_order_action( $actions ) {
            $actions['logestechs_cancel_order'] = __( 'Cancel Order on Logestechs', 'logestechs' );

            return $actions;
        }

        /**
         * Track a Logestechs order.
         *
         * @param string $order_id The ID of the order to track.
         * @return mixed The tracking information.
         */
        public function track_order( $order_id ) {
            // Track the order
            // return $this->api->request('track-endpoint', 'GET', ['order_id' => $order_id]);
        }

        /**
         * Cancel a Logestechs order.
         *
         * @param string $order_id The ID of the order to cancel.
         * @return mixed The response from the API.
         */
        public function cancel_order( $order_id ) {
            // Before you cancel the order, you might want to do some checks,
            // e.g. check if the order is in a status that allows cancellation

            // Make the API request to cancel the order
            // Cancel the order
            $response = $this->api->cancel_order( $order_id );
            // Check if the order creation was successful
            if ( $response ) {
                // Save the status in the database
                update_post_meta( $order_id, 'logestechs_order_status', 'cancelled' );
            }
            // return $this->api->request('cancel-endpoint', 'POST', ['order_id' => $order_id]);
            echo 'xD';
            die();
        }

        

        /**
         * Transfer order to Logestechs.
         *
         * @param array $order The order to transfer.
         * @return mixed The response from the API.
         */
        public function assign_company( WC_Order $order ) {
            // Transfer the order to Logestechs
            // You can use the API instance to make a request to Logestechs
            // return $this->api->request('transfer-endpoint', 'POST', $order);
            // Preprocess the order data as needed by Logestechs API
            // Get the WooCommerce order's ID
            $wc_order_id = $order->get_id();

            // Prepare the order data for the Logestechs API
            // This will vary depending on the Logestechs API's requirements
            $order_data = $this->prepare_order_data( $order );

            // Create the order in Logestechs
            // $response = $this->api->create_order( $order_data );
            $logestechs_order_id = $this->api->create_order( $order_data );

            // Check if the order creation was successful
            if ( $logestechs_order_id ) {
                // Store the fact that the order has been transferred, along with the Logestechs order ID
                update_post_meta( $wc_order_id, 'logestechs_order_id', $logestechs_order_id );
                update_post_meta( $wc_order_id, 'logestechs_company_name', 'KSA Demo' );
                update_post_meta( $wc_order_id, 'logestechs_order_status', 'transferred' );
            }
            die();
        }
    }
}
