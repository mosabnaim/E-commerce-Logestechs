<?php
/**
 * The file responsible for rendering the custom column in the WooCommerce orders list.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/views
 */

if ( ! class_exists( 'Logestechs_Woocommerce_List_View' ) ) {

    class Logestechs_Woocommerce_List_View {

        /**
         * Add a custom column header to the WooCommerce orders list.
         *
         * @since    1.0.0
         * @param array $columns Existing column headers.
         * @return array $columns Updated column headers with our custom column.
         */
        public function add_custom_column_header( $columns ) {
            $new_columns = [];

            // Adds the custom column after the "Order" column.
            foreach ( $columns as $column_name => $column_info ) {
                $new_columns[$column_name] = $column_info;
                if ( 'order_number' === $column_name ) {
                    $new_columns['logestechs']       = Logestechs_Config::PLUGIN_NAME;
                    $new_columns['logestechs_notes'] = __( 'Notes', 'logestechs' );
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
        public function add_custom_column_data( $column, $post_id ) {
            if ( 'logestechs' == $column ) {
                // Check if it's our custom column and display the column data.
                $transfer_acceptable_statuses = Logestechs_Config::ACCEPTABLE_TRANSFER_STATUS;
                $logestechs_order_status      = get_post_meta( $post_id, '_logestechs_order_status', true );
                if ( Logestechs_Config::COMPANY_DOMAIN ) {
                    $order_barcode = get_post_meta( $post_id, '_logestechs_order_barcode', true );
                    if ( ! in_array( $logestechs_order_status, $transfer_acceptable_statuses ) ) {
                        echo '<p>#' . esc_html( $order_barcode ) . '</p>';
                    } else {
                        echo '<button class="js-open-transfer-popup logestechs-btn-text" data-order-id="' . esc_attr( $post_id ) . '">' . esc_html__( 'Transfer Order', 'logestechs' ) . '</button>';
                    }

                    return;
                }

                $logestechs_company = get_post_meta( $post_id, '_logestechs_company_name', true );
                if ( ! empty( $logestechs_company ) && ! in_array( $logestechs_order_status, $transfer_acceptable_statuses ) ) {
                    echo '<p>' . esc_html( $logestechs_company ) . '</p>';
                } else {
                    echo '<button class="js-open-transfer-popup logestechs-btn-text" data-order-id="' . esc_attr( $post_id ) . '">' . esc_html__( 'Assign Company', 'logestechs' ) . '</button>';
                }
            }else if('logestechs_notes' == $column) {
                $logestechs_notes      = get_post_meta( $post_id, '_logestechs_notes', true );
                echo $logestechs_notes ?: '-'; 
            }

        }
    }
}
