<?php
/**
 * The file that handles the rendering of the dedicated Logestechs admin page
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/views
 */

if (!class_exists('Logestechs_Admin_Page_View')) {

    class Logestechs_Admin_Page_View {
        /**
         * Render the Logestechs admin page
         */
        public function render() {
            // Fetch any necessary data
            // Render the admin page HTML. Ensure you escape all output!
           ob_start();
           ?>
            <div class="logestechs-header">
                <div class="logestechs-logo-background">
                    <img src="<?php echo logestechs_image('logo-bg.svg'); ?>" alt="">
                </div>
                <div class="logestechs-logo-wrapper">
                    <div class="logestechs-logo">
                        <img src="<?php echo esc_url( Logestechs_Config::PLUGIN_LOGO ) ?>" alt="logo">
                    </div>
                    <p class="logestechs-primary-text"><?php echo esc_html( Logestechs_Config::PLUGIN_NAME ) ?></p>
                </div>
                <button id="logestechs-transfer-order" class="js-open-companies-popup logestechs-primary-btn"><?php _e( 'Manage Companies', 'logestechs' )?></button>
            </div>
            <div class="logestechs-table-wrapper">
                <div class="logestechs-table-head">
                    <div class="logestechs-flex">
                        <h3><?php _e( 'Manage Shipments', 'logestechs' )?></h3>
                        <span class="js-logestechs-count"></span>
                    </div>
                    <div class="logestechs-flex">
                        <div class="js-logestechs-sync logestechs-sync-btn">
                            <img src="<?php echo logestechs_image('sync.svg') ?>" alt="">
                            <p><?php _e( 'Sync Status', 'logestechs' )?></p>
                        </div>
                        <input class="logestechs-filter-input" type="text" name="date_range" id="date_range" placeholder="Date">
                        <div class="logestechs-search-wrapper">
                            <input class="logestechs-filter-input js-logestechs-search" type="text" placeholder="Search...">
                            <img class="logestechs-search-icon" src="<?php echo logestechs_image('search.svg'); ?>" alt="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="logestechs-table-wrapper">
                <table id="logestechs_orders_table">
                    <thead>
                        <tr>
                            <th><span class="js-logestechs-sort" data-sort-by="id">Order No.</span></th>
                            <th><span class="js-logestechs-sort" data-sort-by="date">Date</span></th>
                            <th><span class="js-logestechs-sort" data-sort-by="barcode_id">AWB No.</span></th>
                            <th><span class="js-logestechs-sort" data-sort-by="company">Shipping Company</span></th>
                            <th><span class="js-logestechs-sort" data-sort-by="status">Status</span></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 100px;"><div class="logestechs-skeleton-loader" style="width: 60px;"></div></td>
                            <td><div class="logestechs-skeleton-loader" style="width: 120px;"></div></td>
                            <td><div class="logestechs-skeleton-loader" style="width: 120px;"></div></td>
                            <td><div class="logestechs-skeleton-loader" style="width: 120px;"></div></td>
                            <td><div class="logestechs-skeleton-loader" style="width: 120px;"></div></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <div class="logestechs-pagination"></div>
            </div>
            <?php
           echo ob_get_clean();
        }
    }
}
