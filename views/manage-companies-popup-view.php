<?php
/**
 * The file that handles the rendering of the order companies popup
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/views
 */

if ( ! class_exists( 'Logestechs_Manage_Companies_Popup_View' ) ) {

    class Logestechs_Manage_Companies_Popup_View {

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {
            // You might want to enqueue necessary scripts or styles related to this view here.
        }

        /**
         * Render the order companies popup
         */
        public function render( $manage_only = true ) {
            // Fetch any necessary data using $order_id
            // Render the popup HTML. Ensure you escape all output!
            $container_id = $manage_only ? 'logestechs-order-companies-popup' : 'logestechs-order-transfer-popup';
            ob_start();
            if ( ! $manage_only ) {
                ?>
                <form action="logestechs_assign_company" class="logestechs-popup logestechs-order-settings-popup" style="display:none;">
                    <input type="hidden" name="company_id">
                    <input type="hidden" name="order_id">
                    <div class="logestechs-popup-overlay"></div>
                    <div class="logestechs-popup-content">
                        <div class="logestechs-popup-head">
                            <div class="logestechs-close-btn-wrapper">
                                <button class="js-close-popup close-btn">
                                    <span class="bar"></span>
                                    <span class="bar"></span>
                                </button>
                            </div>
                        </div>
                        <div class="logestechs-popup-details">
                            <p class="logestechs-destination-address-label">Destination Address:</p>
                            <p class="js-logestechs-order-address"></p>
                            <div class="logestechs-search-wrapper">
                                <div class="logestechs-field">
                                    <label><?php _e('Destination village', 'logestechs'); ?></label>
                                    <input type="text" id="logestechs-destination-village-search" class="logestechs-dropdown-input" placeholder="Search for the destination village..." required>
                                    <input type="hidden" name="logestechs_destination_village_id" class="js-logestechs-selected-village" required>
                                    <input type="hidden" name="logestechs_destination_city_id" class="js-logestechs-selected-city" required>
                                    <input type="hidden" name="logestechs_destination_region_id" class="js-logestechs-selected-region" required>
                                </div>
                                <div class="logestechs-destination-village-results logestechs-village-results" style="display: none;"></div>
                            </div>
                            <div class="logestechs-checkbox">
                                <input type="checkbox" name="logestechs_custom_store" id="logestechs-custom-store-checkbox" value="1" />
                                <label for="logestechs-custom-store-checkbox"><?php _e('Do you want to use different store?', 'logestechs'); ?></label>
                            </div>
                            <div class="js-logestechs-store-details" style="display:none;">
                                <div class="logestechs-field">
                                    <label for="logestechs_business_name"><?php _e('Business Name', 'logestechs'); ?></label>
                                    <input type="text" name="logestechs_business_name" id="logestechs_business_name" value="" />
                                </div>
                                <!-- Store Owner -->
                                <div class="logestechs-field">
                                    <label for="logestechs_store_owner"><?php _e('Store Owner', 'logestechs'); ?></label>
                                    <input type="text" name="logestechs_store_owner" id="logestechs_store_owner" value="" />
                                </div>
                                <!-- Store Phone Number -->
                                <div class="logestechs-field">
                                    <label for="logestechs_store_phone_number"><?php _e('Store Phone Number', 'logestechs'); ?></label>
                                    <input type="text" name="logestechs_store_phone_number" id="logestechs_store_phone_number" value="" />
                                </div>
                                <div class="logestechs-search-wrapper">
                                    <div class="logestechs-field">
                                        <label for="logestechs-store-village-search"><?php _e('Store village', 'logestechs'); ?></label>
                                        <input type="text" id="logestechs-store-village-search" class="logestechs-dropdown-input" name="logestechs_store_village_name" placeholder="Search for the store village..." value="">
                                        <input type="hidden" name="logestechs_store_region_id" class="js-logestechs-selected-region">
                                        <input type="hidden" name="logestechs_store_city_id" class="js-logestechs-selected-city">
                                        <input type="hidden" name="logestechs_store_village_id" class="js-logestechs-selected-village">
                                    </div>
                                    <div class="logestechs-store-village-results logestechs-village-results" style="display: none;"></div>
                                </div>
                                <!-- Address Line 1 -->
                                <div class="logestechs-field">
                                    <label for="logestechs_store_address"><?php _e('Store Address Line 1', 'logestechs'); ?></label>
                                    <input type="text" name="logestechs_store_address" id="logestechs_store_address" value="" />
                                </div>
                                <!-- Address Line 2 -->
                                <div class="logestechs-field">
                                    <label for="logestechs_store_address_2"><?php _e('Store Address Line 2', 'logestechs'); ?></label>
                                    <input type="text" name="logestechs_store_address_2" id="logestechs_store_address_2" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="logestechs-popup-footer">
                            <button class="js-logestechs-transfer-order logestechs-primary-btn disabled" disabled><?php echo __( 'Transfer Order', 'logestechs' ) ?></button>
                        </div>
                    </div>
                </form>
                <?php
                ?>
                <div class="logestechs-popup js-loader-screen" style="display:none;">
                    <div class="logestechs-popup-overlay"></div>
                    <div class="logestechs-loader-container">
                        <canvas id="logestechs-loader" width="700" height="700"></canvas>
                    </div>
                </div>
                <?php
            }
            ?>
            <div id="<?php echo $container_id; ?>" class="logestechs-popup" style="display: none;">
                <div class="logestechs-popup-overlay"></div>
                <div class="logestechs-popup-content">
                    <div class="logestechs-popup-head">
                        <p class="logestechs-popup-label"><?php _e( 'Manage Companies', 'logestechs' );?></p>
                        <div class="logestechs-close-btn-wrapper">
                            <button class="js-close-popup close-btn">
                                <span class="bar"></span>
                                <span class="bar"></span>
                            </button>
                        </div>
                        <form class="js-company-form logestechs-form-container">
                            <div class="logestechs-input-container">
                                <input type="text" name="domain" required>
                                <label><?php echo __( 'Domain', 'logestechs' ) ?></label>
                            </div>
                            <div class="logestechs-input-container">
                                <input type="text" name="email" required>
                                <label><?php echo __( 'Email', 'logestechs' ) ?></label>
                            </div>
                            <div class="logestechs-input-container">
                                <input type="password" name="password" required>
                                <label><?php echo __( 'Password', 'logestechs' ) ?></label>
                            </div>
                            <button class="logestechs-primary-btn js-logestechs-add-company logestechs-add-btn">
                                <img src="<?php echo logestechs_image( 'plus.svg' ) ?>" alt="plus"> <?php echo __( 'Add Company', 'logestechs' ) ?>
                            </button>
                            <button class="logestechs-primary-btn js-logestechs-update-company logestechs-add-btn" style="display: none;">
                            <img src="<?php echo logestechs_image( 'save.svg' ) ?>" alt="save"> <?php echo __( 'Update Company', 'logestechs' ) ?>
                            </button>
                        </form>
                    </div>
                    <div class="logestechs-popup-main">
                        <div class="js-logestechs-companies logestechs-row-container">
                            <div class="logestechs-skeleton-row" style="margin: 15px 50px">
                                <div class="logestechs-skeleton-loader" style="width: 45px; height: 45px;"></div>
                                <div class="logestechs-skeleton-column">
                                    <div class="logestechs-skeleton-loader" style="width: 230px; height: 20px"></div>
                                    <div class="logestechs-skeleton-loader" style="width: 100px; height: 15px"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    if ( ! $manage_only ) {
                        ?>
                        <div class="logestechs-popup-footer">
                            <button class="js-logestechs-assign-company logestechs-primary-btn disabled" disabled><?php echo __( 'Assign Company', 'logestechs' ) ?></button>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
            echo ob_get_clean();
        }
    }
}
