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
            $this->api              = new Logestechs_Api_Handler();
            $this->woocommerce_list = new Logestechs_Woocommerce_List_View();

            // Hook into WordPress actions and filters
            add_filter( 'bulk_actions-edit-shop_order', [$this, 'custom_bulk_actions'], 20, 1 );
            add_filter( 'manage_edit-shop_order_columns', [$this->woocommerce_list, 'add_custom_column_header'], 20 );
            add_action( 'manage_shop_order_posts_custom_column', [$this->woocommerce_list, 'add_custom_column_data'], 20, 2 );
            add_action( 'wp_ajax_logestechs_assign_company', [$this, 'assign_company'] );
            add_action( 'wp_ajax_logestechs_prepare_order_popup', [$this, 'prepare_order_popup'] );
            add_action( 'wp_ajax_logestechs_print_order', [$this, 'print_order'] );
            add_action( 'wp_ajax_logestechs_cancel_order', [$this, 'cancel_order'] );
            add_action( 'wp_ajax_logestechs_fetch_order_details', [$this, 'track_order'] );
            add_action( 'wp_ajax_logestechs_sync_orders_status', [$this, 'sync_orders_status'] );
            add_action( 'wp_ajax_logestechs_get_orders', [$this, 'load_orders'] );
            add_action( 'wp_ajax_logestechs_fetch_villages', [$this, 'fetch_villages'] );
        }
        function custom_bulk_actions( $bulk_actions ) {
            $bulk_actions['logestechs_bulk_transfer'] = sprintf(__('Bulk Transfer to %s', 'logestechs'), Logestechs_Config::PLUGIN_NAME);
            return $bulk_actions;
        }
          
        /**
         * Get transferred orders.
         *
         * @param int $items_per_page The number of items per page.
         * @since 1.0.0
         * @return array The list of transferred orders and the total count.
         */
        public function get_transferred_orders( $items_per_page = 10 ) {
            global $wpdb;

            if ( ! function_exists( 'wc_get_order_statuses' ) ) {
                return ['orders' => [], 'total_count' => 0];
            }

            $paged           = isset( $_POST['paged'] ) ? intval( $_POST['paged'] ) : 1;
            $search          = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
            $date_from_input = isset( $_POST['date_from'] ) ? sanitize_text_field( $_POST['date_from'] ) : '';
            $date_to_input   = isset( $_POST['date_to'] ) ? sanitize_text_field( $_POST['date_to'] ) : '';
            $status_filter    = isset( $_POST['status_filter'] ) ? sanitize_text_field( $_POST['status_filter'] ) : '';

            $date_from = strtotime( $date_from_input . ' 00:00:00' );
            $date_to   = strtotime( $date_to_input . ' 23:59:59' );

            $sort_by    = isset( $_POST['sort_by'] ) ? sanitize_text_field( $_POST['sort_by'] ) : 'date';
            $sort_order = isset( $_POST['sort_order'] ) ? sanitize_text_field( $_POST['sort_order'] ) : 'ASC';
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
                    
            // Add status filter if provided
            if ($status_filter) {
                $base_query .= $wpdb->prepare(' AND meta_status.meta_value = %s', $status_filter);
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
        
        /**
         * Get unique Logestechs order statuses.
         *
         * Fetches unique values for the '_logestechs_order_status' meta key.
         * These values can be used for filtering orders in a dropdown.
         *
         * @since 1.0.0
         * @return array The array containing unique order statuses.
         */
        function get_unique_order_statuses() {
            global $wpdb;

            $query = "
                SELECT DISTINCT meta_value 
                FROM {$wpdb->postmeta} 
                WHERE meta_key = '_logestechs_order_status'
                AND meta_value IS NOT NULL
            ";
        
            $results = $wpdb->get_col($query);

            return $results;
        }


        /**
         * Handle the custom order cancellation action.
         *
         * @param object $order The WooCommerce order object.
         * @since 1.0.0
         */
        public function handle_cancel_order_action( $order ) {
            // Cancel the order on Logestechs
            $this->cancel_order( $order->get_id() );
        }

        /**
         * Add a custom order action to WooCommerce's order actions dropdown.
         *
         * @since    1.0.0
         * @param array $actions Current actions.
         * @return array Modified actions.
         */
        public function add_cancel_order_action( $actions ) {
            $actions['logestechs_cancel_order'] = __( 'Cancel Order on Logestechs', 'logestechs' );

            return $actions;
        }

        /**
         * Cancel a Logestechs order.
         *
         * @since    1.0.0
         */
        public function cancel_order() {
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( __( 'You do not have permission to perform this action.', 'logestechs' ) );
            }
            $order_id = $_POST['order_id'] ? intval( $_POST['order_id'] ) : null;
            if ( ! $order_id ) {
                wp_send_json_error( __( 'Error while processing this action!', 'logestechs' ) );
                wp_die();
            }
            $order_status = get_post_meta( $order_id, '_logestechs_order_status', true );
            if ( in_array($order_status, Logestechs_Config::COMPLETED_STATUS) ) {
                wp_send_json_error( __( 'This order already finished.', 'logestechs' ) );
                wp_die();
            }
            $response = $this->api->cancel_order( $order_id );

            if ( $response ) {
                wp_send_json_error( $response );
                die();
            }

            update_post_meta( $order_id, '_logestechs_order_status', 'CANCELLED' );
            die();
        }

        /**
         * Print a Logestechs order.
         *
         * @since    1.0.0
         */
        public function print_order() {
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( __( 'You do not have permission to perform this action.', 'logestechs' ) );
            }
            $order_ids = $_POST['order_ids'] ? $_POST['order_ids'] : null;
            if ( ! $order_ids ) {
                wp_send_json_error( __( 'Error while processing this action!', 'logestechs' ) );
                wp_die();
            }
            $response = $this->api->print_order( $order_ids );
            if ( $response ) {
                wp_send_json_success( $response );
            }
            die();
        }

        /**
         * Transfer order to Logestechs.
         *
         * @since    1.0.0
         */
        public function assign_company() {
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( __( 'You do not have permission to perform this action.', 'logestechs' ) );
            }

            $post_data_keys = [
                'company_id', 'destination', 'requesting_pickup',
                'logestechs_store_village_id',
                'logestechs_store_city_id',
                'logestechs_store_region_id',
                'logestechs_business_name',
                'logestechs_store_owner',
                'logestechs_store_phone_number',
                'logestechs_store_address',
                'logestechs_store_address_2',
                'logestechs_custom_store'
            ];

            $sanitized_data = [];
            foreach ( $post_data_keys as $key ) {
                $sanitized_data[$key] = $_POST[$key] ?? null;
            }

            if ( ! $sanitized_data['company_id'] || ! $sanitized_data['destination'] ) {
                wp_send_json_error( __( 'Error while processing this action!', 'logestechs' ) );
                wp_die();
            }

            if ( count($sanitized_data['destination']) > 20 ) {
                wp_send_json_error( __( 'Maximum orders allowed to transfer at once is 20', 'logestechs' ) );
                wp_die();
            }
           
            foreach ($sanitized_data['destination'] as $order_id => $address) {
                $sanitized_data['logestechs_destination_village_id'] = $address['village_id'] ?? '';
                $sanitized_data['logestechs_destination_region_id'] = $address['region_id'] ?? '';
                $sanitized_data['logestechs_destination_city_id'] = $address['city_id'] ?? '';
                $this->process_order_transfer($sanitized_data, $order_id);
            }

            wp_send_json_success( isset( $response['barcode'] ) );
        }
        public function process_order_transfer($sanitized_data, $order_id) {
            $order_status = get_post_meta( $order_id, '_logestechs_order_status', true );

            if ( !empty($order_status) && !in_array($order_status, Logestechs_Config::ACCEPTABLE_TRANSFER_STATUS) ) {
                wp_send_json_error( __( 'This order already transferred', 'logestechs' ) );
                wp_die();
            }

            $security_manager = new Logestechs_Security_Manager();
            $sanitizer        = $security_manager->get_sanitizer();

            // Retrieve the order
            $order = wc_get_order( $order_id );
            $order_data = $this->get_order_data( $order, $sanitized_data );

            $order_data = $sanitizer->sanitize_order( $order_data );
            $validator = $security_manager->get_validator();
            $errors    = $validator->validate_order( $order_data );

            if ( ! empty( $errors ) ) {
                wp_send_json_error( ['errors' => $errors] );
                wp_die();
            }

            if ( $sanitized_data['logestechs_custom_store'] ) {
                $store_keys = [
                    'logestechs_store_village_id',
                    'logestechs_store_city_id',
                    'logestechs_store_region_id',
                    'logestechs_business_name',
                    'logestechs_store_owner',
                    'logestechs_store_phone_number',
                    'logestechs_store_address',
                    'logestechs_store_address_2',
                    'logestechs_custom_store'
                ];

                foreach ( $store_keys as $key ) {
                    if(!empty($sanitized_data[$key])) {
                        $order->update_meta_data( '_' . $key, $sanitized_data[$key] );
                    }
                }
            }
            $destination_keys = [
                'logestechs_destination_village_id',
                'logestechs_destination_region_id',
                'logestechs_destination_city_id'
            ];

            foreach ( $destination_keys as $key ) {
                if(!empty($sanitized_data[$key])) {
                    $meta_key = '_' . $key;
                    $order->update_meta_data( $meta_key, $sanitized_data[$key] );
                }
            }

            $credentials_storage = Logestechs_Credentials_Storage::get_instance();
            $company             = $credentials_storage->get_company( $sanitized_data['company_id'] );

            // Call your API handler to save the order
            $response = $this->api->transfer_order_to_logestechs( $company, $order_data );

            if ( ! $response ) {
                wp_send_json_error( ['errors' => [__( 'Error while processing this action!', 'logestechs' )]] );
                wp_die();
            }
            if ( isset( $response['error'] ) ) {
                wp_send_json_error( ['errors' => [$response['error']]] );
                wp_die();
            }
            $date = new DateTime();                                     // Current date and time
            $date->setTimezone( new DateTimeZone( wp_timezone_string() ) ); // Set WordPress timezone
            $timestamp = $date->getTimestamp(); // Get Unix timestamp
            // Check if the order creation was successful
            if ( isset( $response['barcode'] ) ) {
                
                // Save the updated meta data
                $order->save();

                // Store the fact that the order has been transferred, along with the Logestechs order ID
                update_post_meta( $order_id, '_logestechs_order_barcode', $response['barcode'] );
                update_post_meta( $order_id, '_logestechs_order_id', $response['id'] );
                update_post_meta( $order_id, '_logestechs_company_name', $company->company_name );
                update_post_meta( $order_id, '_logestechs_api_company_id', $company->company_id );
                update_post_meta( $order_id, '_logestechs_currency', $company->currency );
                update_post_meta( $order_id, '_logestechs_local_company_id', $sanitized_data['company_id'] );
                update_post_meta( $order_id, '_logestechs_date', $timestamp );
                update_post_meta( $order_id, '_logestechs_order_status', 'REQUESTED' );
            }

            return isset( $response['barcode'] );
        }
        /**
         * Prepare order for Logestechs popup.
         *
         * @since    1.0.0
         */
        public function prepare_order_popup() {
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( __( 'You do not have permission to perform this action.', 'logestechs' ) );
            }

            $selected_orders = $_POST['selected_orders'] ?$_POST['selected_orders'] : null;

            if ( ! $selected_orders) {
                wp_send_json_error( __( 'Error while processing this action!', 'logestechs' ) );
                wp_die();
            }

            $addresses = [];
            foreach ($selected_orders as $order_id) {
                $order = wc_get_order( $order_id );
                $addresses[$order_id] = $order->get_formatted_shipping_address();
            }
            wp_send_json_success( $addresses );

            wp_die();
        }

        /**
         * Track a Logestechs order.
         *
         * @since    1.0.0
         */
        public function track_order() {
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            // Ensure the user has the necessary capability.
            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( __( 'You do not have permission to perform this action.', 'logestechs' ) );
            }

            // Validate and fetch order ID.
            $order_id = isset( $_POST['order_id'] ) ? intval( $_POST['order_id'] ) : null;

            if ( ! $order_id ) {
                wp_send_json_error( __( 'Error while processing this action!', 'logestechs' ) );
                wp_die();
            }

            // Get company currency symbol.
            $currency_symbol = get_post_meta($order_id, '_logestechs_currency', true);

            // Make a request to the Logestechs API for order tracking details.
            $response = $this->api->track_order( $order_id );

            if ( ! $response || ! isset( $response['barcode'] ) ) {
                wp_send_json_error( __( 'Unable to retrieve order details from Logestechs.', 'logestechs' ) );
                wp_die();
            }

            // Extract and format order details.
            $created_date  = logestechs_convert_to_local_time( $response['createdDate'] );
            $expected_date = logestechs_convert_to_local_time( $response['expectedDeliveryDate'] );

            $details_to_display = [
                'order_id'               => $order_id,
                'package_number'         => '#' . $response['barcode'],
                'price'                  => $response['cost'] . ' ' . $currency_symbol,
                'reservation_date'       => ! empty( $created_date ) ? $created_date->format( 'd/m/Y' ) : __( 'N/A', 'logestechs' ),
                'shipment_type'          => $response['shipmentType'],
                'recipient'              => $response['receiverFirstName'] . ' ' . $response['receiverLastName'],
                'package_weight'         => ! empty( $response['weight'] ) ? $response['weight'] : __( 'N/A', 'logestechs' ),
                'expected_delivery_date' => ! empty( $expected_date ) ? $expected_date->format( 'd/m/Y' ) : __( 'N/A', 'logestechs' ),
                'phone_number'           => $response['receiverPhone']
            ];
            // Compile tracking data.
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

            // Send the prepared data.
            wp_send_json( $details_to_display );
            wp_die();
        }

        /**
         * Retrieve and prepare order data for Logestechs API.
         *
         * This function retrieves data from a WooCommerce order and prepares it to be sent to Logestechs API.
         *
         * @param WC_Order $order The WooCommerce order object.
         * @return array The prepared order data.
         *
         * @since 1.0.0
         */
        public function get_order_data( WC_Order $order, $form_data ) {
            // Get WooCommerce Order ID
            $order_id = $order->get_id();

            // Prepare to retrieve metadata keys for Logestechs.
            $relevant_store_keys = [
                'logestechs_store_village_id',
                'logestechs_store_city_id',
                'logestechs_store_region_id',
                'logestechs_business_name',
                'logestechs_store_owner',
                'logestechs_store_phone_number',
                'logestechs_store_address',
                'logestechs_store_address_2',
                'logestechs_custom_store',
                'logestechs_destination_village_id',
                'logestechs_destination_region_id',
                'logestechs_destination_city_id'
            ];

            $package_data = [];
            foreach ($relevant_store_keys as $key) {
                if (isset($form_data[$key])) {
                    $package_data[$key] = $form_data[$key];
                }
            }
            $requesting_pickup = $form_data['requesting_pickup'] ?? false;
            // Calculate total items quantity.
            $quantity = array_sum( array_map( function ( $item ) {
                return $item->get_quantity();
            }, $order->get_items() ) );

            // Compile package items data.
            $package_items = array_map( function ( $item ) {
                $product = $item->get_product();

                return [
                    'name' => $item->get_quantity() . 'x ' .$product->get_name(),
                    'cod'  => $product->get_price() * $item->get_quantity()
                ];
            }, $order->get_items() );

            // Build order data for Logestechs.
            $order_data = [
                'pkg' => [
                    'receiverName'              => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
                    'cod'                       => $order->get_total(),
                    'notes'                     => $order->get_customer_note(),
                    'packageItemsToDeliverList' => array_values($package_items),
                    'receiverPhone'             => $order->get_billing_phone(),
                    'receiverPhone2'            => '',
                    'serviceType'               => 'STANDARD',
                    'shipmentType'              => $requesting_pickup? 'BRING' : 'COD',
                    'quantity'                  => $quantity,
                    'description'               => "Order ID: {$order_id}",
                    'integrationSource'         => 'WOOCOMMERCE',
                    'supplierInvoice'           => $order_id,
                ],
                'pkgUnitType'   => 'METRIC',
            ];

            // Incorporate the retrieved metadata into the $order_data array.
            if (!empty($package_data['logestechs_custom_store'])) {
                $order_data['pkg']['senderName']         = $package_data['logestechs_store_owner'];
                $order_data['pkg']['businessSenderName'] = $package_data['logestechs_business_name'];
                $order_data['pkg']['senderPhone']        = $package_data['logestechs_store_phone_number'];
        
                $order_data['originAddress'] = [
                    'addressLine1' => $package_data['logestechs_store_address'],
                    'addressLine2' => $package_data['logestechs_store_address_2'],
                    'cityId'       => intval( $package_data['logestechs_store_city_id'] ),
                    'regionId'     => intval( $package_data['logestechs_store_region_id'] ),
                    'villageId'    => intval( $package_data['logestechs_store_village_id'] )
                ];
            }
            
            $order_data['destinationAddress'] = [
                'addressLine1' => trim( $order->get_shipping_address_1() . ' - ' . $order->get_shipping_address_2() ),
                'cityId'       => intval( $package_data['logestechs_destination_city_id'] ),
                'regionId'     => intval( $package_data['logestechs_destination_region_id'] ),
                'villageId'    => intval( $package_data['logestechs_destination_village_id'] )
            ];

            return $order_data;
        }

        /**
         * Sync order statuses with Logestechs.
         *
         * @since    1.0.0
         */
        public function sync_orders_status() {
            // Check nonce for security.
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            // Check user capabilities.
            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( __( 'You do not have permission to perform this action.', 'logestechs' ) );
                wp_die();
            }

            // Initialize API handler.
            $api_handler = new Logestechs_Api_Handler();

            // Fetch transferred orders.
            $orders = $this->get_transferred_orders()['orders'];

            // Extract WordPress order IDs and corresponding logestechs_order_id.
            $logestechs_order_ids = array_reduce( $orders, function ( $carry, $order_post ) {
                $order_id         = $order_post->ID;
                $carry[$order_id] = get_post_meta( $order_id, '_logestechs_order_id', true );

                return $carry;
            }, [] );

            // Fetch statuses from Logestechs.
            $statuses = $api_handler->get_orders_status( array_values( $logestechs_order_ids ) );

            $statuses_mapping = Logestechs_Config::STATUS_ARRAY;
            
            // Update post meta and store updated statuses.
            $updated_statuses = array_reduce( array_keys( $logestechs_order_ids ), function ( $carry, $order_id ) use ( $statuses, $logestechs_order_ids, $statuses_mapping ) {
                $logestechs_order_id = $logestechs_order_ids[$order_id];
                if ( isset( $statuses[$logestechs_order_id] ) ) {
                    $status = $statuses[$logestechs_order_id];
                    update_post_meta( $order_id, '_logestechs_order_status', $status );
                    $carry[$order_id] = $status;
                }

                return $carry;
            }, [] );

            // Respond with updated statuses.
            wp_send_json( $updated_statuses );
            wp_die();
        }

        /**
         * Load and display transferred orders in the admin panel.
         *
         * @since    1.0.0
         */
        public function load_orders() {
            // Check nonce for security.
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            // Check user capabilities.
            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( __( 'You do not have permission to perform this action.', 'logestechs' ) );
                wp_die();
            }

            // Get the requested page number.
            $current_page = isset( $_POST['paged'] ) ? intval( $_POST['paged'] ) : 1;

            // Fetch transferred orders.
            $orders_array = $this->get_transferred_orders();
            $orders       = $orders_array['orders'];
            $total_count  = $orders_array['total_count'];

            // Begin HTML output buffer.
            ob_start();
            if ( empty( $orders ) ) {
                ?>
                <tr>
                    <td colspan="6"><?php _e( 'No orders found.', 'logestechs' );?></td>
                </tr>
                <?php
            } else {
                foreach ( $orders as $order_post ) {
                    $order         = wc_get_order( $order_post );
                    $order_id      = $order->get_id();
                    $order_barcode = get_post_meta( $order_id, '_logestechs_order_barcode', true );
                    $company_name  = get_post_meta( $order_id, '_logestechs_company_name', true );
                    $date          = logestechs_convert_to_local_time( get_post_meta( $order_id, '_logestechs_date', true ) );
                    ?>
                    <tr class="js-logestechs-order" data-order-id="<?php echo $order_id; ?>">
                        <td><input type="checkbox" name="selected_orders"></td>
                        <td>
                            <a href="<?php echo esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ); ?>">
                                #<?php echo esc_html( $order_id ); ?>
                            </a>
                        </td>
                        <td><?php echo $date->format( 'd/m/Y H:i' ); ?></td>
                        <td>#<?php echo esc_html( $order_barcode ); ?></td>
                        <td class="logestechs-company-name"><?php echo esc_html( $company_name ); ?></td>
                        <td>
                            <span class="js-logestechs-status-cell"><div class="logestechs-skeleton-loader"></div></span>
                        </td>
                        <td>
                            <div class="logestechs-dropdown">
                                <img src="<?php echo logestechs_image( 'dots.svg' ); ?>" />
                                <div class="logestechs-dropdown-content js-normal-dropdown">
                                    <div class="js-logestechs-print" data-order-id="<?php echo $order_id; ?>"><?php _e( 'Print Invoice', 'logestechs' );?></div>
                                    <div class="js-open-details-popup" data-order-id="<?php echo $order_id; ?>"><?php _e( 'Track', 'logestechs' );?></div>
                                    <div class="js-logestechs-cancel" data-order-id="<?php echo $order_id; ?>"><?php _e( 'Cancel', 'logestechs' );?></div>
                                </div>
                                <div class="logestechs-dropdown-content js-cancelled-dropdown hidden">
                                    <div class="js-logestechs-print" data-order-id="<?php echo $order_id; ?>"><?php _e( 'Print Invoice', 'logestechs' );?></div>
                                    <div class="js-open-transfer-popup logestechs-white-btn" data-order-id="<?php echo $order_id; ?>"><?php _e( 'Assign Order', 'logestechs' );?></div>
                                    <div class="js-open-pickup-popup js-logestechs-request-return logestechs-white-btn" data-order-id="<?php echo $order_id; ?>"><?php _e( 'Request Pickup', 'logestechs' );?></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            }
            $orders_html = ob_get_clean();

                                  // Set up pagination.
            $items_per_page = 10; // or whatever constant you want
            $total_pages    = ceil( $total_count / $items_per_page );
            ob_start();
            $pagination_links = paginate_links( [
                'base'      => add_query_arg( 'paged', '%#%', remove_query_arg( ['action', 'paged'] ) ),
                'format'    => '?paged=%#%',
                'prev_text' => __( '&laquo;', 'logestechs' ),
                'next_text' => __( '&raquo;', 'logestechs' ),
                'total'     => $total_pages,
                'current'   => $current_page,
                'mid_size'  => 1 // Number of pages to display around the current page
            ] );
            if ( $pagination_links ) {
                echo '<div class="logestechs-pagination">';
                echo '<span class="logestechs-pagination-label">' . sprintf( __( 'Page %1$s of %2$s', 'logestechs' ), $current_page, $total_pages ) . '</span> ';
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

        /**
         * Fetch villages based on the query from the AJAX request.
         *
         * @since    1.0.0
         */
        public function fetch_villages() {
            // Check nonce for security.
            check_ajax_referer( 'logestechs-security-nonce', 'security' );

            // Check user capabilities.
            if ( ! current_user_can( 'manage_woocommerce' ) ) {
                wp_send_json_error( __( 'You do not have permission to perform this action.', 'logestechs' ) );
                wp_die();
            }

            // Sanitize and validate input.
            $company_id = isset( $_POST['company_id'] ) ? intval( $_POST['company_id'] ) : null;
            $query    = isset( $_POST['query'] ) ? sanitize_text_field( $_POST['query'] ) : '';

            try {
                // Search the villages based on the query.
                $villages = $this->api->search_villages(  $query, $company_id );
                // Send JSON response.
                wp_send_json_success( ['villages' => $villages] );
            } catch ( Exception $e ) {
                wp_send_json_error( __( 'There was an error fetching the villages. Please try again.', 'logestechs' ) );
            } finally {
            wp_die();
            wp_die();
                wp_die();
            }
        }

    }
}
