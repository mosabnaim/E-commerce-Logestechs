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

            // add_filter( 'woocommerce_order_actions', [$this, 'add_cancel_order_action'] );
            // add_action( 'woocommerce_order_action_logestechs_cancel_order', [$this, 'handle_cancel_order_action'] );

            // If you want to allow non-logged in users to perform the action, use wp_ajax_nopriv_{action} instead.
            add_action( 'wp_ajax_logestechs_assign_company', [$this, 'assign_company'] );
            add_action( 'wp_ajax_logestechs_print_order', [$this, 'print_order'] );
            add_action( 'wp_ajax_logestechs_cancel_order', [$this, 'cancel_order'] );
        }

        public function get_transferred_orders() {
            if ( ! function_exists( 'wc_get_order_statuses' ) ) {
                return [];
            }

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
        public function cancel_order() {
            // Before you cancel the order, you might want to do some checks,
            // e.g. check if the order is in a status that allows cancellation
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( 'You do not have permission to perform this action.' );
            }
            $order_id   = $_POST['order_id'] ? intval( $_POST['order_id'] ) : null;
            if ( ! $order_id ) {
                wp_send_json_error( 'Error while processing this action!' );
                wp_die();
            }

            if(get_post_meta( $order_id, 'logestechs_order_status', true ) == 'cancelled') {
                wp_send_json_error( 'This order already cancelled' );
                wp_die();
            }

            // Make the API request to cancel the order
            // Cancel the order
            $response = $this->api->cancel_order( $order_id );
            // Check if the order creation was successful
            if ( $response ) {
                // Save the status in the database
                update_post_meta( $order_id, 'logestechs_order_status', 'cancelled' );
                wp_send_json_success($response);
            }
            die();
        }
        public function print_order() {
            // Before you cancel the order, you might want to do some checks,
            // e.g. check if the order is in a status that allows cancellation
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( 'You do not have permission to perform this action.' );
            }
            $order_id   = $_POST['order_id'] ? intval( $_POST['order_id'] ) : null;
            if ( ! $order_id ) {
                wp_send_json_error( 'Error while processing this action!' );
                wp_die();
            }

            if(get_post_meta( $order_id, 'logestechs_order_status', true ) == 'cancelled') {
                wp_send_json_error( 'This order already cancelled' );
                wp_die();
            }

            // Make the API request to cancel the order
            // Cancel the order
            $response = $this->api->print_order( $order_id );
            // Check if the order creation was successful
            if ( $response ) {
                // Save the status in the database
                wp_send_json_success($response);
            }
            die();
        }

        /**
         * Transfer order to Logestechs.
         *
         * @param array $order The order to transfer.
         * @return mixed The response from the API.
         */         
        public function assign_company() {
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( 'You do not have permission to perform this action.' );
            }

            $company_id = $_POST['company_id'] ? intval( $_POST['company_id'] ) : null;
            $order_id   = $_POST['order_id'] ? intval( $_POST['order_id'] ) : null;

            if ( ! $company_id || ! $order_id ) {
                wp_send_json_error( 'Error while processing this action!' );
                wp_die();
            }

            if(get_post_meta( $order_id, 'logestechs_order_status', true ) == 'transferred') {
                wp_send_json_error( 'This order already transferred' );
                wp_die();
            }

            // Retrieve the order
            $order = wc_get_order( $order_id );

            // You can perform further operations or validations here
            // Sanitize POST data
            $security_manager = new Logestechs_Security_Manager();
            $validator        = $security_manager->get_validator();
            $errors           = $validator->validate_order( $order );

            if ( ! empty( $errors ) ) {
                wp_send_json_error( ['errors' => $errors] );
                wp_die();
            }
            $credentials_storage = Logestechs_Credentials_Storage::get_instance();
            $company             = $credentials_storage->get_company( $company_id );
            // Call your API handler to save the order
            $response = $this->api->transfer_order_to_logestechs( $company, $order );

            // Check if the order creation was successful
            if ( isset( $response['barcode'] ) ) {
                // Store the fact that the order has been transferred, along with the Logestechs order ID
                update_post_meta( $order_id, 'logestechs_order_barcode', $response['barcode'] );
                update_post_meta( $order_id, 'logestechs_order_id', $response['id'] );
                update_post_meta( $order_id, 'logestechs_company_name', $company->company_name );
                update_post_meta( $order_id, 'logestechs_api_company_id', $company->company_id );
                update_post_meta( $order_id, 'logestechs_local_company_id', $company_id );
                update_post_meta( $order_id, 'logestechs_date', date('d/m/Y H:i') );
                update_post_meta( $order_id, 'logestechs_order_status', 'transferred' );
            }
            wp_send_json_success( isset( $response['barcode'] ) );
        }

        public function get_order_data( $order ) {
            $quantity = 0;
            foreach ( $order->get_items() as $item ) {
                $quantity += $item->get_quantity();
            }
            $custom_settings = new Logestechs_Woocommerce_Custom_Settings;
            $order_data      = [
                'pkg'                => [
                    'receiverName'       => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
                    'cod'                => $order->get_total(), // You'll need to modify this according to your requirements
                    'notes'          => $order->get_customer_note(), // Order notes
                    // 'invoiceNumber' => $order->get_id(),
                    'senderName' => $custom_settings->store_owner(),
                    'businessSenderName' => $custom_settings->business_name(),
                    'senderPhone'        => $custom_settings->store_phone(),
                    'receiverPhone'      => $order->get_billing_phone(), // Ensure you have this field
                    'receiverPhone2' => '',
                    'serviceType'        => 'STANDARD',
                    'shipmentType'       => 'COD',
                    'quantity'           => $quantity,
                    'description'        => ''
                ],
                'destinationAddress' => [
                    'addressLine1' => $order->get_shipping_address_1() . ' - ' . $order->get_shipping_address_2(),
                    'cityId'       => 1, // You'll need to map WooCommerce city to Logestechs city ID
                    'villageId' => 38, // You'll need to provide this value
                    'regionId' => 1 // You'll need to provide this value
                ],
                'pkgUnitType'        => 'METRIC',
                'originAddress'      => [
                    'addressLine1' => get_option( 'woocommerce_store_address' ),
                    'addressLine2' => get_option( 'woocommerce_store_address_2' ),
                    'cityId'       => 5, // You'll need to map WooCommerce city to Logestechs city ID
                    'regionId' => 1, // You'll need to provide this value
                    'villageId' => 180 // You'll need to provide this value
                ]
            ];

            return $order_data;
        }
    }
}
