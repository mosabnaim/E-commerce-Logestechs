<?php
/**
 * The file that handles custom order metaboxes
 *
 * This file is used to handle custom order metaboxes for Logestechs interactions such as transfer order button and tracking info.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/include/orders
 */

if (!class_exists('Logestechs_Order_Metabox')) {

    class Logestechs_Order_Metabox {

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {
            add_action('add_meta_boxes', array($this, 'add_logestechs_metabox'));
            add_action('save_post', array($this, 'save_logestechs_metabox'));
        }

        /**
         * Add Logestechs metabox.
         *
         * @since    1.0.0
         */
        public function add_logestechs_metabox() {
            add_meta_box(
                'logestechs_metabox_id',  // Unique ID
                __('Logestechs Actions', 'logestechs'),  // Box title
                array($this, 'logestechs_metabox_html'),  // Content callback
                'shop_order'  // post type
            );
        }

        /**
         * Display the Logestechs metabox.
         *
         * @param WP_Post $post The object for the current post/page.
         * @since    1.0.0
         */
        public function logestechs_metabox_html($post) {
            // Display the metabox with Logestechs actions
            // include_once LOGESTECHS_PATH . 'views/order-transfer-popup-view.php';
        }

        /**
         * Save Logestechs metabox.
         *
         * @param int $post_id The ID of the post being saved.
         * @since    1.0.0
         */
        public function save_logestechs_metabox($post_id) {
            // Save the metabox data
            // Do some operations related to saving metabox data
        }
    }
}
