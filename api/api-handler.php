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

        private $api_base_url; // Set the Logestechs API base URL here
        private $api_error_handler; // instance of Logestechs_API_Error_Handler

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {
            // Set the Logestechs API base URL
            $this->api_base_url = 'https://apisv2.logestechs.com/api/';

            // Initialize the API error handler
            $this->api_error_handler = new Logestechs_Api_Error_Handler();
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
                    'Content-Type' => 'application/json'
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
            $local_company_id = get_post_meta( $order_id, 'logestechs_local_company_id', true );

            $credentials_storage = Logestechs_Credentials_Storage::get_instance();
            $company             = $credentials_storage->get_company( $local_company_id );

            $security_manager   = new Logestechs_Security_Manager();
            $encryptor          = $security_manager->get_encryptor();
            $decrypted_password = $encryptor->decrypt( $company->password ); // Encrypt the password
            $logestechs_id      = get_post_meta( $order_id, 'logestechs_order_id', true );

            // Call the 'request' method to create an order on Logestechs
            // $response = $this->request( 'cancel_order', 'POST', [[ 'order_id' => $order_id ]] );

            $response = $this->request( "guests/{$company->company_id}/packages/{$logestechs_id}/cancel/", 'PUT', [
                'email'    => $company->email,
                'password' => $decrypted_password
            ], $company->company_id );

            return empty( $response );
        }

        public function print_order( $order_id ) {
            $company_id    = get_post_meta( $order_id, 'logestechs_api_company_id', true );
            $logestechs_id = get_post_meta( $order_id, 'logestechs_order_id', true );

            // Call the 'request' method to create an order on Logestechs
            // $response = $this->request( 'cancel_order', 'POST', [[ 'order_id' => $order_id ]] );

            $response = $this->request( "guests/{$company_id}/packages/pdf", 'POST', [
                'ids' => [
                    $logestechs_id
                ]
            ], $company_id );

            return $response;
        }

        public function get_company_by_domain( $domain ) {
            $response = $this->request( 'guests/companies/info-by-domain/', 'GET', [
                'domain' => $domain
            ] );

            $api_handler        = new Logestechs_Api_Handler();
            $processed_response = $api_handler->handle_response( $response );
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

            return ! isset( $response['error'] );
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
