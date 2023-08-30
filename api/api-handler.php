<?php

/**
 * The file that interacts with Logestechs API
 *
 * This class defines all code necessary to communicate with Logestechs API.
 * It handles sending requests and receiving responses from the API.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/api
 */

if ( ! class_exists( 'Logestechs_Api_Handler' ) ) {

    class Logestechs_Api_Handler {

        /**
         * Base URL of Logestechs API.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $api_base_url    Base URL for Logestechs API.
         */
        private $api_base_url = 'https://apisv2.logestechs.com/api/';

        /**
         * Instance to handle API errors.
         *
         * @since    1.0.0
         * @access   private
         * @var      object    $api_error_handler   Instance of Logestechs_Api_Error_Handler.
         */
        private $api_error_handler;

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {
            // Initialize properties or dependencies if needed
            $this->api_error_handler = new Logestechs_Api_Error_Handler();
        }

        /**
         * Make a request to the Logestechs API.
         *
         * This function makes an HTTP request to the Logestechs API and returns the response.
         * It includes support for different HTTP methods and handling specific company IDs.
         *
         * @param string $endpoint    The endpoint to request.
         * @param string $method      The HTTP method to use for the request (default 'GET').
         * @param array  $body        The request body parameters (default empty array).
         * @param string $company_id  The company ID for the request (if needed, default null).
         * @return mixed The response from the API as an associative array, or false if an error occurred.
         *
         * @since    1.0.0
         */
        public function request( $endpoint, $method = 'GET', $body = [], $company_id = null ) {
            // Full API URL:
            $url = $this->api_base_url . $endpoint;

            // Arguments setup for wp_remote_request():
            $args = [
                'method'  => $method,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'LanguageCode' => logestechs_get_current_language()
                ]
            ];

            // Include company ID header if provided:
            if ( $company_id ) {
                $args['headers']['company-id'] = $company_id;
            }

            // Handling request body:
            if ( ! empty( $body ) ) {
                if ( $method === 'GET' ) {
                    $url = add_query_arg( $body, $url );
                } else {
                    $args['body'] = json_encode( $body );
                }
            }

            // Making the request using WordPress HTTP API:
            $response = wp_remote_request( $url, $args );

            // Check for WP error:
            if ( is_wp_error( $response ) ) {
                // Additional error handling could be added here if needed
                return false;
            }

            // Return JSON-decoded response as an associative array:

            return json_decode( wp_remote_retrieve_body( $response ), true );
        }

        /**
         * Handle the response from the Logestechs API.
         *
         * This function processes the response from the Logestechs API, handling any errors that occur
         * and returning the processed response. It checks if the response is valid and if there
         * are any errors, and delegates error handling to the api_error_handler if necessary.
         *
         * @param mixed $response The response from the API as an associative array.
         * @return mixed The processed response, or false if an error occurred.
         *
         * @since    1.0.0
         */
        public function handle_response( $response ) {
            // Check for no or invalid response:
            if ( ! $response ) {
                // Additional error handling could be added here if needed.
                return false;
            }

            // Check for an error in the response:
            if ( isset( $response['error'] ) ) {
                // Delegate error handling to the api_error_handler:
                $this->api_error_handler->handle_api_error( $response['error'] );
            }

            return $response;
        }

        /**
         * Cancel an order through the Logestechs API.
         *
         * This function handles the cancellation of an order by sending a request to the Logestechs API.
         * It retrieves the necessary credentials, constructs the request, and sends it to the API.
         *
         * @param int $order_id The order ID to be canceled.
         * @return mixed The response from the API or false if an error occurred.
         *
         * @since    1.0.0
         */
        public function cancel_order( $order_id ) {
            // Retrieve the local company ID:
            $local_company_id = get_post_meta( $order_id, '_logestechs_local_company_id', true );

            // Get the company details:
            $credentials_storage = Logestechs_Credentials_Storage::get_instance();
            $company             = $credentials_storage->get_company( $local_company_id );
            $debugger = new Logestechs_Debugger;
            $debugger->clear()->log([
                $company,
                !$company,
                empty($company),
                is_object($company),
                !empty($company),
            ])->write();
            if(empty($company)) {
                return __( 'Error while processing this action!', 'logestechs' );
            }

            // Get the encryptor instance and decrypt the password:
            $security_manager   = new Logestechs_Security_Manager();
            $encryptor          = $security_manager->get_encryptor();
            $decrypted_password = $encryptor->decrypt( $company->password );

            // Retrieve the Logestechs order ID:
            $logestechs_id = get_post_meta( $order_id, '_logestechs_order_id', true );

            // Make a request to cancel the order:
            $response = $this->request(
                "guests/{$company->company_id}/packages/{$logestechs_id}/cancel/",
                'PUT',
                [
                    'email'    => $company->email,
                    'password' => $decrypted_password
                ],
                $company->company_id
            );

            return $response;
        }

        /**
         * Send a request to print an order on Logestechs.
         *
         * This method sends a POST request to Logestechs to print a specific order
         * and returns the response. It uses the order's company ID and Logestechs ID
         * as parameters in the request.
         *
         * @param int $order_id The ID of the order to print.
         * @return mixed The response from the API, or false if an error occurred.
         *
         * @since    1.0.0
         */
        public function print_order( $order_id ) {
            $company_id    = get_post_meta( $order_id, '_logestechs_api_company_id', true );
            $logestechs_id = get_post_meta( $order_id, '_logestechs_order_id', true );

            $response = $this->request( "guests/{$company_id}/packages/pdf", 'POST', [
                'ids' => [
                    $logestechs_id
                ]
            ], $company_id );

            return $response;
        }

        /**
         * Search villages in Logestechs using a query.
         *
         * This method sends a GET request to Logestechs to search for villages
         * based on a given query and returns the response.
         *
         * @param int $order_id The ID of the order to associate with the search.
         * @param string $query The search query for villages.
         * @return mixed The response from the API, or false if an error occurred.
         *
         * @since    1.0.0
         */
        public function search_villages( $order_id, $query ) {
            $company_id = get_post_meta( $order_id, '_logestechs_api_company_id', true );

            $response = $this->request( 'addresses/villages', 'GET', [
                'search' => $query
            ], $company_id );

            return $response;
        }

        /**
         * Retrieve the company information by the given domain.
         *
         * @param string $domain The domain name to look up.
         * @return array|false The company details including ID, logo URL, and name, or false if an error occurred.
         *
         * @since    1.0.0
         */
        public function get_company_by_domain( $domain ) {
            $response = $this->request( 'guests/companies/info-by-domain/', 'GET', [
                'domain' => $domain
            ] );

            $processed_response = $this->handle_response( $response );
            if ( ! $processed_response || ! isset( $processed_response['id'], $processed_response['logo'], $processed_response['name'] ) ) {
                return false;
            }

            return [
                'company_id' => $processed_response['id'],
                'logo_url'   => $processed_response['logo'],
                'name'       => $processed_response['name'],
                'currency'   => $processed_response['currency'],
            ];
        }

        /**
         * Check the provided credentials against the Logestechs API.
         *
         * @param string $company_id The company ID for the credentials.
         * @param string $email      The email address to check.
         * @param string $password   The password to check.
         * @return mixed|null Error information, or null if no error.
         *
         * @since    1.0.0
         */
        public function check_credentials( $company_id, $email, $password ) {
            $response = $this->request( 'auth/customer/check', 'POST', [
                'companyId' => $company_id,
                'email'     => $email,
                'password'  => $password
            ], $company_id );

            return $response;
        }

        /**
         * Track an order using the Logestechs API.
         *
         * @param int $order_id The ID of the order to track.
         * @return mixed The response from the API, or error handling as necessary.
         *
         * @since    1.0.0
         */
        public function track_order( int $order_id ) {
            $company_id          = get_post_meta( $order_id, '_logestechs_api_company_id', true );
            $logestechs_order_id = get_post_meta( $order_id, '_logestechs_order_id', true );

            $response = $this->request( 'guests/' . $company_id . '/packages/tracking', 'GET', [
                'id'                => $logestechs_order_id,
                'isShowFullHistory' => true
            ], $company_id );

            return $response;
        }

        /**
         * Retrieve the status of multiple orders from Logestechs API.
         *
         * This function takes an array of order IDs, makes a request to Logestechs API to fetch the status
         * of those orders, and returns an associative array mapping each order ID to its status.
         *
         * @param array $order_ids The array of order IDs to fetch the status for.
         * @return array Associative array of order IDs mapped to their corresponding status.
         *
         * @since    1.0.0
         */
        public function get_orders_status( $order_ids = [] ) {
            $response = $this->request( 'guests/packages/status/by-ids', 'POST', [
                'ids' => $order_ids
            ] );

            $statuses_mapping = [
                'DRAFT'                                                => __( 'Draft' ),
                'PENDING_CUSTOMER_CARE_APPROVAL'                       => __( 'Submitted' ),
                'APPROVED_BY_CUSTOMER_CARE_AND_WAITING_FOR_DISPATCHER' => __( 'Ready for dispatching' ),
                'CANCELLED'                                            => __( 'Cancelled' ),
                'ASSIGNED_TO_DRIVER_AND_PENDING_APPROVAL'              => __( 'Assigned to Drivers' ),
                'REJECTED_BY_DRIVER_AND_PENDING_MANGEMENT'             => __( 'Rejected By Drivers' ),
                'ACCEPTED_BY_DRIVER_AND_PENDING_PICKUP'                => __( 'Pending Pickup' ),
                'SCANNED_BY_DRIVER_AND_IN_CAR'                         => __( 'Picked' ),
                'SCANNED_BY_HANDLER_AND_UNLOADED'                      => __( 'Pending Sorting' ),
                'MOVED_TO_SHELF_AND_OUT_OF_HANDLER_CUSTODY'            => __( 'Sorted on Shelves' ),
                'OPENED_ISSUE_AND_WAITING_FOR_MANAGEMENT'              => __( 'Reported to Management' ),
                'DELIVERED_TO_RECIPIENT'                               => __( 'Delivered' ),
                'POSTPONED_DELIVERY'                                   => __( 'Postponed delivery' ),
                'RETURNED_BY_RECIPIENT'                                => __( 'Returned by recipient' ),
                'COMPLETED'                                            => __( 'Completed' ),
                'FAILED'                                               => __( 'Failed' ),
                'RESOLVED_FAILURE'                                     => __( 'Resolved Failure' ),
                'UNRESOLVED_FAILURE'                                   => __( 'Unresolved Failure' ),
                'TRANSFERRED_OUT'                                      => __( 'Transferred out' ),
                'PARTIALLY_DELIVERED'                                  => __( 'Partially delivered' ),
                'SWAPPED'                                              => __( 'Swapped' ),
                'BROUGHT'                                              => __( 'Brought' ),
                'DELIVERED_TO_SENDER'                                  => __( 'Delivered to sender' )
            ];

            $statuses = [];

            foreach ( $response as $item ) {
                if ( ! is_array( $item ) || ! isset( $item['packageId'], $item['status'] ) ) {
                    continue;
                }

                $packageId            = $item['packageId'];
                $status_code          = $item['status'];
                $status               = isset( $statuses_mapping[$status_code] ) ? $statuses_mapping[$status_code] : $status_code;
                $statuses[$packageId] = $status;
            }

            return $statuses;
        }

        /**
         * Transfer an order to Logestechs.
         *
         * This function takes a company object and a WooCommerce order, and makes a request to Logestechs
         * API to transfer the order. It also handles encryption and decryption of sensitive data.
         *
         * @param object   $company The company object containing email, password, and company_id.
         * @param WC_Order $order   The WooCommerce order object to transfer.
         * @return mixed   The response from the Logestechs API.
         *
         * @since    1.0.0
         */
        public function transfer_order_to_logestechs( $company, $order_data ) {
            $security_manager   = new Logestechs_Security_Manager();
            $encryptor          = $security_manager->get_encryptor();
            $decrypted_password = $encryptor->decrypt( $company->password ); // Decrypt the password

            $api_data      = [
                'email'    => $company->email,
                'password' => $decrypted_password
            ];
            $api_data = array_merge( $api_data, $order_data );

            $response = $this->request( 'ship/request/by-email', 'POST', $api_data, $company->company_id );

            return $response;
        }

    }
}
