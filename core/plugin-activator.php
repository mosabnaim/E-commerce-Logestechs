<?php
/**
 * The file that handles the activation of the plugin
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/core
 */
if ( ! class_exists( 'Logestechs_Plugin_Activator' ) ) {

    class Logestechs_Plugin_Activator {

        public static function activate() {
            self::generate_encryption_key();

            $credentials_storage = Logestechs_Credentials_Storage::get_instance();
            $credentials_storage->create_table();
        }

        private static function generate_encryption_key() {
            // Check if the key is already set.
            if ( ! get_option( 'logestechs_encryption_key' ) ) {
                // Generate a secure random key with a length of 32.
                $encryption_key = wp_generate_password( 32, true, true );
                // Store the key in the options table.
                update_option( 'logestechs_encryption_key', $encryption_key );
            }
        }
    }
}