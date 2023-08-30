<?php
/**
 * The file that manages security features.
 *
 * This file handles the security features of the Logestechs plugin, acting as a bridge
 * between the plugin's functionalities and security operations.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/security
 */

if (!class_exists('Logestechs_Security_Manager')) {

    class Logestechs_Security_Manager {

        /**
         * Instance of the Logestechs_Data_Validator class.
         *
         * @since    1.0.0
         * @access   private
         * @var      Logestechs_Data_Validator    $validator    Manages data validation.
         */
        private $validator;

        /**
         * Instance of the Logestechs_Input_Sanitizer class.
         *
         * @since    1.0.0
         * @access   private
         * @var      Logestechs_Input_Sanitizer   $sanitizer    Manages input sanitization.
         */
        private $sanitizer;

        /**
         * Instance of the Logestechs_Data_Encryption class.
         *
         * @since    1.0.0
         * @access   private
         * @var      Logestechs_Data_Encryption   $encryptor    Manages data encryption.
         */
        private $encryptor;

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {
            $this->validator = new Logestechs_Data_Validator();
            $this->sanitizer = new Logestechs_Input_Sanitizer();
            
            $encryption_key = get_option('logestechs_encryption_key');
            if (!$encryption_key) {
                // Handle error if encryption key isn't found. This could be logging the error or generating a new key.
                // For now, we'll just throw an exception.
                throw new Exception('Encryption key not found.');
            }
            
            $this->encryptor = new Logestechs_Data_Encryption($encryption_key);
        }

        /**
         * Retrieve the validator instance.
         *
         * @since    1.0.0
         * @return   Logestechs_Data_Validator   The validator instance.
         */
        public function get_validator() {
            return $this->validator;
        }

        /**
         * Retrieve the sanitizer instance.
         *
         * @since    1.0.0
         * @return   Logestechs_Input_Sanitizer   The sanitizer instance.
         */
        public function get_sanitizer() {
            return $this->sanitizer;
        }

        /**
         * Retrieve the encryptor instance.
         *
         * @since    1.0.0
         * @return   Logestechs_Data_Encryption   The encryptor instance.
         */
        public function get_encryptor() {
            return $this->encryptor;
        }
    }
}
