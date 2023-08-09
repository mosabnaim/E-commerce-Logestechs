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
            if ( ! $order->id ) {
                $errors[] = 'Order Id is required';
            }

            return $errors;
        }
    }
}
