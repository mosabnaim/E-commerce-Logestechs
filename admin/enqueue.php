<?php
/**
 * The file that handles enqueuing of styles and scripts for the plugin
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/admin
 */

if (!class_exists('Logestechs_Enqueue')) {

    class Logestechs_Enqueue {

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {
            add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        }

        /**
         * Register the stylesheets for the admin area.
         *
         * @since    1.0.0
         */
        public function enqueue_styles() {
            // Use wp_enqueue_style to register and enqueue the style
            // wp_enqueue_style('logestechs-admin', plugin_dir_url(__FILE__) . 'css/admin-style.css', array(), null, 'all');
        }

        /**
         * Register the JavaScript for the admin area.
         *
         * @since    1.0.0
         */
        public function enqueue_scripts() {
            // Use wp_enqueue_script to register and enqueue the script
            // wp_enqueue_script('logestechs-admin', plugin_dir_url(__FILE__) . 'js/admin-script.js', array('jquery'), null, true);
        }

    }

}
