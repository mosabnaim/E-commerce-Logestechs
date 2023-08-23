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
            add_action( 'wp_ajax_logestechs_prepare_order_popup', [$this, 'prepare_order_popup'] );
            add_action( 'wp_ajax_logestechs_print_order', [$this, 'print_order'] );
            add_action( 'wp_ajax_logestechs_cancel_order', [$this, 'cancel_order'] );
            add_action( 'wp_ajax_logestechs_fetch_order_details', [$this, 'track_order'] );
            add_action( 'wp_ajax_logestechs_sync_orders_status', [$this, 'sync_orders_status'] );
            add_action( 'wp_ajax_logestechs_get_orders', [$this, 'load_orders'] );
            add_action( 'wp_ajax_logestechs_fetch_villages', [$this, 'fetch_villages'] );
        }

        public function get_transferred_orders( $items_per_page = 10 ) {
            global $wpdb;

            if ( ! function_exists( 'wc_get_order_statuses' ) ) {
                return ['orders' => [], 'total_count' => 0];
            }

            $paged           = isset( $_POST['paged'] ) ? intval( $_POST['paged'] ) : 1;
            $search          = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
            $date_from_input = isset( $_POST['date_from'] ) ? sanitize_text_field( $_POST['date_from'] ) : '';
            $date_to_input   = isset( $_POST['date_to'] ) ? sanitize_text_field( $_POST['date_to'] ) : '';

            $date_from = strtotime( $date_from_input . ' 00:00:00' );
            $date_to   = strtotime( $date_to_input . ' 23:59:59' );

            $sort_by    = isset( $_POST['sort_by'] ) ? sanitize_text_field( $_POST['sort_by'] ) : 'date';
            $sort_order = isset( $_POST['sort_order'] ) ? sanitize_text_field( $_POST['sort_order'] ) : 'DESC';
            // Define a mapping array
            $sort_mapping = [
                'id'         => 'p.ID',
                'date'       => 'meta_logestechs_date.meta_value',
                'barcode_id' => 'meta_order_barcode.meta_value',
                'company'    => 'meta_company_name.meta_value',
                'status'     => 'meta_status.meta_value'
            ];
            if ( array_key_exists( $sort_by, $sort_mapping ) ) {
                $orderby_column = $sort_mapping[$sort_by];
            } else {
                $orderby_column = 'p.ID'; // default sort column
            }
            $status_keys = implode( "','", array_keys( wc_get_order_statuses() ) );

            $base_query = "FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} as meta_status ON p.ID = meta_status.post_id AND meta_status.meta_key = '_logestechs_order_status'
                LEFT JOIN {$wpdb->postmeta} as meta_order_barcode ON p.ID = meta_order_barcode.post_id AND meta_order_barcode.meta_key = '_logestechs_order_barcode'
                LEFT JOIN {$wpdb->postmeta} as meta_company_name ON p.ID = meta_company_name.post_id AND meta_company_name.meta_key = '_logestechs_company_name'
                LEFT JOIN {$wpdb->postmeta} as meta_logestechs_date ON p.ID = meta_logestechs_date.post_id AND meta_logestechs_date.meta_key = '_logestechs_date'
                WHERE p.post_type = 'shop_order'
                AND p.post_status IN ('$status_keys')
                AND meta_order_barcode.meta_value IS NOT NULL
                ";

            // Add search
            if ( $search ) {
                $search_like = '%' . $wpdb->esc_like( $search ) . '%';
                $base_query .= $wpdb->prepare( ' AND (p.ID LIKE %s OR meta_status.meta_value LIKE %s OR meta_order_barcode.meta_value LIKE %s OR meta_company_name.meta_value LIKE %s)', $search_like, $search_like, $search_like, $search_like );
            }

            // Add date filter
            if ( $date_from_input && $date_to_input ) {
                $base_query .= $wpdb->prepare( ' AND meta_logestechs_date.meta_value BETWEEN %d AND %d', $date_from, $date_to );
            }

            // Add order by (assuming $sort_by and $sort_order are validated against a whitelist of allowable values)
            if ( $sort_by && $sort_order ) {
                $base_query .= " ORDER BY {$orderby_column} {$sort_order}";
            }

            $count_query = 'SELECT COUNT(DISTINCT p.ID) ' . $base_query;
            $total_count = $wpdb->get_var( $count_query );

            $offset = ( $paged - 1 ) * $items_per_page;

            // Base query
            $query = $wpdb->prepare( '
                SELECT p.*,
                    meta_status.meta_value as status,
                    meta_order_barcode.meta_value as barcode,
                    meta_company_name.meta_value as company_name
                ' . $base_query, $status_keys );
            $query .= $wpdb->prepare( ' LIMIT %d, %d', $offset, $items_per_page );
            $orders = $wpdb->get_results( $query );

            return ['orders' => $orders, 'total_count' => $total_count];
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
            $order_id = $_POST['order_id'] ? intval( $_POST['order_id'] ) : null;
            if ( ! $order_id ) {
                wp_send_json_error( 'Error while processing this action!' );
                wp_die();
            }

            if ( get_post_meta( $order_id, '_logestechs_order_status', true ) == 'Cancelled' ) {
                wp_send_json_error( 'This order already cancelled' );
                wp_die();
            }
            // Make the API request to cancel the order
            // Cancel the order
            $response = $this->api->cancel_order( $order_id );
            // Check if the order creation was successful
            // Save the status in the database

            if ( $response ) {
                wp_send_json_error( $response );
                die();
            }

            update_post_meta( $order_id, '_logestechs_order_status', 'Cancelled' );
            die();
        }

        public function print_order() {
            // Before you cancel the order, you might want to do some checks,
            // e.g. check if the order is in a status that allows cancellation
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( 'You do not have permission to perform this action.' );
            }
            $order_id = $_POST['order_id'] ? intval( $_POST['order_id'] ) : null;
            if ( ! $order_id ) {
                wp_send_json_error( 'Error while processing this action!' );
                wp_die();
            }

            if ( get_post_meta( $order_id, '_logestechs_order_status', true ) == 'Cancelled' ) {
                wp_send_json_error( 'This order already cancelled' );
                wp_die();
            }

            // Make the API request to cancel the order
            // Cancel the order
            $response = $this->api->print_order( $order_id );
            // Check if the order creation was successful
            if ( $response ) {
                // Save the status in the database
                wp_send_json_success( $response );
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

            $company_id    = $_POST['company_id'] ? intval( $_POST['company_id'] ) : null;
            $order_id      = $_POST['order_id'] ? intval( $_POST['order_id'] ) : null;
            $village_id    = $_POST['logestechs_destination_village_id'] ? intval( $_POST['logestechs_destination_village_id'] ) : null;
            $region        = $_POST['logestechs_destination_region_id'] ? intval( $_POST['logestechs_destination_region_id'] ) : null;
            $city          = $_POST['logestechs_destination_city_id'] ? intval( $_POST['logestechs_destination_city_id'] ) : null;
            $store_village = $_POST['logestechs_store_village_id'] ? intval( $_POST['logestechs_store_village_id'] ) : null;
            $store_city    = $_POST['logestechs_store_city_id'] ? intval( $_POST['logestechs_store_city_id'] ) : null;
            $store_region  = $_POST['logestechs_destination_region_id'] ? intval( $_POST['logestechs_store_region_id'] ) : null;
              // Get other POST variables, safely trimmed
            $business_name      = sanitize_text_field($_POST['logestechs_business_name'] ?? '');
            $store_owner        = sanitize_text_field($_POST['logestechs_store_owner'] ?? '');
            $store_phone_number = sanitize_text_field($_POST['logestechs_store_phone_number'] ?? '');
            $store_address_1    = sanitize_text_field($_POST['logestechs_store_address'] ?? '');
            $store_address_2    = sanitize_text_field($_POST['logestechs_store_address_2'] ?? '');
            $custom_store       = isset($_POST['logestechs_custom_store']) ? sanitize_text_field($_POST['logestechs_custom_store']) : '';


            if ( ! $company_id || ! $order_id ) {
                wp_send_json_error( 'Error while processing this action!' );
                wp_die();
            }

            if ($custom_store && (!$order_id || !$company_id || !$business_name || !$store_owner || !$store_phone_number || !$store_address_1 || !$store_address_2)) {
                // You might want to handle this error better, possibly returning a WP_Error object
                wp_send_json_error('Missing required fields.');
            }

            if ( get_post_meta( $order_id, '_logestechs_order_status', true ) == 'transferred' ) {
                wp_send_json_error( 'This order already transferred' );
                wp_die();
            }

            if ( get_post_meta( $order_id, '_logestechs_order_status', true ) == 'transferred' ) {
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

            update_post_meta( $order_id, '_logestechs_village', $village_id );
            update_post_meta( $order_id, '_logestechs_region', $region );
            update_post_meta( $order_id, '_logestechs_city', $city );
            
            if($custom_store) {
                $order->store_village = $store_village;
                $order->store_city = $store_city;
                $order->store_region = $store_region;
                $order->business_name = $business_name;
                $order->store_owner = $store_owner;
                $order->store_phone_number = $store_phone_number;
                $order->store_address = $store_address_1;
                $order->store_address_2 = $store_address_2;
            }

            $credentials_storage = Logestechs_Credentials_Storage::get_instance();
            $company             = $credentials_storage->get_company( $company_id );
            // Call your API handler to save the order
            $response               = $this->api->transfer_order_to_logestechs( $company, $order );
            if(isset($response['error'])) {
                wp_send_json_error( ['errors' => [$response['error']]] );
                wp_die();
            }
            $date                   = new DateTime();                   // Current date and time
            $date->setTimezone( new DateTimeZone( wp_timezone_string() ) ); // Set WordPress timezone
            $timestamp = $date->getTimestamp();                         // Get Unix timestamp
                                                                        // Check if the order creation was successful
            if ( isset( $response['barcode'] ) ) {
                // Store the fact that the order has been transferred, along with the Logestechs order ID
                update_post_meta( $order_id, '_logestechs_order_barcode', $response['barcode'] );
                update_post_meta( $order_id, '_logestechs_order_id', $response['id'] );
                update_post_meta( $order_id, '_logestechs_company_name', $company->company_name );
                update_post_meta( $order_id, '_logestechs_api_company_id', $company->company_id );
                update_post_meta( $order_id, '_logestechs_local_company_id', $company_id );
                update_post_meta( $order_id, '_logestechs_date', $timestamp );
                update_post_meta( $order_id, '_logestechs_order_status', 'transferred' );
            }
            wp_send_json_success( isset( $response['barcode'] ) );
        }

        public function prepare_order_popup() {
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
            // Retrieve the order
            $order = wc_get_order( $order_id );
            wp_send_json_success( $order->get_formatted_shipping_address() );

            wp_die();
        }

        public function track_order() {
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( 'You do not have permission to perform this action.' );
            }

            $order_id = $_POST['order_id'] ? intval( $_POST['order_id'] ) : null;

            if ( ! $order_id ) {
                wp_send_json_error( 'Error while processing this action!' );
                wp_die();
            }

            // Get the order ID from the Ajax request
            $order_id = intval( $_POST['order_id'] );

            // Get the order details from WooCommerce (you might need to modify this part based on your needs)
            $currency_symbol = html_entity_decode( get_woocommerce_currency_symbol() );

            // Make a request to the Logestechs API to get the details
            $response      = $this->api->track_order( $order_id );
            $created_date  = logestechs_convert_to_local_time( $response['createdDate'] );
            $expected_date = logestechs_convert_to_local_time( $response['expectedDeliveryDate'] );

            // Compile the details to display
            $details_to_display = [
                'order_id'               => $order_id,
                'package_number'         => '#' . $response['barcode'],
                'price'                  => $response['cost'] . ' ' . $currency_symbol, // Price from WooCommerce
                                                                                        // 'price'                  => $order->get_total() . ' ' . $currency_symbol, // Price from WooCommerce
                'reservation_date' => ! empty( $created_date ) ? $created_date->format( 'd/m/Y' ) : 'N/A',
                'shipment_type'          => $response['shipmentType'],
                'recipient'              => $response['receiverFirstName'] . ' ' . $response['receiverLastName'],
                'package_weight'         => ! empty( $response['weight'] ) ? $response['weight'] : 'N/A',
                'expected_delivery_date' => ! empty( $expected_date ) ? $expected_date->format( 'd/m/Y' ) : 'N/A',
                'phone_number'           => $response['receiverPhone']
            ];

            $tracking_data = [];
            foreach ( $response['deliveryRoute'] as $tracking ) {
                $date            = logestechs_convert_to_local_time( $tracking['deliveryDate'] );
                $tracking_data[] = [
                    'name' => $tracking['name'],
                    'date' => $date->format( 'h:i A' ),
                    'time' => $date->format( 'Y-m-d' )
                ];
            }

            $details_to_display['tracking_data'] = $tracking_data;
            // Respond with JSON
            wp_send_json( $details_to_display );
            wp_die();
        }

        public function get_order_data( $order ) {
            $quantity = 0;
            foreach ( $order->get_items() as $item ) {
                $quantity += $item->get_quantity();
            }
            $store_address    = $order->store_address;
            $store_address_2  = $order->store_address_2;
            $store_phone      = $order->store_phone_number;
            $business_name    = $order->business_name;
            $store_owner      = $order->store_owner;
            $store_region_id  = $order->store_region;
            $store_city_id    = $order->store_city;
            $store_village_id = $order->store_village;

            $region_id  = $order->village;
            $city_id    = $order->region;
            $village_id = $order->city;
            
            $package_items = [];
            foreach ( $order->get_items() as $item ) {
                $product = $item->get_product();
                $name = $product->get_name(); // Product name
                $price = $product->get_price(); // Product price
        
                $package_items[] = [
                    "name" => $name,
                    "cod" => $price * $item->get_quantity() // Total cost for this item
                ];
            }
            $order_data = [
                'pkg'                => [
                    'receiverName' => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
                    'cod'          => $order->get_total(),                                                          // You'll need to modify this according to your requirements
                    'notes'        => $order->get_customer_note(),                                                  // Order notes
                      // 'invoiceNumber' => $order->get_id(),
                    'packageItemsToDeliverList' => $package_items,
                    'senderName'                => $store_owner,
                    'businessSenderName'        => $business_name,
                    'senderPhone'               => $store_phone,
                    'receiverPhone'             => $order->get_billing_phone(),   // Ensure you have this field
                    'receiverPhone2'            => '',
                    'serviceType'               => 'STANDARD',
                    'shipmentType'              => 'COD',
                    'quantity'                  => $quantity,
                    'description'               => '',
                    'integrationSource'         => 'WOOCOMMERCE'
                ],
                'destinationAddress' => [
                    'addressLine1' => $order->get_shipping_address_1() . ' - ' . $order->get_shipping_address_2(),
                    'cityId'       => intval($city_id), // You'll need to map WooCommerce city to Logestechs city ID
                    'regionId'     => intval($region_id), // You'll need to provide this value
                    'villageId'    => intval($village_id) // You'll need to provide this value
                ],
                'pkgUnitType'   => 'METRIC',
                'originAddress' => [
                    'addressLine1' => $store_address,
                    'addressLine2' => $store_address_2,
                    'cityId'       => intval($store_region_id),   // You'll need to map WooCommerce city to Logestechs city ID
                    'regionId'     => intval($store_city_id),     // You'll need to provide this value
                    'villageId'    => intval($store_village_id)   // You'll need to provide this value
                ]
            ];
            // $debugger = new Logestechs_Debugger;
            // $debugger->clear()->log([$order_data])->write();
            return $order_data;
        }

        public function sync_orders_status() {
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( 'You do not have permission to perform this action.' );
            }

            $api_handler   = new Logestechs_Api_Handler();
            $order_handler = new Logestechs_Order_Handler();

            $orders = $order_handler->get_transferred_orders()['orders'];

            // Extract the WordPress order IDs and their corresponding logestechs_order_id
            $logestechs_order_ids = [];
            foreach ( $orders as $order_post ) {
                $order_id                        = $order_post->ID;
                $logestechs_order_ids[$order_id] = get_post_meta( $order_id, '_logestechs_order_id', true );
            }

            // Fetch statuses from Logestechs
            $statuses = $api_handler->get_orders_status( array_values( $logestechs_order_ids ) );

            // Update post meta
            $updated_statuses = [];
            foreach ( $logestechs_order_ids as $order_id => $logestechs_order_id ) {
                if ( isset( $statuses[$logestechs_order_id] ) ) {
                    update_post_meta( $order_id, '_logestechs_order_status', $statuses[$logestechs_order_id] );
                    $updated_statuses[$order_id] = $statuses[$logestechs_order_id];
                }
            }

            // Respond with JSON
            wp_send_json( $updated_statuses );
            wp_die();
        }

        public function load_orders() {
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( 'You do not have permission to perform this action.' );
            }

            // Get the requested page number
            $current_page  = isset( $_POST['paged'] ) ? intval( $_POST['paged'] ) : 1;
            $order_handler = new Logestechs_Order_Handler();
            $orders_array  = $order_handler->get_transferred_orders();
            $orders        = $orders_array['orders'];
            $total_count   = $orders_array['total_count'];

            ob_start();
            if ( empty( $orders ) ) {
                ?>
                <tr>
                    <td colspan="6"><?php _e( 'No orders found.', 'logestechs' );?></td>
                </tr>
                <?php
            } else {
                foreach ( $orders as $order_post ) {
                    $order               = wc_get_order( $order_post );
                    $order_id            = $order->get_id();
                    $order_barcode       = get_post_meta( $order_id, '_logestechs_order_barcode', true );
                    $order_logestechs_id = get_post_meta( $order_id, '_logestechs_order_id', true );
                    $company_name        = get_post_meta( $order_id, '_logestechs_company_name', true );
                    $date                = logestechs_convert_to_local_time( get_post_meta( $order_id, '_logestechs_date', true ) );
                    $status              = get_post_meta( $order_id, '_logestechs_order_status', true );
                    ?>
                    <tr class="js-logestechs-order" data-order-id="<?php echo $order_id; ?>">
                        <td>#<?php echo esc_html( $order_id ); ?></td>
                        <td><?php echo $date->format( 'd/m/Y H:i' ); ?></td>
                        <td>#<?php echo esc_html( $order_barcode ); ?></td>
                        <td><?php echo esc_html( $company_name ); ?></td>
                        <td>
                            <span class="js-logestechs-status-cell"><div class="logestechs-skeleton-loader"></div></span>
                        </td>
                        <td>
                            <div class="logestechs-dropdown">
                                <img src="<?php echo logestechs_image( 'dots.svg' ); ?>" />
                                <div class="logestechs-dropdown-content js-normal-dropdown">
                                    <div class="js-logestechs-print" data-order-id="<?php echo $order_id; ?>">Print Invoice</div>
                                    <div class="js-open-details-popup" data-order-id="<?php echo $order_id; ?>">Track</div>
                                    <div class="js-logestechs-cancel" data-order-id="<?php echo $order_id; ?>">Cancel</div>
                                </div>
                                <div class="logestechs-dropdown-content js-cancelled-dropdown hidden">
                                    <div class="js-open-transfer-popup logestechs-white-btn" data-order-id="<?php echo $order_id; ?>">Assign Order</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            }
            $orders_html = ob_get_clean();

                                                    // Set up pagination.
            $total_pages = ceil( $total_count / 10 ); // Replace YOUR_ITEMS_PER_PAGE with the number of items per page.
            ob_start();
            $pagination_links = paginate_links( [
                'base'      => add_query_arg( 'paged', '%#%', remove_query_arg( ['action', 'paged'] ) ),
                'format'    => '?paged=%#%',
                'prev_text' => __( '&laquo;' ),
                'next_text' => __( '&raquo;' ),
                'total'     => $total_pages,
                'current'   => $current_page,
                'mid_size'  => 1 // Number of pages to display around the current page
            ] );
            if ( $pagination_links ) {
                echo '<div class="logestechs-pagination">';
                echo '<span class="logestechs-pagination-label">Page ' . $current_page . ' of ' . $total_pages . '</span> ';
                echo '<div>' . $pagination_links . '</div>';
                echo '</div>';
            }
            $pagination_links_html = ob_get_clean();
            // Send JSON response.
            wp_send_json( [
                'orders_html'      => $orders_html,
                'pagination_links' => $pagination_links_html,
                'total_count'      => $total_count
            ] );

            wp_die();
        }

        public function fetch_villages() {
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( 'You do not have permission to perform this action.' );
            }

            $order_id = $_POST['order_id'] ? intval( $_POST['order_id'] ) : null;

            $query = isset( $_POST['query'] ) ? sanitize_text_field( $_POST['query'] ) : '';

            // Search the villages based on the query
            $villages = $this->api->search_villages( $order_id, $query );
            // Send JSON response.
            wp_send_json_success( ['villages' => $villages] );
            wp_die();
        }
    }
}
