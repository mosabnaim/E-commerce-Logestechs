<?php
/**
 * The file that handles the rendering of the order companies popup.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/views
 */

if ( ! class_exists( 'Logestechs_Manage_Companies_Popup_View' ) ) {

    class Logestechs_Manage_Companies_Popup_View {

        /**
         * Initialize the class.
         *
         * @since    1.0.0
         */
        public function __construct() {
            // Initialization tasks can be added here if needed.
        }

        /**
         * Render the order companies popup.
         *
         * @since    1.0.0
         * 
         * @param    bool    $manage_only   Whether to render only the management view.
         * @return   void
         */
        public function render( $manage_only = true ) {
            $container_id = $manage_only ? 'logestechs-order-companies-popup' : 'logestechs-order-transfer-popup';

            ob_start();

            if ( ! $manage_only ) {
                ?>
                <form action="logestechs_assign_company" class="logestechs-popup logestechs-order-settings-popup" style="display:none;">
                    <!-- Hidden fields for storing company and order details -->
                    <input type="hidden" name="company_id">
                    <input type="hidden" name="order_id">
                    
                    <div class="logestechs-popup-overlay"></div>
                    <div class="logestechs-popup-content">
                        <!-- Close button for popup -->
                        <div class="logestechs-popup-head">
                            <div class="logestechs-close-btn-wrapper">
                                <button class="js-close-popup close-btn">
                                    <span class="bar"></span>
                                    <span class="bar"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Order details for the popup -->
                        <div class="logestechs-popup-details">
                            <div class="logestechs-section">
                                <p class="logestechs-section-label"><?php esc_html_e( 'Shipment Details', 'logestechs' )?></p>
                                <div class="logestechs-form-divider"></div>
                                <div class="logestechs-pickup-details">
                                    <div class="logestechs-field">
                                        <input type="radio" id="collectFromReceiver" name="payment_type" class="logestechs-radio" checked value="collect_from_receiver">
                                        <label for="collectFromReceiver">
                                            <span class="logestechs-radio"></span>
                                            Collect From Receiver
                                        </label>
                                        <input type="number" min="0" step="0.01" name="pickup_amount" class="logestechs-dropdown-input logestechs-number-input">
                                    </div>
                                    <div class="logestechs-field">
                                        <input type="radio" id="payByReceiver" name="payment_type" class="logestechs-radio" value="pay_by_receiver">
                                        <label for="payByReceiver">
                                            <span class="logestechs-radio"></span>
                                            Pay By Receiver
                                        </label>
                                        <input type="number" min="0" step="0.01" name="pickup_amount" class="logestechs-dropdown-input logestechs-number-input disabled" disabled>
                                    </div>
                                    <div class="logestechs-form-divider"></div>
                                </div>

                            </div>
                            <div class="logestechs-section">
                                <div class="logestechs-checkbox">
                                    <input type="checkbox" name="logestechs_custom_village" id="logestechs-custom-village-checkbox" value="1" />
                                    <label for="logestechs-custom-village-checkbox"><?php _e('Do you want to specify order village?', 'logestechs'); ?></label>
                                </div>
                                <div class="js-logestechs-village" style="display:none;">
                                    <div class="js-logestechs-addresses">
                                        <div class="js-logestechs-address-wrapper js-logestechs-address-block" style="display: none;">
                                            <p class="logestechs-order-head"><?php esc_html_e( 'Order ID:', 'logestechs' )?> #<span></span></p>

                                            <p class="js-logestechs-order-address"></p>
                                            <!-- Village search for destination -->
                                            <div class="logestechs-search-wrapper">
                                                <div class="logestechs-field">
                                                    <label><?php _e('Destination village', 'logestechs'); ?></label>
                                                    <input name="_search" type="text" class="logestechs-destination-village-search logestechs-dropdown-input" placeholder="<?php _e('Search for village...', 'logestechs'); ?>">
                                                    <input name="_village" type="hidden" class="js-logestechs-selected-village">
                                                    <input name="_city" type="hidden" class="js-logestechs-selected-city">
                                                    <input name="_region" type="hidden" class="js-logestechs-selected-region">
                                                </div>
                                                <div class="logestechs-destination-village-results logestechs-village-results" style="display: none;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="logestechs-checkbox">
                                <input type="checkbox" name="logestechs_custom_store" id="logestechs-custom-store-checkbox" value="1" />
                                <label for="logestechs-custom-store-checkbox"><?php _e('Do you want to use different store?', 'logestechs'); ?></label>
                            </div>
                            <div class="js-logestechs-store-details" style="display:none;">
                                <div class="logestechs-field">
                                    <label for="logestechs_business_name"><?php _e('Business Name', 'logestechs'); ?></label>
                                    <input type="text" name="logestechs_business_name" id="logestechs_business_name" />
                                </div>
                                <!-- Store Owner -->
                                <div class="logestechs-field">
                                    <label for="logestechs_store_owner"><?php _e('Store Owner', 'logestechs'); ?></label>
                                    <input type="text" name="logestechs_store_owner" id="logestechs_store_owner" />
                                </div>
                                <!-- Store Phone Number -->
                                <div class="logestechs-field">
                                    <label for="logestechs_store_phone_number"><?php _e('Store Phone Number', 'logestechs'); ?></label>
                                    <input type="text" name="logestechs_store_phone_number" id="logestechs_store_phone_number" placeholder="+1234567890" />
                                </div>
                                <div class="logestechs-search-wrapper">
                                    <div class="logestechs-field">
                                        <label for="logestechs-store-village-search"><?php _e('Store village', 'logestechs'); ?></label>
                                        <input type="text" id="logestechs-store-village-search" class="logestechs-dropdown-input" name="logestechs_store_village_name" placeholder="Search for village...">
                                        <input type="hidden" name="logestechs_store_region_id" class="js-logestechs-selected-region">
                                        <input type="hidden" name="logestechs_store_city_id" class="js-logestechs-selected-city">
                                        <input type="hidden" name="logestechs_store_village_id" class="js-logestechs-selected-village">
                                    </div>
                                    <div class="logestechs-store-village-results logestechs-village-results" style="display: none;"></div>
                                </div>
                                <!-- Address Line 1 -->
                                <div class="logestechs-field">
                                        <label for="logestechs_store_address"><?php _e('Store Address Line 1', 'logestechs'); ?></label>
                                        <input type="text" name="logestechs_store_address" id="logestechs_store_address" />
                                    </div>
                                    
                                    <!-- Address Line 2 -->
                                    <div class="logestechs-field">
                                        <label for="logestechs_store_address_2"><?php _e('Store Address Line 2', 'logestechs'); ?></label>
                                        <input type="text" name="logestechs_store_address_2" id="logestechs_store_address_2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Popup footer with transfer order button -->
                            <div class="logestechs-popup-footer">
                                <button class="js-logestechs-transfer-order logestechs-primary-btn"><?php _e('Transfer Order', 'logestechs'); ?></button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Loader screen to be displayed during API calls or other time-consuming operations -->
                <div class="logestechs-popup js-loader-screen" style="display:none;">
                    <div class="logestechs-popup-overlay"></div>
                    <div class="logestechs-loader-container">
                        <canvas id="logestechs-loader" width="700" height="700"></canvas>
                    </div>
                </div>

            <?php } ?>

            <!-- Manage Companies Popup Content -->
            <div id="<?php echo esc_attr($container_id); ?>" class="logestechs-popup" style="display: none;">
                <div class="logestechs-popup-overlay"></div>
                <div class="logestechs-popup-content">
                    <div class="logestechs-popup-head">
                        <p class="logestechs-popup-label"><?php _e('Manage Companies', 'logestechs'); ?></p>
                        <div class="logestechs-close-btn-wrapper">
                            <button class="js-close-popup close-btn">
                                <span class="bar"></span>
                                <span class="bar"></span>
                            </button>
                        </div>
                        <!-- Company form to add or update company details -->
                        <form class="js-company-form logestechs-form-container">
                            <div class="logestechs-input-container">
                                <input type="text" name="domain" required>
                                <label><?php _e('Domain', 'logestechs'); ?></label>
                            </div>
                            <div class="logestechs-input-container">
                                <input type="text" name="email" required>
                                <label><?php _e('Email', 'logestechs'); ?></label>
                            </div>
                            <div class="logestechs-input-container">
                                <input type="password" name="password" required>
                                <label><?php _e('Password', 'logestechs'); ?></label>
                            </div>
                            <button class="logestechs-primary-btn js-logestechs-add-company logestechs-add-btn">
                                <img src="<?php echo esc_url(logestechs_image('plus.svg')); ?>" alt="plus"> <?php _e('Add Company', 'logestechs'); ?>
                            </button>
                            <button class="logestechs-primary-btn js-logestechs-update-company logestechs-add-btn" style="display: none;">
                            <img src="<?php echo esc_url(logestechs_image('save.svg')); ?>" alt="save"> <?php _e('Update Company', 'logestechs'); ?>
                            </button>
                        </form>
                    </div>
                    <!-- Display stored companies in this section -->
                    <div class="logestechs-popup-main">
                        <div class="js-logestechs-companies logestechs-row-container">
                            <!-- Skeleton loaders can be displayed during company fetching -->
                            <div class="logestechs-skeleton-row" style="margin: 15px 50px">
                                <div class="logestechs-skeleton-loader" style="width: 45px; height: 45px;"></div>
                                <div class="logestechs-skeleton-column">
                                    <div class="logestechs-skeleton-loader" style="width: 230px; height: 20px"></div>
                                    <div class="logestechs-skeleton-loader" style="width: 100px; height: 15px"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ( ! $manage_only ) { ?>
                        <!-- Assign Company footer button for assigning the order to a company -->
                        <div class="logestechs-popup-footer">
                            <button class="js-logestechs-assign-company logestechs-primary-btn disabled" disabled><?php _e('Assign Company', 'logestechs'); ?></button>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <?php
            echo ob_get_clean();
        }
    }
}
