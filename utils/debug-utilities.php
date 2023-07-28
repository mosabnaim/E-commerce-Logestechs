<?php
/**
 * The file that handles debugging utilities
 *
 * This file is used to handle various debugging utilities and functions.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/utils
 */

if (!function_exists('logestechs_debug_log')) {

    /**
     * Log debug message.
     *
     * @param mixed $message The message to log.
     */
    function logestechs_debug_log($message) {
        // Implement debug logging
        // Check if WP_DEBUG_LOG is enabled
        // If it is, log the message
        // if (WP_DEBUG_LOG) {
        //     error_log(print_r($message, true));
        // }
    }
}

if (!function_exists('logestechs_debug_dump')) {

    /**
     * Dump variable for debugging.
     *
     * @param mixed $var The variable to dump.
     */
    function logestechs_debug_dump($var) {
        // Implement variable dumping
        // Check if WP_DEBUG is enabled
        // If it is, dump the variable
        // if (WP_DEBUG) {
        //     var_dump($var);
        // }
    }
}
