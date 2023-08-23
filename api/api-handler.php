<?php
/**
 * The file that interacts with Logestechs API
 *
 * This file is used to make requests to the Logestechs API and handle responses.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/api
 */

if ( ! class_exists( 'Logestechs_Api_Handler' ) ) {

    class Logestechs_Api_Handler {

        private $api_base_url;      // Set the Logestechs API base URL here
        private $api_error_handler; // instance of Logestechs_API_Error_Handler

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {
            // Set the Logestechs API base URL
            $this->api_base_url = 'https://apisv2.logestechs.com/api/';

        }

        /**
         * Make a request to the Logestechs API.
         *
         * @param string $endpoint The endpoint to request.
         * @param string $method The HTTP method to use for the request.
         * @param array $body The request body parameters.
         * @return mixed The response from the API.
         */
        public function request( $endpoint, $method = 'GET', $body = [], $company_id = null ) {
            // Make a request to the Logestechs API and return the response
            // e.g. use wp_remote_get() or wp_remote_post() depending on the $method
            // Construct the full API URL:
            $url = $this->api_base_url . $endpoint;

            // Set up the arguments for wp_remote_get() or wp_remote_post():
            $args = [
                'method'  => $method,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'LanguageCode' => $this->get_current_language()
                    // Include any additional headers (e.g., API authentication)
                ]
            ];

            if ( $company_id ) {
                $args['headers']['company-id'] = $company_id;
            }

            // If there's data in the $body parameter, add it to the $args array
            if ( ! empty( $body ) ) {
                if ( $method === 'GET' ) {
                    $url = add_query_arg( $body, $url );
                } else {
                    $args['body'] = json_encode( $body );
                }
            }

            // Use the WordPress HTTP API to make the request:
            $response = wp_remote_request( $url, $args );

            // Check for errors in the response:
            if ( is_wp_error( $response ) ) {
                // Handle the error according to your error handling practices
                // For simplicity, we're returning false here:
                return false;
            }

            // If no errors, return the body of the response:

            return json_decode( wp_remote_retrieve_body( $response ), true );
        }
        function get_current_language() {
            $locale = get_locale();
        
            // Check if the current locale is Arabic
            if ($locale == 'ar') {
                return 'ar';
            }
        
            // Default to English for any other language
            return 'en';
        }
        /**
         * Handle the response from the Logestechs API.
         *
         * @param mixed $response The response from the API.
         * @return mixed The processed response.
         */
        public function handle_response( $response ) {
            // Handle the response from the API call
            // e.g. use $this->api_error_handler->handle_error($response) to handle errors
            if ( ! $response || ! is_array( $response ) ) {
                // No or invalid response.
                return false;
            }
            if ( isset( $response['error'] ) ) {
                // Handle error according to your error handling practices
                // This can include logging the error, displaying a user-friendly message, etc.
                $this->api_error_handler->handle_api_error( $response );

                return false;
            }

            // If everything went well, return the response (or part of it)

            return $response;
        }

        public function cancel_order( $order_id ) {
            $local_company_id = get_post_meta( $order_id, '_logestechs_local_company_id', true );

            $credentials_storage = Logestechs_Credentials_Storage::get_instance();
            $company             = $credentials_storage->get_company( $local_company_id );

            $security_manager   = new Logestechs_Security_Manager();
            $encryptor          = $security_manager->get_encryptor();
            $decrypted_password = $encryptor->decrypt( $company->password ); // Encrypt the password
            $logestechs_id      = get_post_meta( $order_id, '_logestechs_order_id', true );

            // Call the 'request' method to create an order on Logestechs
            // $response = $this->request( 'cancel_order', 'POST', [[ 'order_id' => $order_id ]] );

            $response = $this->request( "guests/{$company->company_id}/packages/{$logestechs_id}/cancel/", 'PUT', [
                'email'    => $company->email,
                'password' => $decrypted_password
            ], $company->company_id );

            return $response;
        }

        public function print_order( $order_id ) {
            $company_id    = get_post_meta( $order_id, '_logestechs_api_company_id', true );
            $logestechs_id = get_post_meta( $order_id, '_logestechs_order_id', true );

            // Call the 'request' method to create an order on Logestechs
            // $response = $this->request( 'cancel_order', 'POST', [[ 'order_id' => $order_id ]] );

            $response = $this->request( "guests/{$company_id}/packages/pdf", 'POST', [
                'ids' => [
                    $logestechs_id
                ]
            ], $company_id );

            return $response;
        }

        public function search_villages( $order_id, $query ) {
            $company_id = get_post_meta( $order_id, '_logestechs_api_company_id', true );

            // Call the 'request' method to create an order on Logestechs
            // $response = $this->request( 'cancel_order', 'POST', [[ 'order_id' => $order_id ]] );

            $response = $this->request( "addresses/villages", 'GET', [
                'search' => $query
            ], $company_id );
            
            return $response;
        }

        public function get_company_by_domain( $domain ) {
            $response = $this->request( 'guests/companies/info-by-domain/', 'GET', [
                'domain' => $domain
            ] );

            $processed_response = $this->handle_response( $response );
            if ( ! $processed_response || ! isset( $processed_response['id'], $processed_response['logo'], $processed_response['name'] ) ) {
                // Return or handle error case
                return false;
            }
            // Return the company_id and logo_url

            return [
                'company_id' => $processed_response['id'],
                'logo_url'   => $processed_response['logo'],
                'name'       => $processed_response['name']
            ];
        }

        public function check_credentials( $company_id, $email, $password ) {
            $response = $this->request( 'auth/customer/check', 'POST', [
                'companyId' => $company_id,
                'email'     => $email,
                'password'  => $password
            ], $company_id );

            return $response['error'] ?? null;
        }

        public function track_order( int $order_id ) {
            $company_id          = get_post_meta( $order_id, '_logestechs_api_company_id', true );
            $logestechs_order_id = get_post_meta( $order_id, '_logestechs_order_id', true );

            $response = $this->request( 'guests/' . $company_id . '/packages/tracking', 'GET', [
                'id'                => $logestechs_order_id,
                'isShowFullHistory' => true
            ], $company_id );

            return $response;
        }

        public function get_orders_status( $order_ids = [] ) {
            $response = $this->request( 'guests/packages/status/by-ids', 'POST', [
                'ids' => $order_ids
            ] );
        
            $statuses_mapping = [
                'DRAFT' => __('Draft'),
                'PENDING_CUSTOMER_CARE_APPROVAL' => __('Submitted'),
                'APPROVED_BY_CUSTOMER_CARE_AND_WAITING_FOR_DISPATCHER' => __('Ready for dispatching'),
                'CANCELLED' => __('Cancelled'),
                'ASSIGNED_TO_DRIVER_AND_PENDING_APPROVAL' => __('Assigned to Drivers'),
                'REJECTED_BY_DRIVER_AND_PENDING_MANGEMENT' => __('Rejected By Drivers'),
                'ACCEPTED_BY_DRIVER_AND_PENDING_PICKUP' => __('Pending Pickup'),
                'SCANNED_BY_DRIVER_AND_IN_CAR' => __('Picked'),
                'SCANNED_BY_HANDLER_AND_UNLOADED' => __('Pending Sorting'),
                'MOVED_TO_SHELF_AND_OUT_OF_HANDLER_CUSTODY' => __('Sorted on Shelves'),
                'OPENED_ISSUE_AND_WAITING_FOR_MANAGEMENT' => __('Reported to Management'),
                'DELIVERED_TO_RECIPIENT' => __('Delivered'),
                'POSTPONED_DELIVERY' => __('Postponed delivery'),
                'RETURNED_BY_RECIPIENT' => __('Returned by recipient'),
                'COMPLETED' => __('Completed'),
                'FAILED' => __('Failed'),
                'RESOLVED_FAILURE' => __('Resolved Failure'),
                'UNRESOLVED_FAILURE' => __('Unresolved Failure'),
                'TRANSFERRED_OUT' => __('Transferred out'),
                'PARTIALLY_DELIVERED' => __('Partially delivered'),
                'SWAPPED' => __('Swapped'),
                'BROUGHT' => __('Brought'),
                'DELIVERED_TO_SENDER' => __('Delivered to sender')
            ];
        
            $statuses = [];
            
            foreach ( $response as $item ) {
                if ( ! is_array( $item ) || !isset($item['packageId'], $item['status']) ) {
                    continue;
                }
        
                $packageId = $item['packageId'];
                $status_code = $item['status'];
        
                $status = isset($statuses_mapping[$status_code]) ? $statuses_mapping[$status_code] : $status_code;
                $statuses[$packageId] = $status;
            }
        
            return $statuses;
        }
        

        public function transfer_order_to_logestechs( $company, WC_Order $order ) {

            $security_manager   = new Logestechs_Security_Manager();
            $encryptor          = $security_manager->get_encryptor();
            $decrypted_password = $encryptor->decrypt( $company->password ); // Encrypt the password

            $order_handler = new Logestechs_Order_Handler();
            $order_data    = $order_handler->get_order_data( $order );
            $api_data      = [
                'email'    => $company->email,
                'password' => $decrypted_password
            ];
            $api_data = array_merge( $api_data, $order_data );
            // Proceed with your API call here with the $api_data

            $response = $this->request( 'ship/request/by-email', 'POST', $api_data, $company->company_id );

            return $response;
        }
    }
}
