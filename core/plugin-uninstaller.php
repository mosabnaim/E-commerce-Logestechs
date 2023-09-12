<?php
/**
 * The file that handles the uninstallation of the plugin
 *
 * This class defines all code necessary to run during the plugin's uninstallation.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/core
 */

// If uninstall is not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

if ( ! class_exists( 'Logestechs_Plugin_Uninstaller' ) ) {

    class Logestechs_Plugin_Uninstaller {

        /**
         * The method that runs during plugin uninstallation
         *
         * This should include any operations necessary when the plugin is uninstalled,
         * like removing database tables, options, or scheduled events (cron jobs).
         *
         * @since    1.0.0
         */
        public static function uninstall() {
            require_once LOGESTECHS_PLUGIN_PATH . 'include/credentials/credentials-storage.php';
        }
    }

    // Call the uninstall method
    Logestechs_Plugin_Uninstaller::uninstall();
}
