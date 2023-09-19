<?php
/**
 * The file responsible for handling the rendering of the dedicated Logestechs admin page.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/views
 */

 if (!class_exists('Logestechs_Woocommerce_Metabox_View')) {

    class Logestechs_Woocommerce_Metabox_View {

        private $order_details;

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         * @param    array $order_details Details about the order.
         */
        public function __construct($order_details) {
            $this->order_details = $order_details;
        }

        /**
         * Render the Logestechs metabox content.
         *
         * @since    1.0.0
         */
        public function render() {
            $logestechs_order_id = $this->order_details['package_number'] ?? '';
            $completed_statuses = Logestechs_Config::ACCEPTABLE_TRANSFER_STATUS;
            $acceptable_pickup_statuses = Logestechs_Config::ACCEPTABLE_PICKUP_STATUS;

            $pickup_allowed = !( !empty($logestechs_order_id) &&  !in_array($this->order_details['status'], $acceptable_pickup_statuses));
            $is_assignable = !( !empty($logestechs_order_id) &&  !in_array($this->order_details['status'], $completed_statuses) );
            $display_pickup_btn = $pickup_allowed ? 'block' : 'none';
            $display_details = $is_assignable ? 'none' : 'block';
            $display_assign_btn = $is_assignable ? 'block' : 'none';
            $details_to_display = [
                'package_number'         => __('Shipment Number', 'logestechs'),
                'price'                  => __('Price', 'logestechs'),
                'reservation_date'       => __('Reservation Date', 'logestechs'),
                'shipment_type'          => __('Shipment Type', 'logestechs'),
                'recipient'              => __('Recipient', 'logestechs'),
                'package_weight'         => __('Shipment Weight', 'logestechs'),
                'expected_delivery_date' => __('Expected Delivery Date', 'logestechs'),
                'phone_number'           => __('Phone Number', 'logestechs')
            ];
            
            ob_start();
            ?>
            <div class="logestechs-metabox-header">
                <div class="logestechs-flex">
                    <div class="logestechs-logo">
                        <img src="<?php echo esc_url(Logestechs_Config::PLUGIN_LOGO); ?>" alt="Logestechs Logo">
                    </div>
                    <p class="logestechs-primary-text"><?php echo esc_html(Logestechs_Config::PLUGIN_NAME); ?></p>
                </div>
                <div class="logestechs-flex">
                    <button data-order-id="<?php echo esc_attr($this->order_details['id']); ?>" class="js-open-pickup-popup logestechs-white-btn" style="display: <?php echo esc_attr($display_pickup_btn); ?>;"><?php esc_html_e('Request Pickup', 'logestechs'); ?></button>
                    <button id="logestechs-transfer-order" data-order-id="<?php echo esc_attr($this->order_details['id']); ?>" class="js-open-transfer-popup logestechs-white-btn" style="display: <?php echo esc_attr($display_assign_btn); ?>;">
                    <?php (Logestechs_Config::COMPANY_ID)? esc_html_e('Transfer Order', 'logestechs') :  esc_html_e( 'Assign Company', 'logestechs' ); ?>
                </button>
                </div>
            </div>
            <div class="logestechs-details" style="display: <?php echo esc_attr($display_details); ?>;">
                <div class="js-logestechs-order-details logestechs-details-flex">
                    <?php
                    foreach ($details_to_display as $key => $label) {
                        ?>
                        <div class="logestechs-details-cell">
                            <span class="key"><?php echo esc_html($label); ?></span>
                            <span class="value" data-key="<?php echo esc_attr($key); ?>"><?php echo esc_html($this->order_details[$key] ?? '-'); ?></span>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="logestechs-metabox-footer">
                    <button id="logestechs-cancel-order" data-order-id="<?php echo esc_attr($this->order_details['id']); ?>" class="js-logestechs-cancel logestechs-secondary-btn"><?php esc_html_e('Cancel Shipping', 'logestechs'); ?></button>
                    <button id="logestechs-print-invoice" data-order-id="<?php echo esc_attr($this->order_details['id']); ?>" class="js-logestechs-print logestechs-primary-btn"><?php esc_html_e('Print Invoice', 'logestechs'); ?></button>
                    <button id="logestechs-show-tracking" data-order-id="<?php echo esc_attr($this->order_details['id']); ?>" class="js-open-details-popup logestechs-primary-btn"><?php esc_html_e('Track Shipment', 'logestechs'); ?></button>
                </div>
            </div>
            <?php
            echo ob_get_clean();
        }
    }
}
