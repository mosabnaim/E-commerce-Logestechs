<?php
/**
 * The file that handles API errors
 *
 * This is used to handle any errors that come from the Logestechs API calls.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/api
 */

if (!class_exists('Logestechs_API_Error_Handler')) {

    class Logestechs_API_Error_Handler {

        /**
         * Handle errors from the Logestechs API.
         *
         * @param object $response The response object from the API.
         * @return void
         */
        public function handle_error($response) {
            // Here you can handle the API errors. The $response parameter is
            // the response from the API call.

            // Example of handling a WP_Error
            // if (is_wp_error($response)) {
            //     $error_message = $response->get_error_message();
            //     echo "Something went wrong: $error_message";
            // } else {
            //     // Handle other types of errors
            // }
        }
    }

}
