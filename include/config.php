<?php
/**
 * Logestechs_Config - Configuration file for the Logestechs Plugin.
 *
 * This file is used to hold the configurable elements of the Logestechs plugin,
 * such as colors, logo path, icon path, and font. It's meant to act as a single
 * point of change for these elements.
 *
 * @package Logestechs
 */

class Logestechs_Config {
    const PLUGIN_NAME = 'Logestechs';
    const MENU_TITLE = 'Logestechs';
    const MENU_SLUG = 'logestechs';
    const PLUGIN_LOGO = LOGESTECHS_PLUGIN_URL . 'assets/img/logo.jpeg';
    const PLUGIN_ICON = LOGESTECHS_PLUGIN_URL . 'assets/img/logo.svg';

    const PLUGIN_STYLES = [
        '--logestechs-primary-color'     => '#F97F35',
        '--logestechs-gradient'          => 'linear-gradient(270deg, #FBA229 0%, #F87E34 100%);',
        '--logestechs-btn-gradient'      => 'linear-gradient(128deg, #F87E34 0%, #F24844 100%);',
        '--logestechs-font'              => 'Almarai, roboto, sans-serif',
        '--logestechs-secondary-font'    => 'roboto, sans-serif'
    ];

    /**
     * Prevent instantiation.
     * This class is only for defining constants, it should not be instantiated.
     */
    private function __construct() {}
}