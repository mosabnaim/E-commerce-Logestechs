<?php
/**
 * The file that handles plugin errors
 *
 * This class should be responsible for logging the errors to a specific log file and, if necessary, displaying the error message to the user.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/include
 */

if ( ! class_exists( 'Logestechs_Error_Handler' ) ) {

    class Logestechs_Error_Handler {

        /**
         * Log error messages into a specific log file.
         *
         * @param string $message
         * @param string $severity
         */
        public function log_error( $message, $severity ) {
            // Implement your error logging logic here.
        }

        /**
         * Display error message to the user.
         *
         * @param string $message
         */
        public function display_error( $message ) {
            // Implement your error displaying logic here.
        }
    }
}