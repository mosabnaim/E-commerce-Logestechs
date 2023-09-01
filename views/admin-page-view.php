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
        public function render($args = []) {
            $statuses = $args['statuses'] ?? [];
            $status_filter = $_GET['status_filter'] ?? '';
            $display_status = Logestechs_Config::STATUS_ARRAY[$status_filter] ?? esc_html__('All', 'logestechs');
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
                <div class="logestechs-table-head js-logestechs-table-head">
                    <div class="logestechs-flex">
                        <h3><?php esc_html_e('Manage Shipments', 'logestechs'); ?></h3>
                        <span class="js-logestechs-count"></span>
                    </div>
                    <div class="logestechs-flex">
                        <div class="js-logestechs-sync logestechs-action-btn">
                            <img src="<?php echo esc_url(logestechs_image('sync.svg')); ?>" alt="<?php esc_attr_e('Sync Icon', 'logestechs'); ?>">
                            <p><?php esc_html_e('Sync Status', 'logestechs'); ?></p>
                        </div>
                        <div class="logestechs-relative">
                            <input class="logestechs-filter-input" type="text" name="date_range" id="date_range" placeholder="<?php esc_attr_e('Date', 'logestechs'); ?>">
                            <img class="logestechs-calendar-icon" src="<?php echo esc_url(logestechs_image('calendar.svg')); ?>" alt="<?php esc_attr_e('Search Icon', 'logestechs'); ?>">
                        </div>
                        <div class="logestechs-search-wrapper">
                            <input class="logestechs-filter-input js-logestechs-search" type="text" placeholder="<?php esc_attr_e('Search...', 'logestechs'); ?>">
                            <img class="logestechs-search-icon" src="<?php echo esc_url(logestechs_image('search.svg')); ?>" alt="<?php esc_attr_e('Search Icon', 'logestechs'); ?>">
                        </div>
                    </div>
                </div>
                <div class="logestechs-table-head js-logestechs-selection-actions" style="display: none;">
                    <div class="logestechs-flex">
                        <h3><?php esc_html_e('Manage Shipments', 'logestechs'); ?></h3>
                        <div class="logestechs-divider"></div>
                        <p class="logestechs-selected-text"><?php esc_html_e('You have selected', 'logestechs'); ?> <span class="js-logestechs-selection-count"></span> <?php esc_html_e('packages', 'logestechs'); ?></p>
                        <div class="js-logestechs-cancel-selection logestechs-action-btn">
                            <p><?php esc_html_e('Cancel', 'logestechs'); ?></p>
                        </div>
                        <div class="js-logestechs-select-all-btn logestechs-action-btn">
                            <p><?php esc_html_e('Select All', 'logestechs'); ?></p>
                        </div>
                        <div class="js-logestechs-bulk-print logestechs-action-btn">
                            <img src="<?php echo esc_url(logestechs_image('printer.svg')); ?>" alt="<?php esc_attr_e('Print Icon', 'logestechs'); ?>">
                            <p><?php esc_html_e('Print Invoice', 'logestechs'); ?></p>
                        </div>
                        <div class="js-logestechs-bulk-transfer logestechs-action-btn">
                            <img src="<?php echo esc_url(logestechs_image('package.svg')); ?>" alt="<?php esc_attr_e('Transfer Icon', 'logestechs'); ?>">
                            <p><?php esc_html_e('Transfer Packages', 'logestechs'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Orders table section -->
            <div class="logestechs-table-wrapper">
                <table id="logestechs_orders_table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" class="js-logestechs-select-all" name="all_orders"></th>
                            <th><span class="js-logestechs-sort" data-sort-by="id"><?php esc_html_e('Order No.', 'logestechs'); ?></span></th>
                            <th><span class="js-logestechs-sort" data-sort-by="date"><?php esc_html_e('Date', 'logestechs'); ?></span></th>
                            <th><span class="js-logestechs-sort" data-sort-by="barcode_id"><?php esc_html_e('AWB No.', 'logestechs'); ?></span></th>
                            <th><span class="js-logestechs-sort" data-sort-by="company"><?php esc_html_e('Shipping Company', 'logestechs'); ?></span></th>
                            <th class="logestechs-relative">
                                <div class="logestechs-dropdown-filter">
                                    <span class="js-logestechs-sort logestechs-status-label" data-sort-by="status"><?php esc_html_e('Status', 'logestechs'); ?></span>
                                    <div class="logestechs-status-dropdown">
                                        <p class="logestechs-status-filter"><?php echo $display_status ?></p>
                                        <img src="<?php echo esc_url(logestechs_image('arrow-down.svg')); ?>" alt="'down arrow'">
                                    </div>
                                </div>
                                <div class="logestechs-status-results js-logestechs-status-filter-dropdown" style="display: none;">
                                    <div class="logestechs-status-search-wrapper">
                                        <input type="text" id="logestechs-status-search" class="logestechs-dropdown-input" placeholder="<?php _e('Filter by status...', 'logestechs'); ?>" required>
                                        <img src="<?php echo esc_url(logestechs_image('search.svg')); ?>" alt="'search'">
                                    </div>
                                    <div class="logestechs-status-options">
                                        <div data-value=""><?php esc_html_e('All', 'logestechs') ?></div>
                                        <?php
                                        foreach ($statuses as $status) {
                                            ?>
                                            <div data-value="<?php echo $status; ?>"><?php echo Logestechs_Config::STATUS_ARRAY[$status] ?? 'N/A' ?></div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </th>
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
