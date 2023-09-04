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

            if ( empty( $credentials['domain'] ) ) {
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
        public function validate_order( $order ) {
            $errors = [];
        
            // Validate package data
            if ( isset($order['pkg']) ) {
                $pkg = $order['pkg'];
                if ( empty($pkg['receiverName']) ) {
                    $errors[] = __('Receiver name is required.', 'logestechs');
                }
                if ( empty($pkg['cod']) || ! is_numeric($pkg['cod']) || $pkg['cod'] <= 0 ) {
                    $errors[] = __('Invalid COD value for the package.', 'logestechs');
                }
                if ( empty($pkg['receiverPhone']) ) {
                    $errors[] = __('Invalid receiver phone number.', 'logestechs');
                }
                if ( ! isset($pkg['packageItemsToDeliverList']) || ! is_array($pkg['packageItemsToDeliverList']) || empty($pkg['packageItemsToDeliverList']) ) {
                    $errors[] = __('Package items to deliver list is required.', 'logestechs');
                } else {
                    foreach ($pkg['packageItemsToDeliverList'] as $item) {
                        if ( empty($item['name']) ) {
                            $errors[] = __('Item name is required.', 'logestechs');
                        }
                        if ( empty($item['cod']) || ! is_numeric($item['cod']) || $item['cod'] <= 0 ) {
                            $errors[] = __('Invalid COD value for an item.', 'logestechs');
                        }
                    }
                }
            }
        
            // Validate destination address data
            if ( isset($order['destinationAddress']) ) {
                $destinationAddress = $order['destinationAddress'];
                if ( empty($destinationAddress['addressLine1']) ) {
                    $errors[] = __('Destination address line 1 is required.', 'logestechs');
                }
                if ( empty($destinationAddress['cityId']) || ! is_numeric($destinationAddress['cityId']) ) {
                    $errors[] = __('Invalid city ID for destination address.', 'logestechs');
                }
                if ( empty($destinationAddress['regionId']) || ! is_numeric($destinationAddress['regionId']) ) {
                    $errors[] = __('Invalid region ID for destination address.', 'logestechs');
                }
                if ( empty($destinationAddress['villageId']) || ! is_numeric($destinationAddress['villageId']) ) {
                    $errors[] = __('Invalid village ID for destination address.', 'logestechs');
                }
            }
        
            // Validate origin address data if present
            if ( isset($order['originAddress']) ) {
                $originAddress = $order['originAddress'];
                if ( empty($originAddress['addressLine1']) ) {
                    $errors[] = __('Origin address line 1 is required.', 'logestechs');
                }
                if ( empty($originAddress['cityId']) || ! is_numeric($originAddress['cityId']) ) {
                    $errors[] = __('Invalid city ID for origin address.', 'logestechs');
                }
                if ( empty($originAddress['regionId']) || ! is_numeric($originAddress['regionId']) ) {
                    $errors[] = __('Invalid region ID for origin address.', 'logestechs');
                }
                if ( empty($originAddress['villageId']) || ! is_numeric($originAddress['villageId']) ) {
                    $errors[] = __('Invalid village ID for origin address.', 'logestechs');
                }
                // Validate phone number using regular expression
                if ( !empty($originAddress['senderPhone']) && ! preg_match("/^\+[1-9]{1}[0-9]{9,14}$/", $originAddress['senderPhone']) ) {
                    $errors[] = __('Invalid sender phone number. It must start with a "+" followed by digits.', 'logestechs');
                }
            } else {
                // If origin address is not provided, all related fields should be optional
                if (
                    ! empty($order['originAddress']['addressLine2']) ||
                    ! empty($order['originAddress']['cityId']) ||
                    ! empty($order['originAddress']['regionId']) ||
                    ! empty($order['originAddress']['villageId'])
                ) {
                    $errors[] = __('All origin address fields should be empty when origin address is not provided.', 'logestechs');
                }
            }
        
            return $errors;
        }
        
    }
}
