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

            $logestechs_order_id = $this->order_details['logestechs_order_id'];
            $display_details = empty($logestechs_order_id) ? 'none' : 'block'; // Inline CSS display value will be 'none' if $logestechs_order_id is empty, else 'block'
            $display_assign_btn = empty($logestechs_order_id) ? 'block' : 'none'; // Inline CSS display value will be 'none' if $logestechs_order_id is empty, else 'block'
            
            $details_to_display = [
                'Package Number' => '#130724024379',
                'Price' => '20 SAR',
                'Reservation Date' => '24/07/2023',
                'Shipment Type' => 'Cod',
                'Recipient' => 'Omar Sakr',
                'Package Weight' => '0',
                'Expected Delivery Date' => '26/07/2023',
                'Phone Number' => '0595453476'
            ];

            ob_start();
            ?>
            <div class="logestechs-metabox-header">
                <div class="logestechs-flex">
                    <div class="logestechs-logo">
                        <img src="<?php echo esc_url( Logestechs_Config::PLUGIN_LOGO ) ?>" alt="logo">
                    </div>
                    <p class="logestechs-primary-text"><?php echo esc_html( Logestechs_Config::PLUGIN_NAME ) ?></p>
                </div>
                <button id="logestechs-transfer-order" class="js-open-transfer-popup logestechs-white-btn" style="display: <?php echo $display_assign_btn; ?>;"><?php _e( 'Assign Company', 'logestechs' )?></button>
            </div>
            <div class="logestechs-details" style="display: <?php echo $display_details; ?>;">
                <div class="logestechs-details-flex">
                    <?php
                    $counter = 0;
                    foreach ($details_to_display as $key => $value) {
                        echo '<div class="logestechs-details-cell">';
                        echo '<span class="key">' . esc_html($key) . '</span><span class="value">' . esc_html($value) . '</span>';
                        echo '</div>';
                        $counter++;
                    }
                    ?>
                </div>
                <div class="logestechs-metabox-footer">
                    <button id="logestechs-cancel-order" class="logestechs-secondary-btn">Cancel Shipping</button>
                    <button id="logestechs-print-invoice" class="logestechs-primary-btn">Print Invoice</button>
                    <button id="logestechs-show-tracking" class="js-open-details-popup logestechs-primary-btn">Track Package</button>
                </div>
            </div>
            <?php
            echo ob_get_clean();
        }
    }
}
