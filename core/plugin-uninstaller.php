<?php
// If uninstall is not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

// Place your uninstall logic here. It could be deleting plugin options,
// dropping tables, or removing posts or pages that were created by the plugin.

/*
// For example, to remove a database table:
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}logestechs_table");

// To delete plugin options:
delete_option('logestechs_option');

// To delete user metadata:
delete_metadata('user', 0, 'logestechs_user_meta', '', true);
 */

if ( ! class_exists( 'Logestechs_Plugin_Uninstaller' ) ) {

    class Logestechs_Plugin_Uninstaller {

        /**
         * The method that runs during plugin deactivation
         *
         * This should include any operations necessary when the plugin is deactivated,
         * like removing scheduled events (cron jobs).
         *
         * @since    1.0.0
         */
        public static function uninstall() {
            require_once LOGESTECHS_PLUGIN_PATH . 'include/credentials/credentials-storage.php';

            $credentials_storage = Logestechs_Credentials_Storage::get_instance();
            $credentials_storage->drop_table();

            // Delete option
            delete_option( 'logestechs_encryption_key' );
        }

    }
}
