<?php
/**
 * The file that handles input sanitization
 *
 * This file is used to handle sanitization of inputs before processing them.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/security
 */

if (!class_exists('Logestechs_Input_Sanitizer')) {

    class Logestechs_Input_Sanitizer {

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {
        }

        /**
         * Sanitize input.
         *
         * @param string|array $input The input to sanitize.
         * @return string|array The sanitized input.
         */
        public function sanitize($input) {
            // Implement input sanitization
            // Sanitize strings using a function like sanitize_text_field()
            // Sanitize arrays recursively
            return is_array($input) ? array_map(array($this, 'sanitize'), $input) : sanitize_text_field($input);
        }

        public function sanitize_credentials($data) {
            $credentials['domain']     = $this->sanitize( $data['domain'] );
            $credentials['password']   = $this->sanitize( $data['password'] );
            $credentials['email']      = $this->sanitize( $data['email'] );
            $credentials['company_id'] = $data['company_id'] ? intval( $data['company_id'] ) : null;

            return $credentials;
        }
    }
}
