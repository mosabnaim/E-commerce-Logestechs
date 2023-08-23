<?php
/**
 * The file that handles the rendering of the dedicated Logestechs admin page
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/views
 */

if ( ! class_exists( 'Logestechs_Woocommerce_Metabox_View' ) ) {

    class Logestechs_Woocommerce_Metabox_View {

        private $order_details;

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct( $order_details ) {
            $this->order_details = $order_details;
        }

        /**
         * Render the Logestechs metabox
         */
        public function render() {
            // Render the metabox HTML here, using $this->order_transferred, $this->company_name, and $this->tracking_details
            // Ensure you escape all output!
            // Add metabox content here.
            // Make sure to properly sanitize all output!
            $logestechs_order_id = isset($this->order_details['package_number'])? $this->order_details['package_number'] : '';
            $is_assignable       = ! ( ! empty( $logestechs_order_id ) && $this->order_details['status'] != 'Cancelled' );
            $display_details     = $is_assignable ? 'none' : 'block'; // Inline CSS display value will be 'none' if $logestechs_order_id is empty, else 'block'
            $display_assign_btn  = $is_assignable ? 'block' : 'none'; // Inline CSS display value will be 'none' if $logestechs_order_id is empty, else 'block'
            $details_to_display = [ 'package_number', 'price', 'reservation_date', 'shipment_type', 'recipient', 'package_weight', 'expected_delivery_date', 'phone_number', ];

            ob_start();
            ?>
            <div class="logestechs-metabox-header">
                <div class="logestechs-flex">
                    <div class="logestechs-logo">
                        <img src="<?php echo esc_url( Logestechs_Config::PLUGIN_LOGO ) ?>" alt="logo">
                    </div>
                    <p class="logestechs-primary-text"><?php echo esc_html( Logestechs_Config::PLUGIN_NAME ) ?></p>
                </div>
                <button id="logestechs-transfer-order" data-order-id="<?php echo $this->order_details['id'] ?>" class="js-open-transfer-popup logestechs-white-btn" style="display: <?php echo $display_assign_btn; ?>;"><?php _e( 'Assign Company', 'logestechs' )?></button>
            </div>
            <div class="logestechs-details" style="display: <?php echo $display_details; ?>;">
                <div class="js-logestechs-order-details logestechs-details-flex">
                    <?php
                    $counter = 0;
                    foreach ( $details_to_display as $item ) {
                        echo '<div class="logestechs-details-cell">';
                        echo '<span class="key">' . logestechs_wordify( $item ) . '</span><span class="value" data-key="'.$item.'">' . $this->order_details[$item] ?? '-' . '</span>';
                        echo '</div>';
                        ++$counter;
                    }
                    ?>
                </div>
                <div class="logestechs-metabox-footer">
                    <button id="logestechs-cancel-order" data-order-id="<?php echo $this->order_details['id'] ?>" class="js-logestechs-cancel logestechs-secondary-btn">Cancel Shipping</button>
                    <button id="logestechs-print-invoice" data-order-id="<?php echo $this->order_details['id'] ?>" class="js-logestechs-print logestechs-primary-btn">Print Invoice</button>
                    <button id="logestechs-show-tracking" data-order-id="<?php echo $this->order_details['id'] ?>" class="js-open-details-popup logestechs-primary-btn">Track Package</button>
                </div>
            </div>
            <?php
            echo ob_get_clean();
        }
    }
}
