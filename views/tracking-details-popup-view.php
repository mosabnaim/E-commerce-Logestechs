<?php
/**
 * The file that handles the rendering of the tracking details popup
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/views
 */

if (!class_exists('Logestechs_Tracking_Details_Popup_View')) {

    class Logestechs_Tracking_Details_Popup_View {

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {
            // You might want to enqueue necessary scripts or styles related to this view here.
        }

        /**
         * Render the tracking details popup
         *
         * @param int $orderId The ID of the order whose tracking details are to be displayed
         */
        public function render() {
            // Fetch any necessary data using $order_id
            // Render the popup HTML. Ensure you escape all output!
            $details_to_display = [ 'package_number', 'price', 'reservation_date', 'shipment_type', 'recipient', 'package_weight', 'expected_delivery_date', 'phone_number', ];
            
            ob_start();
            ?>
            <div id="logestechs-order-details-popup" class="logestechs-popup logestechs-transfer-popup" style="display: none;">
                <div class="logestechs-popup-overlay"></div>
                <div class="logestechs-popup-content">
                    <div class="logestechs-popup-tracker-head">
                        <div class="logestechs-popup-label-wrapper">
                            <div class="logestechs-box-wrapper">
                            <img src="<?php echo logestechs_image('box.svg'); ?>" alt="">
                            </div>
                            <p class="logestechs-popup-label"><?php _e('Package Tracking And Details', 'logestechs'); ?></p>
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
                            foreach ($details_to_display as $item) {
                                echo '<div class="logestechs-details-cell ' . logestechs_wordify($item) . '">';
                                echo '<span class="key">' . logestechs_wordify($item) . '</span><div class="js-logestechs-order-value value" data-key="'.$item.'"><div class="logestechs-skeleton-loader" style="width: 40px; height: 20px"></div></div>';
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
