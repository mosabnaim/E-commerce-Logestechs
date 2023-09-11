<?php
/**
 * The file that handles input sanitization.
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
         * Sanitize input data.
         *
         * @since    1.0.0
         * @param    string|array   $input    The input data to sanitize.
         * @return   string|array             The sanitized input data.
         */
        public function sanitize($input) {
            // If the input data is an array, sanitize each element.
            if (is_array($input)) {
                return array_map(array($this, 'sanitize'), $input);
            }

            // If the input data is a string, sanitize the string.
            return sanitize_text_field($input);
        }

        /**
         * Sanitize and validate the credentials.
         *
         * @since    1.0.0
         * @param    array   $data    The credentials data to sanitize.
         * @return   array             The sanitized credentials.
         */
        public function sanitize_credentials($data) {
            // Ensure that the array keys exist.
            $data = wp_parse_args($data, [
                'domain'     => '',
                'password'   => '',
                'email'      => '',
                'company_id' => null,
            ]);

            // Sanitize each credential element.
            if(! Logestechs_Config::COMPANY_DOMAIN ) {
                $credentials['domain']     = $this->sanitize($data['domain']);
            }
            $credentials['password']   = $this->sanitize($data['password']);
            $credentials['email']      = $this->sanitize($data['email']);
            $credentials['company_id'] = $data['company_id'] ? intval($data['company_id']) : null;

            return $credentials;
        }
    }
}
