<?php
/**
 * The file that defines the core plugin class
 *
 * This is used to define internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/core
 */

if ( ! class_exists( 'Logestechs_Autoloader' ) ) {

    class Logestechs_Autoloader {

        /**
         * An array containing the mapping of directories and class names.
         *
         * @since    1.0.0
         * @access   private
         * @var      array $classes_to_load    Classes to be autoloaded.
         */
        private $classes_to_load = [
            'admin' => [
                'Logestechs_Admin_Page',
                'Logestechs_Enqueue'
            ],
            'api' => [
                'Logestechs_Api_Handler',
                'Logestechs_Api_Error_Handler'
            ],
            'core' => [
                'Logestechs_Plugin_Activator',
                'Logestechs_Plugin_Core',
                'Logestechs_Missing_Woocommerce'
            ],
            'include' => [
                'Logestechs_Config',
            ],
            'include/credentials' => [
                'Logestechs_Credentials_Manager',
                'Logestechs_Credentials_Storage'
            ],
            'include/orders' => [
                'Logestechs_Order_Handler',
                'Logestechs_Order_Metabox',
                'Logestechs_Popup_Handler'
            ],
            'security' => [
                'Logestechs_Data_Encryption',
                'Logestechs_Data_Validator',
                'Logestechs_Input_Sanitizer',
                'Logestechs_Security_Manager'
            ],
            'utils' => [
                'Logestechs_Debugger',
                'Logestechs_Helper_Functions'
            ],
            'views' => [
                'Logestechs_Admin_Page_View',
                'Logestechs_Manage_Companies_Popup_View',
                'Logestechs_Tracking_Details_Popup_View',
                'Logestechs_Woocommerce_List_View',
                'Logestechs_Woocommerce_Metabox_View',
            ]
        ];

        /**
         * Initialize the autoloader.
         *
         * @since    1.0.0
         */
        public function init() {
            // Code that runs during plugin initialization
            spl_autoload_register( [$this, 'autoload'] );
        }

        /**
         * Autoloads classes based on the $classes_to_load array.
         *
         * @since    1.0.0
         * @param    string $class_name    The name of the class to be loaded.
         */
        public function autoload( $class_name ) {
            $file_path = LOGESTECHS_PLUGIN_PATH . "utils/debugger.php";
            if ( file_exists( $file_path ) ) {
                require_once $file_path;
            }

            // Convert class name to file name. e.g., Logestechs_Logger => logger
            $file_name = strtolower( preg_replace( '/^Logestechs_/', '', $class_name ) ) . '.php';
            $file_name = str_replace( '_', '-', $file_name );
            foreach ( $this->classes_to_load as $directory => $classes ) {
                if ( in_array( $class_name, $classes ) ) {
                    // Construct the full file path
                    $file_path = LOGESTECHS_PLUGIN_PATH . "{$directory}/{$file_name}";
                    if ( file_exists( $file_path ) ) {
                        require_once $file_path;
                    }
                    break;
                }
            }
        }

    }

}
