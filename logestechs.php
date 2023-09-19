<?php
/**
 * Plugin Name: Logestechs for WooCommerce
 * Plugin URI: https://logestechs.com/en/
 * Description: Logestechs is an advanced application suite for order and delivery management. It supports Arabic and English, integrates with various services, and offers convenience for your operations.
 * Version: 1.0.0
 * Author: Eleven Stars
 * Author URI: https://logestechs.com
 * License: GPL v2 or later
 * Text Domain: logestechs-enhancement
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) or die();

// Define plugin paths and URLs for easy access
define( 'LOGESTECHS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'LOGESTECHS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'LOGESTECHS_PLUGIN_BASENAME', plugin_basename( dirname( __FILE__ ) ) ); // Basename

// Plugin activation and deactivation.
register_activation_hook( __FILE__, [ 'Logestechs_Plugin_Activator', 'activate' ] );

// Require plugin classes using autoloader
require_once LOGESTECHS_PLUGIN_PATH . 'utils/helper-functions.php';
require_once LOGESTECHS_PLUGIN_PATH . 'autoloader.php';
$autoloader = new Logestechs_Autoloader;
$autoloader->init();

$logestechs_core = new Logestechs_Plugin_Core();
$logestechs_core->run();
