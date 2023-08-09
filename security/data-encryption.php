<?php
/**
 * The file that handles data encryption
 *
 * This file is used to handle encryption of sensitive data like Logestechs credentials.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/security
 */

if ( ! class_exists( 'Logestechs_Data_Encryption' ) ) {

    class Logestechs_Data_Encryption {
        private $encryption_key;
        private $encryption_method;
        private static $instance = null;

        public function __construct( $encryption_key, $encryption_method = 'AES-256-CBC' ) {
            $this->encryption_key    = $encryption_key;
            $this->encryption_method = $encryption_method;
        }

        public static function get_instance($encryption_key, $encryption_method = 'AES-256-CBC') {
            if (null === self::$instance) {
                self::$instance = new self($encryption_key, $encryption_method);
            }
            return self::$instance;
        }

        public function encrypt( $password ) {
            $iv        = openssl_random_pseudo_bytes( openssl_cipher_iv_length( $this->encryption_method ) );
            $encrypted = openssl_encrypt( $password, $this->encryption_method, $this->encryption_key, 0, $iv );
            // The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)

            return base64_encode( $encrypted . '::' . $iv );
        }

        public function decrypt( $encrypted ) {
            // Decrypt the data using the $encryption_key and $iv
            list( $encrypted_data, $iv ) = explode( '::', base64_decode( $encrypted ), 2 );

            return openssl_decrypt( $encrypted_data, $this->encryption_method, $this->encryption_key, 0, $iv );
        }
    }
}
