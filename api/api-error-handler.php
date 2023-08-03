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

if ( ! class_exists( 'Logestechs_Api_Error_Handler' ) ) {

    // require_once 'path_to_your_plugin_directory/includes/error-handler.php';

    class Logestechs_Api_Error_Handler {

        protected $generalErrorHandler;

        public function __construct() {
            // $this->generalErrorHandler = new Logestechs_Error_Handler();
        }

        /**
         * Handle API specific errors.
         *
         * @param object $api_response
         */
        public function handle_api_error( $api_response ) {
            // Here, you might check the API response for specific error codes/messages.
            // The exact implementation will depend on how Logestechs' API structures its error responses.
            /**
            *if ( $api_response->error_code == 'SPECIFIC_ERROR_CODE' ) {
            *    $this->generalErrorHandler->log_error( 'Specific error message', 'severity' );
            *    $this->generalErrorHandler->display_error( 'Specific error message' );
            *}
            */
            // Add additional error handling logic as needed, for other error codes/messages.
        }
    }
}