<?php
// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

define( 'LOGESTECHS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
require_once plugin_dir_path( __FILE__ ) . 'core/plugin-uninstaller.php';
Logestechs_Plugin_Uninstaller::uninstall();
