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

        /**
         * The encryption key.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $encryption_key    The encryption key.
         */
        private $encryption_key;

        /**
         * The encryption method.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $encryption_method    The encryption method.
         */
        private $encryption_method;

        /**
         * The instance of this class.
         *
         * @since    1.0.0
         * @access   private
         * @var      Logestechs_Data_Encryption    $instance    The single instance of this class.
         */
        private static $instance = null;

        /**
         * Constructor for the encryption class.
         *
         * @since    1.0.0
         * @param    string    $encryption_key        The encryption key.
         * @param    string    $encryption_method     The encryption method.
         */
        public function __construct( $encryption_key, $encryption_method = 'AES-256-CBC' ) {
            $this->encryption_key    = $encryption_key;
            $this->encryption_method = $encryption_method;
        }

        /**
         * Returns the instance of this class.
         *
         * @since    1.0.0
         * @param    string    $encryption_key        The encryption key.
         * @param    string    $encryption_method     The encryption method.
         * @return   Logestechs_Data_Encryption       The single instance of this class.
         */
        public static function get_instance($encryption_key, $encryption_method = 'AES-256-CBC') {
            if (null === self::$instance) {
                self::$instance = new self($encryption_key, $encryption_method);
            }
            return self::$instance;
        }

        /**
         * Encrypts the provided password.
         *
         * @since    1.0.0
         * @param    string    $password     The password to encrypt.
         * @return   string                  The encrypted password.
         */
        public function encrypt( $password ) {
            $iv        = openssl_random_pseudo_bytes( openssl_cipher_iv_length( $this->encryption_method ) );
            $encrypted = openssl_encrypt( $password, $this->encryption_method, $this->encryption_key, 0, $iv );

            // The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
            return base64_encode( $encrypted . '::' . $iv );
        }

        /**
         * Decrypts the provided encrypted password.
         *
         * @since    1.0.0
         * @param    string    $encrypted   The encrypted password.
         * @return   string                 The decrypted password.
         */
        public function decrypt( $encrypted ) {
            // Decrypt the data using the $encryption_key and $iv
            list( $encrypted_data, $iv ) = explode( '::', base64_decode( $encrypted ), 2 );

            return openssl_decrypt( $encrypted_data, $this->encryption_method, $this->encryption_key, 0, $iv );
        }

    }

}
