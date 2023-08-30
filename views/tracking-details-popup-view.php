<?php
/**
 * The file that handles the rendering of the tracking details popup.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/views
 */

if (!class_exists('Logestechs_Tracking_Details_Popup_View')) {

    class Logestechs_Tracking_Details_Popup_View {
        /**
         * Render the tracking details popup.
         *
         * @since    1.0.0
         * @param int $orderId The ID of the order whose tracking details are to be displayed.
         */
        public function render() {
            // Fetch any necessary data using $order_id.
            $details_to_display = [
                'package_number' => __('Package Number', 'logestechs'),
                'price' => __('Price', 'logestechs'),
                'reservation_date' => __('Reservation Date', 'logestechs'),
                'shipment_type' => __('Shipment Type', 'logestechs'),
                'recipient' => __('Recipient', 'logestechs'),
                'package_weight' => __('Package Weight', 'logestechs'),
                'expected_delivery_date' => __('Expected Delivery Date', 'logestechs'),
                'phone_number' => __('Phone Number', 'logestechs')
            ];
            
            
            ob_start();
            ?>
            <div id="logestechs-order-details-popup" class="logestechs-popup logestechs-transfer-popup" style="display: none;">
                <div class="logestechs-popup-overlay"></div>
                <div class="logestechs-popup-content">
                    <div class="logestechs-popup-tracker-head">
                        <div class="logestechs-popup-label-wrapper">
                            <div class="logestechs-box-wrapper">
                                <img src="<?php echo esc_url(logestechs_image('box.svg')); ?>" alt="<?php esc_attr_e('Box Icon', 'logestechs'); ?>">
                            </div>
                            <p class="logestechs-popup-label"><?php esc_html_e('Package Tracking And Details', 'logestechs'); ?></p>
                        </div>
                        <div class="logestechs-close-btn-wrapper">
                            <button class="js-close-popup close-btn">
                                <span class="bar"></span>
                                <span class="bar"></span>
                            </button>
                        </div>
                    </div>
                    <div class="logestechs-popup-main">
                        <div class="logestechs-details-flex">
                            <?php
                            foreach ($details_to_display as $key => $label) {
                                echo '<div class="logestechs-details-cell ' . esc_attr($label) . '">';
                                echo '<span class="key">' . esc_html($label) . '</span><div class="js-logestechs-order-value value" data-key="'.esc_attr($key).'"><div class="logestechs-skeleton-loader" style="width: 40px; height: 20px"></div></div>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                        <div class="js-tracking-data logestechs-scrollable-area"></div>
                    </div>
                </div>
            </div>
            <?php
            echo ob_get_clean();
        }
    }
}
