<?php
// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

// Place your uninstall logic here. It could be deleting plugin options, 
// dropping tables, or removing posts or pages that were created by the plugin.

function logestechs_uninstall() {
    /*
    // For example, to remove a database table:
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}logestechs_table");

    // To delete plugin options:
    delete_option('logestechs_option');
    
    // To delete user metadata:
    delete_metadata('user', 0, 'logestechs_user_meta', '', true);
    */
}

logestechs_uninstall();
