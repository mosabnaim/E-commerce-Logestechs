<?php
/**
 * The file that handles deactivation of the plugin
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/core
 */

if (!class_exists('Logestechs_Plugin_Deactivator')) {

    class Logestechs_Plugin_Deactivator {

        /**
         * The method that runs during plugin deactivation
         *
         * This should include any operations necessary when the plugin is deactivated,
         * like removing scheduled events (cron jobs).
         *
         * @since    1.0.0
         */
        public static function deactivate() {
            // Example: Remove a scheduled event (cron job)
            // wp_clear_scheduled_hook('logestechs_scheduled_event');
        }

    }
}
