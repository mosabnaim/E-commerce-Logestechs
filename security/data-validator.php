<?php
/**
 * The file that handles data validation.
 *
 * This file is used to handle validation of data like Logestechs credentials and orders.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/security
 */

if ( ! class_exists( 'Logestechs_Data_Validator' ) ) {

    class Logestechs_Data_Validator {

        /**
         * Validates the provided credentials.
         *
         * @since    1.0.0
         * @param    array    $credentials   The credentials to validate.
         * @return   array                   An array of error messages.
         */
        public function validate_credentials( $credentials ) {
            $errors = [];

            if ( ! Logestechs_Config::COMPANY_DOMAIN && empty( $credentials['domain'] ) ) {
                $errors[] = __( 'Domain is required.', 'logestechs' );
            }

            if ( empty( $credentials['email'] ) ) {
                $errors[] = __( 'Email is required.', 'logestechs' );
            }

            if ( empty( $credentials['password'] ) ) {
                $errors[] = __( 'Password is required.', 'logestechs' );
            } elseif ( strlen( $credentials['password'] ) < 3 ) {
                $errors[] = __( 'Password should be at least 3 characters long.', 'logestechs' );
            }

            if ( isset( $credentials['company_id'] ) && ! intval( $credentials['company_id'] ) ) {
                $errors[] = __( 'Invalid company ID.', 'logestechs' );
            }

            return $errors;
        }

        /**
         * Validates the provided order.
         *
         * This function performs various checks to validate the integrity of the provided order object.
         * It examines mandatory and custom fields, ensuring they conform to expected formats and values.
         *
         * @since    1.0.0
         * @param    object    $order   The order to validate.
         * @return array                An array of errors, empty if no errors found.
         */
        public function validate_order($order) {
            $errors = [];
        
            // Validate package data
            if (isset($order['pkg'])) {
                $pkg = $order['pkg'];
        
                if (empty($pkg['cod']) || !is_numeric($pkg['cod']) || $pkg['cod'] <= 0) {
                    $errors[] = __('Invalid COD value for the package.', 'logestechs');
                }
                
                if (!isset($pkg['packageItemsToDeliverList']) || !is_array($pkg['packageItemsToDeliverList']) || empty($pkg['packageItemsToDeliverList'])) {
                    $errors[] = __('Package items to deliver list is required.', 'logestechs');
                } else {
                    foreach ($pkg['packageItemsToDeliverList'] as $item) {
                        if (empty($item['name'])) {
                            $errors[] = __('Item name is required.', 'logestechs');
                        }
                        if (empty($item['cod']) || !is_numeric($item['cod']) || $item['cod'] <= 0) {
                            $errors[] = __('Invalid COD value for an item.', 'logestechs');
                        }
                    }
                }
        
                if (isset($pkg['shipmentType']) && $pkg['shipmentType'] === 'BRING') {
                    if (!isset($pkg['toCollectFromReceiver']) && !isset($pkg['toPayToReceiver'])) {
                        $errors[] = __('Either toCollectFromReceiver or toPayToReceiver is required for pickup.', 'logestechs');
                    }
                }
            }
        
            // Validate destination address data
            if (isset($order['destinationAddress'])) {
                $this->validate_address($order['destinationAddress'], 'Destination', $errors);
            }
        
            // Validate origin address data if present
            if (isset($order['originAddress'])) {
                $this->validate_address($order['originAddress'], 'Origin', $errors);
            }
        
            return $errors;
        }
        
        /**
         * Validates the address.
         *
         * @param array $address The address to validate.
         * @return bool|array False if validation fails, sanitized address otherwise.
         */
        public function validate_address($address) {
            $sanitized_address = [];
            
            // Check for mandatory fields like addressLine1
            if (empty($address['addressLine1'])) {
                return false;
            } else {
                $sanitized_address['addressLine1'] = sanitize_text_field($address['addressLine1']);
            }
            
            // Handle cityId, regionId, and villageId
            if (isset($address['cityId']) && isset($address['regionId']) && isset($address['villageId'])) {
                $sanitized_address['cityId'] = intval($address['cityId']);
                $sanitized_address['regionId'] = intval($address['regionId']);
                $sanitized_address['villageId'] = intval($address['villageId']);
            } elseif (isset($address['village']) && is_string($address['village'])) {
                $sanitized_address['village'] = sanitize_text_field($address['village']);
            } else {
                return false;
            }

            return $sanitized_address;
        }
        
    }
}
