<?php
/**
 * The file that handles the rendering of the dedicated Logestechs admin page
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/views
 */

if (!class_exists('Logestechs_Settings_Page_View')) {

    class Logestechs_Settings_Page_View {
        /**
         * Render the Logestechs admin page
         */
        public function render() {
            // Fetch the existing settings values
            $store_phone_number = get_option('logestechs_store_phone_number');
            $business_name = get_option('logestechs_business_name');
            $store_owner = get_option('logestechs_store_owner');
            $store_address1 = get_option('woocommerce_store_address');
            $store_address2 = get_option('woocommerce_store_address_2');
            $region_id = get_option('logestechs_store_region_id');
            $city_id = get_option('logestechs_store_city_id');
            $village_id = get_option('logestechs_store_village_id');
            $village_name = get_option('logestechs_store_village_name');

            // Render the admin page HTML. Ensure you escape all output!
           ob_start();
           ?>
            <div class="logestechs-settings-page">
            <div class="logestechs-header">
                <div class="logestechs-logo-background">
                    <img src="<?php echo logestechs_image('logo-bg.svg'); ?>" alt="">
                </div>
                <div class="logestechs-logo-wrapper">
                    <div class="logestechs-logo">
                        <img src="<?php echo esc_url( Logestechs_Config::PLUGIN_LOGO ) ?>" alt="logo">
                    </div>
                    <p class="logestechs-primary-text"><?php echo esc_html( Logestechs_Config::PLUGIN_NAME ) ?> Settings</p>
                </div>
                <button id="logestechs-transfer-order"
                    class="js-logestechs-submit-settings logestechs-primary-btn"><?php _e( 'Save Changes', 'logestechs' )?></button>
            </div>
            <?php
            if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
                ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php _e('Settings have been saved.', 'logestechs'); ?></p>
                </div>
                <?php
            }
            ?>
            <div class="logestechs-settings">
                <form method="post" action="options.php">
                    <?php settings_fields('logestechs_settings'); ?>
                    <div class="logestechs-card">
                        <h2>Store Settings</h2>
                        <p>These settings are necessary to be able to transfer orders to logestechs successfully.</p>
                        <!-- Business Name -->
                        <div class="logestechs-field">
                            <label><?php _e('Business Name', 'logestechs'); ?></label>
                            <input type="text" name="logestechs_business_name" value="<?php echo esc_attr($business_name); ?>" />
                        </div>
                        <!-- Store Owner -->
                        <div class="logestechs-field">
                            <label><?php _e('Store Owner', 'logestechs'); ?></label>
                            <input type="text" name="logestechs_store_owner" value="<?php echo esc_attr($store_owner); ?>" />
                        </div>
                        <!-- Store Phone Number -->
                        <div class="logestechs-field">
                            <label><?php _e('Store Phone Number', 'logestechs'); ?></label>
                            <input type="text" name="logestechs_store_phone_number"
                                value="<?php echo esc_attr($store_phone_number); ?>" />
                        </div>
                        <!-- Address Line 1 -->
                        <div class="logestechs-field">
                            <label><?php _e('Store Address Line 1', 'logestechs'); ?></label>
                            <input type="text" name="woocommerce_store_address" value="<?php echo esc_attr($store_address1); ?>" />
                        </div>
                        <!-- Address Line 2 -->
                        <div class="logestechs-field">
                            <label><?php _e('Store Address Line 2', 'logestechs'); ?></label>
                            <input type="text" name="woocommerce_store_address_2"
                                value="<?php echo esc_attr($store_address2); ?>" />
                        </div>
                        <!-- Store Village -->
                        <div class="logestechs-field">
                            <label><?php _e('Store Village', 'logestechs'); ?></label>
                            <div class="logestechs-search-wrapper">
                                <input type="text" id="logestechs-village-search" class="logestechs-dropdown-input"
                                    name="logestechs_store_village_name" placeholder="Search for a village..."
                                    value="<?php echo $village_name; ?>">
                                <div class="logestechs-village-results" style="display: none;"></div>
                                <input type="hidden" name="logestechs_store_region_id" class="js-logestechs-selected-region"
                                    value="<?php echo $region_id; ?>">
                                <input type="hidden" name="logestechs_store_city_id" class="js-logestechs-selected-city"
                                    value="<?php echo $city_id; ?>">
                                <input type="hidden" name="logestechs_store_village_id" class="js-logestechs-selected-village"
                                    value="<?php echo $village_id; ?>">
                            </div>
                        </div>
                    </div>
                    <button id="logestechs-settings-submit" type="submit" style="display: none;"></button>
                </form>
            </div>
            </div>
            <?php
           echo ob_get_clean();
        }
    }
}
