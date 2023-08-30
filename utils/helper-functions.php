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
if ( ! function_exists( 'logestechs_wordify' ) ) {

    /**
     * Update Logestechs option.
     *
     * @param string $option The option to update.
     * @param mixed $value The new value for the option.
     */
    function logestechs_wordify( $string ) {
        $words = str_replace( '_', ' ', $string ); // Replace underscores with spaces
        $words = ucwords( $words );                // Capitalize the first letter of each word

        return $words;
    }
}
if ( ! function_exists( 'logestechs_get_current_language' ) ) {

    /**
     * Update Logestechs option.
     *
     * @param string $option The option to update.
     * @param mixed $value The new value for the option.
     */
    function logestechs_get_current_language() {
        return (get_locale() == 'ar') ? 'ar' : 'en';
    }
}
if ( ! function_exists( 'logestechs_convert_to_local_time' ) ) {

    /**
     * Update Logestechs option.
     *
     * @param string $option The option to update.
     * @param mixed $value The new value for the option.
     */
    function logestechs_convert_to_local_time($date_input) {
        // Get WordPress timezone
        $timezone = get_option('timezone_string');
        if (!$timezone) {
            $timezone_offset = get_option('gmt_offset');
            $timezone        = timezone_name_from_abbr('', $timezone_offset * 60 * 60, false);
        }
    
        // Create a new DateTime object
        $date_time = new DateTime();
    
        // Check if the input is a Unix timestamp
        if (is_numeric($date_input)) {
            // If it's a timestamp, set it directly
            $date_time->setTimestamp($date_input);
        } else {
            // If it's a date string, parse it normally
            $date_time = new DateTime($date_input);
        }
    
        // Set the time zone for the DateTime object
        if ($timezone) {
            $date_time->setTimezone(new DateTimeZone($timezone));
        }
    
        return $date_time;
    }
    
}
