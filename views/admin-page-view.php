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
        private $orders;
        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct($orders) {
            // You might want to enqueue necessary scripts or styles related to this view here.
            $this->orders = $orders;
        }

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
                        <span><?php echo count($this->orders) ?></span>
                    </div>
                    <div class="logestechs-flex">
                        <div class="logestechs-sync-btn">
                            <img src="<?php echo logestechs_image('sync.svg') ?>" alt="">
                            <p><?php _e( 'Sync Status', 'logestechs' )?></p>
                        </div>
                        <input class="logestechs-filter-input" type="date" name="" id="" placeholder="Date">
                        <div class="logestechs-search-wrapper">
                            <input class="logestechs-filter-input" type="text" placeholder="Search...">
                            <img class="logestechs-search-icon" src="<?php echo logestechs_image('search.svg'); ?>" alt="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="logestechs-table-wrapper">
                <table id="logestechs_orders_table">
                    <thead>
                        <tr>
                            <th>Order No.</th>
                            <th>Date</th>
                            <th>AWB No.</th>
                            <th>Shipping Company</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (empty($this->orders)) { 
                            ?>
                            <tr>
                                <td colspan="6"><?php _e('No orders found.', 'logestechs'); ?></td>
                            </tr>
                            <?php 
                        } else {
                            foreach ($this->orders as $order_post) {
                                $order = wc_get_order($order_post);
                                $order_id = $order->get_id();
                                $order_barcode = get_post_meta($order_id, 'logestechs_order_barcode', true);
                                $order_logestechs_id = get_post_meta($order_id, 'logestechs_order_id', true);
                                $company_name = get_post_meta($order_id, 'logestechs_company_name', true);
                                $date = get_post_meta($order_id, 'logestechs_date', true);
                                $status = get_post_meta($order_id, 'logestechs_order_status', true);
                                ?>
                                <tr>
                                    <td>#<?php echo esc_html($order_id); ?></td>
                                    <td><?php echo esc_html($date); ?></td>
                                    <td>#<?php echo esc_html($order_barcode); ?></td>
                                    <td><?php echo esc_html($company_name); ?></td>
                                    <td>
                                        <span class="<?php echo 'logestechs-'. esc_html($status); ?>">
                                            <?php echo ucfirst(esc_html($status)); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="logestechs-dropdown">
                                            <img src="<?php echo logestechs_image('dots.svg'); ?>" />
                                            <div class="logestechs-dropdown-content">
                                                <div class="js-logestechs-print" data-order-id="<?php echo $order_id; ?>">Print Invoice</div>
                                                <div class="js-open-details-popup" data-order-id="<?php echo $order_id; ?>">Track</div>
                                                <div class="js-logestechs-cancel" data-order-id="<?php echo $order_id; ?>">Cancel</div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
           echo ob_get_clean();
        }
    }
}
