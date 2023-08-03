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
            // $this->api_base_url = "https://logestechs-api.example.com/";

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
        public function request( $endpoint, $method = 'GET', $body = [] ) {
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

            // If there's data in the $body parameter, add it to the $args array
            if ( ! empty( $body ) ) {
                $args['body'] = json_encode( $body );
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
        

        public function create_order( $order_data ) {
            // Call the 'request' method to create an order on Logestechs
            // $response = $this->request( 'orders', 'POST', $order_data );
            $response['order_id'] = 12;
            if ( $response === false ) {
                // Handle the error
                return false;
            } else {
                // Return the Logestechs order ID
                return $response['order_id'];
            }
        }
        public function cancel_order( $order_id ) {
            // Call the 'request' method to create an order on Logestechs
            // $response = $this->request( 'cancel_order', 'POST', [[ 'order_id' => $order_id ]] );
            $response['order_id'] = $order_id;
            if ( $response === false ) {
                // Handle the error
                return false;
            } else {
                // Return the Logestechs order ID
                return $order_id;
            }
        }
    }
}
