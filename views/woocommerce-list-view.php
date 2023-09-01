<?php
/**
 * The file responsible for rendering the custom column in the WooCommerce orders list.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/views
 */

 if (!class_exists('Logestechs_Woocommerce_List_View')) {

    class Logestechs_Woocommerce_List_View {

        /**
         * Add a custom column header to the WooCommerce orders list.
         *
         * @since    1.0.0
         * @param array $columns Existing column headers.
         * @return array $columns Updated column headers with our custom column.
         */
        public function add_custom_column_header($columns) {
            $new_columns = [];
    
            // Adds the custom column after the "Order" column.
            foreach ( $columns as $column_name => $column_info ) {
                $new_columns[$column_name] = $column_info;
                if ( 'order_number' === $column_name ) {
                    $new_columns['logestechs'] = esc_html__( 'Logestechs', 'logestechs' );
                }
            }
        
            return $new_columns;
        }
        
    
        /**
         * Display custom data in the WooCommerce orders list column.
         *
         * @since    1.0.0
         * @param string $column Column identifier.
         * @param int    $post_id The post ID (order ID).
         */
        public function add_custom_column_data($column, $post_id) {

            // Check if it's our custom column and display the column data.
            if ('logestechs' === $column) {
                $logestechs_company = get_post_meta($post_id, '_logestechs_company_name', true);
                $logestechs_order_status = get_post_meta($post_id, '_logestechs_order_status', true);
                $completed_statuses = Logestechs_Config::ACCEPTABLE_TRANSFER_STATUS;

                if (!empty($logestechs_company) && !in_array($logestechs_order_status, $completed_statuses)) {
                    echo '<p>' . esc_html($logestechs_company) . '</p>';
                } else {
                    echo '<button class="js-open-transfer-popup logestechs-btn-text" data-order-id="' . esc_attr($post_id) . '">' . esc_html__( 'Assign Company', 'logestechs' ) . '</button>';
                }
            }
        }
    }
}
