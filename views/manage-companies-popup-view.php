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
            ?>
            <div id="<?php echo $container_id; ?>" class="logestechs-popup" style="display: none;">
                <div class="logestechs-popup-overlay"></div>
                <div class="logestechs-popup-content">
                    <div class="logestechs-popup-head">
                        <p class="logestechs-popup-label"><?php _e( 'Manage Companies', 'logestechs' );?></p>
                        <div class="logestechs-close-btn-wrapper">
                            <button class="close-popup close-btn">
                                <span class="bar"></span>
                                <span class="bar"></span>
                            </button>
                        </div>
                        <form class="js-company-form logestechs-form-container">
                            <div class="logestechs-input-container">
                                <input type="text" id="domain" required>
                                <label for="domain"><?php echo __( 'Domain', 'logestechs' ) ?></label>
                            </div>
                            <div class="logestechs-input-container">
                                <input type="text" id="email" required>
                                <label for="email"><?php echo __( 'Email', 'logestechs' ) ?></label>
                            </div>
                            <div class="logestechs-input-container">
                                <input type="password" id="password" required>
                                <label for="password"><?php echo __( 'Password', 'logestechs' ) ?></label>
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
                            <p class="logestechs-text-center">Loading...</p>
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
