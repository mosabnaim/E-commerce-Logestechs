<?php
/**
 * The file that renders the custom column in the WooCommerce orders list
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/views
 */

if (!class_exists('Logestechs_Woocommerce_List_View')) {

    class Logestechs_Woocommerce_List_View {

        /**
         * Add custom column header to WooCommerce orders list
         *
         * @param array $columns Existing column headers
         * @return array $columns Updated column headers with our custom column
         */
        public function add_custom_column_header($columns) {
            // Define the column header for our custom column
            $new_columns = [];
    
            // Adds the custom column after the "Order" column.
            foreach ( $columns as $column_name => $column_info ) {
                $new_columns[$column_name] = $column_info;
                if ( 'order_number' === $column_name ) {
                    $new_columns['logestechs'] = __( 'Logestechs', 'logestechs' );
                }
            }
        
            return $new_columns;
        }
    
        /**
         * Add custom column data to WooCommerce orders list
         *
         * @param string $column Column identifier
         * @param int $post_id The post ID (order ID)
         */
        public function add_custom_column_data($column, $post_id) {

            // Check if it's our custom column and add the column data
            if ($column == 'logestechs') {
                $logestechs_company = get_post_meta( $post_id, 'logestechs_company_name', true );
                if(!empty($logestechs_company)) {
                    echo '<p>' . $logestechs_company . '</p>';
                }else {
                    // Fetch data from Logestechs API and display in the column
                    echo '<button class="js-open-transfer-popup logestechs-btn-text" data-order-id="' . $post_id . '">' . __( 'Assign Company', 'logestechs' ) . '</button>';
                }
            }
        }
    }
}