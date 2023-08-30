<?php
/**
 * The file that handles the rendering of the dedicated Logestechs admin page.
 *
 * This file is responsible for producing the output of the Logestechs admin page, 
 * presenting relevant controls and data visualizations to the user.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/views
 */

if (!class_exists('Logestechs_Admin_Page_View')) {

    class Logestechs_Admin_Page_View {

        /**
         * Renders the Logestechs admin page.
         *
         * This method is responsible for generating the HTML structure of the admin page,
         * ensuring all necessary components and data are presented.
         *
         * @since    1.0.0
         */
        public function render() {
            
            // Start the output buffer.
            ob_start();
            ?>
            <!-- Header section -->
            <div class="logestechs-header">
                <div class="logestechs-logo-background">
                    <img src="<?php echo esc_url(logestechs_image('logo-bg.svg')); ?>" alt="">
                </div>
                <div class="logestechs-logo-wrapper">
                    <div class="logestechs-logo">
                        <img src="<?php echo esc_url(Logestechs_Config::PLUGIN_LOGO); ?>" alt="<?php esc_attr_e('Logestechs Logo', 'logestechs'); ?>">
                    </div>
                    <p class="logestechs-primary-text"><?php echo esc_html(Logestechs_Config::PLUGIN_NAME); ?></p>
                </div>
                <button id="logestechs-transfer-order" class="js-open-companies-popup logestechs-primary-btn"><?php esc_html_e('Manage Companies', 'logestechs'); ?></button>
            </div>
            
            <!-- Table control section -->
            <div class="logestechs-table-wrapper">
                <div class="logestechs-table-head">
                    <div class="logestechs-flex">
                        <h3><?php esc_html_e('Manage Shipments', 'logestechs'); ?></h3>
                        <span class="js-logestechs-count"></span>
                    </div>
                    <div class="logestechs-flex">
                        <div class="js-logestechs-sync logestechs-sync-btn">
                            <img src="<?php echo esc_url(logestechs_image('sync.svg')); ?>" alt="<?php esc_attr_e('Sync Icon', 'logestechs'); ?>">
                            <p><?php esc_html_e('Sync Status', 'logestechs'); ?></p>
                        </div>
                        <input class="logestechs-filter-input" type="text" name="date_range" id="date_range" placeholder="<?php esc_attr_e('Date', 'logestechs'); ?>">
                        <div class="logestechs-search-wrapper">
                            <input class="logestechs-filter-input js-logestechs-search" type="text" placeholder="<?php esc_attr_e('Search...', 'logestechs'); ?>">
                            <img class="logestechs-search-icon" src="<?php echo esc_url(logestechs_image('search.svg')); ?>" alt="<?php esc_attr_e('Search Icon', 'logestechs'); ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Orders table section -->
            <div class="logestechs-table-wrapper">
                <table id="logestechs_orders_table">
                    <thead>
                        <tr>
                            <th><span class="js-logestechs-sort" data-sort-by="id"><?php esc_html_e('Order No.', 'logestechs'); ?></span></th>
                            <th><span class="js-logestechs-sort" data-sort-by="date"><?php esc_html_e('Date', 'logestechs'); ?></span></th>
                            <th><span class="js-logestechs-sort" data-sort-by="barcode_id"><?php esc_html_e('AWB No.', 'logestechs'); ?></span></th>
                            <th><span class="js-logestechs-sort" data-sort-by="company"><?php esc_html_e('Shipping Company', 'logestechs'); ?></span></th>
                            <th><span class="js-logestechs-sort" data-sort-by="status"><?php esc_html_e('Status', 'logestechs'); ?></span></th>
                            <th><?php esc_html_e('Action', 'logestechs'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Skeleton loading UI for initial data load -->
                        <tr>
                            <td style="width: 100px;"><div class="logestechs-skeleton-loader" style="width: 60px;"></div></td>
                            <td><div class="logestechs-skeleton-loader" style="width: 120px;"></div></td>
                            <td><div class="logestechs-skeleton-loader" style="width: 120px;"></div></td>
                            <td><div class="logestechs-skeleton-loader" style="width: 120px;"></div></td>
                            <td><div class="logestechs-skeleton-loader" style="width: 120px;"></div></td>
                            <td></td>
                        </tr>
                        <!-- You can insert dynamic order rows here, ensuring they're also escaped properly -->
                    </tbody>
                </table>
                <!-- Pagination controls -->
                <div class="logestechs-pagination"></div>
            </div>

            <?php

            // End the output buffer and display the content.
            echo ob_get_clean();
        }
    }
}
