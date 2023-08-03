<?php
/**
 * The file that defines helper functions
 *
 * This file is used to define various helper functions that can be used throughout the Logestechs plugin.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/utils
 */

if ( ! function_exists( 'logestechs_get_option' ) ) {

    /**
     * Get Logestechs option.
     *
     * @param string $option The option to get.
     * @param mixed $default The default value to return if the option doesn't exist.
     * @return mixed The option value.
     */
    function logestechs_get_option( $option, $default = null ) {
        // Implement option fetching
        // Get the option from the database
        // If it doesn't exist, return the default value
        // return get_option("logestechs_$option", $default);
    }
}

if ( ! function_exists( 'logestechs_update_option' ) ) {

    /**
     * Update Logestechs option.
     *
     * @param string $option The option to update.
     * @param mixed $value The new value for the option.
     */
    function logestechs_update_option( $option, $value ) {
        // Implement option updating
        // Update the option in the database
        // update_option("logestechs_$option", $value);
    }
}
if ( ! function_exists( 'logestechs_asset' ) ) {

    /**
     * Update Logestechs option.
     *
     * @param string $option The option to update.
     * @param mixed $value The new value for the option.
     */
    function logestechs_asset( $asset ) {
        return LOGESTECHS_PLUGIN_URL . 'assets/' . $asset;
    }
}
if ( ! function_exists( 'logestechs_image' ) ) {

    /**
     * Update Logestechs option.
     *
     * @param string $option The option to update.
     * @param mixed $value The new value for the option.
     */
    function logestechs_image( $asset ) {
        return logestechs_asset( 'img/' . $asset );
    }
}

// Define other helper functions as needed
