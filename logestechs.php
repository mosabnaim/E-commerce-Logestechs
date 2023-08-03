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
define( 'LOGESTECHS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define( 'LOGESTECHS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define('LOGESTECHS_PLUGIN_BASENAME', plugin_basename(dirname(__FILE__))); // Basename


// Require plugin classes using autoloader
require_once LOGESTECHS_PLUGIN_PATH . 'utils/helper-functions.php';
require_once LOGESTECHS_PLUGIN_PATH . 'autoloader.php';
$autoloader = new Logestechs_Autoloader;
$autoloader->init();

// Plugin activation and deactivation.
register_activation_hook(__FILE__, array('Logestechs_Plugin_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('Logestechs_Plugin_Deactivator', 'deactivate'));
register_uninstall_hook(__FILE__, array('Logestechs_Plugin_Uninstall', 'uninstall'));


// Start the core functionality.
$logestechs_core = new Logestechs_Plugin_Core();
$logestechs_core->run();