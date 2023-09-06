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

        /**
         * Sanitize order data before sending to Logestechs API.
         *
         * This function takes the prepared order data and sanitizes it, making sure all the data is in a format that
         * Logestechs API can accept.
         *
         * @param array $order_data The prepared order data.
         * @return array The sanitized order data.
         *
         * @since 1.0.0
         */
        public function sanitize_order( $order_data ) {
            // Initialize an array to hold the sanitized data.
            $sanitized_order_data = [];
        
            // Sanitize package data
            if ( isset($order_data['pkg']) ) {
                $pkg = $order_data['pkg'];
                $sanitized_order_data['pkg'] = [
                    'receiverName'              => sanitize_text_field($pkg['receiverName']),
                    'cod'                       => floatval($pkg['cod']),
                    'notes'                     => sanitize_text_field($pkg['notes']),
                    'packageItemsToDeliverList' => array_map(function ($item) {
                        return [
                            'name' => sanitize_text_field($item['name']),
                            'cod'  => floatval($item['cod'])
                        ];
                    }, $pkg['packageItemsToDeliverList']),
                    'senderName'                => sanitize_text_field($pkg['senderName'] ?? ''),
                    'businessSenderName'        => sanitize_text_field($pkg['businessSenderName'] ?? ''),
                    'senderPhone'               => sanitize_text_field($pkg['senderPhone'] ?? ''),
                    'receiverPhone'             => sanitize_text_field($pkg['receiverPhone']),
                    'receiverPhone2'            => sanitize_text_field($pkg['receiverPhone2'] ?? ''),
                    'serviceType'               => 'STANDARD',
                    'shipmentType'              => 'COD',
                    'quantity'                  => intval($pkg['quantity']),
                    'description'               => sanitize_text_field($pkg['description']),
                    'integrationSource'         => 'WOOCOMMERCE'
                ];
            }
        
            // Sanitize destination address data
            if ( isset($order_data['destinationAddress']) ) {
                $destinationAddress = $order_data['destinationAddress'];
                $sanitized_order_data['destinationAddress'] = [
                    'addressLine1' => sanitize_text_field($destinationAddress['addressLine1']),
                    'cityId'       => intval($destinationAddress['cityId']),
                    'regionId'     => intval($destinationAddress['regionId']),
                    'villageId'    => intval($destinationAddress['villageId'])
                ];
            }
        
            // Sanitize package unit type
            $sanitized_order_data['pkgUnitType'] = 'METRIC';
        
            // Sanitize origin address data
            if ( isset($order_data['originAddress']) ) {
                $originAddress = $order_data['originAddress'];
                $sanitized_order_data['originAddress'] = [
                    'addressLine1' => sanitize_text_field($originAddress['addressLine1']),
                    'addressLine2' => sanitize_text_field($originAddress['addressLine2']),
                    'cityId'       => intval($originAddress['cityId']),
                    'regionId'     => intval($originAddress['regionId']),
                    'villageId'    => intval($originAddress['villageId'])
                ];
            }
        
            return $sanitized_order_data;
        }        
        
    }
}
