<?php
/**
 * The file that handles security
 *
 * This file is used to manage security features of the Logestechs plugin.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/security
 */

if (!class_exists('Logestechs_Security_Manager')) {

    class Logestechs_Security_Manager {

        private $validator; // Logestechs_Data_Validator instance
        private $sanitizer; // Logestechs_Input_Sanitizer instance
        private $encryptor; // Logestechs_Data_Encryption instance

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {
            // Initialize validator, sanitizer and encryptor
            // $this->validator = new Logestechs_Data_Validator();
            // $this->sanitizer = new Logestechs_Input_Sanitizer();
            // $this->encryptor = new Logestechs_Data_Encryption();
        }

        /**
         * Get the validator instance.
         *
         * @return Logestechs_Data_Validator The validator instance.
         */
        public function get_validator() {
            return $this->validator;
        }

        /**
         * Get the sanitizer instance.
         *
         * @return Logestechs_Input_Sanitizer The sanitizer instance.
         */
        public function get_sanitizer() {
            return $this->sanitizer;
        }

        /**
         * Get the encryptor instance.
         *
         * @return Logestechs_Data_Encryption The encryptor instance.
         */
        public function get_encryptor() {
            return $this->encryptor;
        }
    }
}
