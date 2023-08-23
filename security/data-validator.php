<?php
/**
 * The file that handles data validation
 *
 * This file is used to handle validation of data like Logestechs credentials and orders.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/security
 */

if ( ! class_exists( 'Logestechs_Data_Validator' ) ) {

    class Logestechs_Data_Validator {
        public function validate_credentials( $credentials ) {
            $errors = [];

            if ( empty( $credentials['domain'] ) ) {
                $errors[] = 'Domain is required.';
            }

            if ( empty( $credentials['email'] ) ) {
                $errors[] = 'Email is required.';
            }

            if ( empty( $credentials['password'] ) ) {
                $errors[] = 'Password is required.';
            } elseif ( strlen( $credentials['password'] ) < 3 ) {
                $errors[] = 'Password should be at least 3 characters long.';
            }

            if ( isset( $credentials['company_id'] ) && ! intval( $credentials['company_id'] ) ) {
                $errors[] = 'Invalid company ID.';
            }

            return $errors;
        }

        public function validate_order( $order ) {
            $errors = []; // Initialize the errors array
        
            if ( ! isset( $order->id ) || empty( $order->id ) ) {
                $errors[] = 'The Order ID is missing. Please ensure the order is valid.';
            }
        
            // $store_address = get_option( 'woocommerce_store_address' );
            // if ( empty( $store_address ) ) {
            //     $errors[] = 'The store address is not set. You can update this information in the WooCommerce settings.';
            // }
        
            // $store_phone = get_option( 'logestechs_store_phone_number' );
            // if ( empty( $store_phone ) ) {
            //     $errors[] = 'The store phone number is missing. You can add or update it in the WooCommerce settings.';
            // }
        
            // $business_name = get_option( 'logestechs_business_name' );
            // if ( empty( $business_name ) ) {
            //     $errors[] = 'The business name is not provided. You can specify this in the WooCommerce settings.';
            // }
        
            // $store_phone = get_option( 'logestechs_store_owner' );
            // if ( empty( $store_owner ) ) {
            //     $errors[] = 'The store owner\'s information is not available. You can set this information in the WooCommerce settings.';
            // }
        
            return $errors; // Return the array of errors
        }
        
        
    }
}
