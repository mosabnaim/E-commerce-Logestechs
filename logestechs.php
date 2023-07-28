<?php
/**
 * Plugin Name: Logestechs for WooCommerce
 * Plugin URI: https://yourwebsite.com/logestechs-enhancement
 * Description: This plugin enhances WooCommerce capabilities by interfacing with Logestechs multi-domain shipping platform.
 * Version: 1.0.0
 * Author: Logestechs
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * Text Domain: logestechs-enhancement
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) or die();

// Define plugin paths and URLs for easy access
define('LOGESTECHS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('LOGESTECHS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include the plugin activator, deactivator, and uninstall classes
// require_once(LOGESTECHS_PLUGIN_PATH . 'core/plugin-activator.php');
// require_once(LOGESTECHS_PLUGIN_PATH . 'core/plugin-deactivator.php');
// require_once(LOGESTECHS_PLUGIN_PATH . 'core/plugin-uninstall.php');

// Include the main plugin class file
require_once(LOGESTECHS_PLUGIN_PATH . 'core/plugin-core.php');

// Plugin activation and deactivation hooks
// register_activation_hook(__FILE__, array('Logestechs_Plugin_Activator', 'activate'));
// register_deactivation_hook(__FILE__, array('Logestechs_Plugin_Deactivator', 'deactivate'));

// Initialize the plugin
$logestechs = new Logestechs_Plugin_Core();
$logestechs->run();