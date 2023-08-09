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
            $details_to_display = [
                [
                    'key' => 'package_number',
                    'label' => 'Package Number',
                    'value' => '#130724024379'
                ],
                [
                    'key' => 'price',
                    'label' => 'Price',
                    'value' => '20 SAR'
                ],
                [
                    'key' => 'reservation_date',
                    'label' => 'Reservation Date',
                    'value' => '24/07/2023'
                ],
                [
                    'key' => 'shipment_type',
                    'label' => 'Shipment Type',
                    'value' => 'Cod'
                ],
                [
                    'key' => 'recipient',
                    'label' => 'Recipient',
                    'value' => 'Omar Sakr'
                ],
                [
                    'key' => 'package_weight',
                    'label' => 'Package Weight',
                    'value' => '0'
                ],
                [
                    'key' => 'expected_delivery_date',
                    'label' => 'Expected Delivery Date',
                    'value' => '26/07/2023'
                ],
                [
                    'key' => 'phone_number',
                    'label' => 'Phone Number',
                    'value' => '0595453476'
                ]
            ];
            
            
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
                            <button class="close-popup close-btn">
                                <span class="bar"></span>
                                <span class="bar"></span>
                            </button>
                        </div>
                    </div>
                    <div class="logestechs-popup-main">
                        <div class="logestechs-details-flex">
                            <?php
                            foreach ($details_to_display as $detail) {
                                echo '<div class="logestechs-details-cell ' . esc_attr($detail['key']) . '">';
                                echo '<span class="key">' . esc_html($detail['label']) . '</span><span class="js-logestechs-order-value value" data-key="'.esc_html($detail['key']).'">' . esc_html($detail['value']) . '</span>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                        <div class="logestechs-scrollable-area">
                            <div class="logestechs-tracking-row">
                                <div class="logestechs-date-wrapper">
                                    <p class="logestechs-date">8:48 AM</p>
                                    <p class="logestechs-time">2023-07-24</p>
                                </div>
                                <div class="logestechs-tracking-circle">
                                    <span class="logestechs-tracker-line"></span>
                                    <span></span>
                                    <div class="logestechs-circle"></div>
                                </div>
                                <div class="logestechs-tracking-data">
                                    Cancelled package by customer
                                </div>
                            </div>
                            <div class="logestechs-tracking-row">
                                <div class="logestechs-date-wrapper">
                                    <p class="logestechs-date">8:48 AM</p>
                                    <p class="logestechs-time">2023-07-24</p>
                                </div>
                                <div class="logestechs-tracking-circle">
                                    <span></span>
                                    <div class="logestechs-circle"></div>
                                </div>
                                <div class="logestechs-tracking-data">
                                    Added the package
                                </div>
                            </div>
                            <div class="logestechs-tracking-row">
                                <div class="logestechs-date-wrapper">
                                    <p class="logestechs-date">8:48 AM</p>
                                    <p class="logestechs-time">2023-07-24</p>
                                </div>
                                <div class="logestechs-tracking-circle">
                                    <span></span>
                                    <div class="logestechs-circle"></div>
                                </div>
                                <div class="logestechs-tracking-data">
                                    Added the package
                                </div>
                            </div>
                            <div class="logestechs-tracking-row">
                                <div class="logestechs-date-wrapper">
                                    <p class="logestechs-date">8:48 AM</p>
                                    <p class="logestechs-time">2023-07-24</p>
                                </div>
                                <div class="logestechs-tracking-circle">
                                    <span></span>
                                    <div class="logestechs-circle"></div>
                                </div>
                                <div class="logestechs-tracking-data">
                                    Added the package
                                </div>
                            </div>
                            <div class="logestechs-tracking-row">
                                <div class="logestechs-date-wrapper">
                                    <p class="logestechs-date">8:48 AM</p>
                                    <p class="logestechs-time">2023-07-24</p>
                                </div>
                                <div class="logestechs-tracking-circle">
                                    <span></span>
                                    <div class="logestechs-circle"></div>
                                </div>
                                <div class="logestechs-tracking-data">
                                    Added the package
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            echo ob_get_clean();
        }
    }
}
