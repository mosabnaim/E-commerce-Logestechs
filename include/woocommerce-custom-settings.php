<?php
/**
 * The file that handles order tracking
 *
 * This file is used to handle order tracking for Logestechs orders.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/include/orders
 */

if ( ! class_exists( 'Logestechs_Woocommerce_Custom_Settings' ) ) {

    class Logestechs_Woocommerce_Custom_Settings {

        public function init() {
            add_action( 'woocommerce_settings_general', [$this, 'add_logestechs_settings_fields'] );
            add_action( 'woocommerce_update_options_general', [$this, 'save_logestechs_settings_fields'] );
        }


        public function add_logestechs_settings_fields() {
            woocommerce_admin_fields(
                [
                    'section_title'     => [
                        'name' => __( 'Logestechs Settings', 'logestechs' ),
                        'type' => 'title',
                        'desc' => __('These settings are necessary to be able to transfer orders to logestechs successfully.', 'logestechs'),
                        'id'   => 'logestechs_section_title'
                    ],
                    'store_phone_number' => [
                        'name'        => __( 'Store Phone Number', 'logestechs' ),
                        'type'        => 'text',
                        'css'         => 'min-width:300px;',
                        'desc_tip'    => true,
                        'description' => __( 'Enter the store phone number.', 'logestechs' ),
                        'id'          => 'woocommerce_store_phone_number'
                    ],
                    'business_sender_name' => [
                        'name'        => __( 'Business Name', 'logestechs' ),
                        'type'        => 'text',
                        'css'         => 'min-width:300px;',
                        'desc_tip'    => true,
                        'description' => __( 'Enter the business name.', 'logestechs' ),
                        'id'          => 'woocommerce_business_name'
                    ],
                    'individual_sender_name' => [
                        'name'        => __( 'Store Owner', 'logestechs' ),
                        'type'        => 'text',
                        'css'         => 'min-width:300px;',
                        'desc_tip'    => true,
                        'description' => __( 'Enter the store owner name.', 'logestechs' ),
                        'id'          => 'woocommerce_store_owner'
                    ],
                    'section_end'       => [
                        'type' => 'sectionend',
                        'id'   => 'logestechs_section_end'
                    ]
                ]
            );
        }

        public function save_logestechs_settings_fields() {
            $fields = ['woocommerce_store_phone_number', 'woocommerce_business_name', 'woocommerce_store_owner'];
            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    update_option($field, wc_clean($_POST[$field]));
                }
            }
        }

        public function store_phone() {
            return get_option( 'woocommerce_store_phone_number' );
        }
        public function business_name() {
            return get_option( 'woocommerce_business_name' );
        }
        public function store_owner() {
            return get_option( 'woocommerce_store_owner' );
        }
    }
}
