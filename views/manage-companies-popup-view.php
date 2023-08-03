<?php
/**
 * The file that handles the rendering of the order companies popup
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/views
 */

if (!class_exists('Logestechs_Manage_Companies_Popup_View')) {

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
        public function render($manage_only = true) {
            // Fetch any necessary data using $order_id
            // Render the popup HTML. Ensure you escape all output!
            $id = $manage_only ? 'logestechs-order-companies-popup' : 'logestechs-order-transfer-popup';
            ob_start();
            ?>
            <div id="<?php echo $id; ?>" class="logestechs-popup" style="display: none;">
                <div class="logestechs-popup-overlay"></div>
                <div class="logestechs-popup-content">
                    <div class="logestechs-popup-head">
                        <p class="logestechs-popup-label"><?php _e('Manage Companies', 'logestechs'); ?></p>
                        <div class="logestechs-close-btn-wrapper">
                            <button class="close-popup close-btn">
                                <span class="bar"></span>
                                <span class="bar"></span>
                            </button>
                        </div>
                        <div class="logestechs-form-container">
                            <div class="logestechs-input-container">
                                <input type="text" id="domain" required>
                                <label for="domain"><?php echo __('Domain', 'logestechs') ?></label>
                            </div>
                            <div class="logestechs-input-container">
                                <input type="email" id="email" required>
                                <label for="email"><?php echo __('Email', 'logestechs') ?></label>
                            </div>
                            <div class="logestechs-input-container">
                                <input type="password" id="password" required>
                                <label for="password"><?php echo __('Password', 'logestechs') ?></label>
                            </div>
                            <button class="logestechs-primary-btn">+ <?php echo __('Add Company', 'logestechs') ?></button>
                        </div>
                    </div>
                    <div class="logestechs-popup-main">
                        <div class="logestechs-row-container">
                            <div class="logestechs-row">
                                <div class="logestechs-row-main">
                                    <img src="<?php echo logestechs_image('logo.jpeg') ?>" alt="image">
                                    <p class="logestechs-row-main-text">KSA Demo</p>
                                    <span>#213</span>
                                </div>
                                <img class="logestechs-delete-icon" src="<?php echo logestechs_image('trash.svg') ?>" alt="image">
                            </div>
                            <div class="logestechs-row">
                                <div class="logestechs-row-main">
                                    <img src="<?php echo logestechs_image('logo.jpeg'); ?>" alt="image">
                                    <p class="logestechs-row-main-text">Testing Company</p>
                                    <span>#133</span>
                                </div>
                                <img class="logestechs-delete-icon" src="<?php echo logestechs_image('trash.svg') ?>" alt="image">
                            </div>
                        </div>
                    </div>
                    <?php 
                    if( ! $manage_only ) {
                        ?>
                        <div class="logestechs-popup-footer">
                            <button class="logestechs-primary-btn"><?php echo __('Assign Company', 'logestechs') ?></button>
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
