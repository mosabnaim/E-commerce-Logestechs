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
    const MENU_TITLE  = 'Logestechs';
    const MENU_SLUG   = 'logestechs';
    const PLUGIN_LOGO = LOGESTECHS_PLUGIN_URL . 'assets/img/logo.jpeg';
    const PLUGIN_ICON = LOGESTECHS_PLUGIN_URL . 'assets/img/logo.svg';
    const COMPANY_DOMAIN   = null;
    const COMPANY_ID   = null;

    // const COMPANY_DOMAIN   = 'ksademo.logestechs.com';
    // const COMPANY_ID   = 214;

    
    const PLUGIN_STYLES = [
        '--logestechs-primary-color'  => '#F97F35',
        '--logestechs-gradient'       => 'linear-gradient(270deg, #FBA229 0%, #F87E34 100%);',
        '--logestechs-btn-gradient'   => 'linear-gradient(128deg, #F87E34 0%, #F24844 100%);',
        '--logestechs-font'           => 'Almarai, roboto, sans-serif',
        '--logestechs-secondary-font' => 'roboto, sans-serif'
    ];

    public static function get_status_array() {
        return [
            'DRAFT'                                               => __('Draft', 'logestechs'),
            'PENDING_CUSTOMER_CARE_APPROVAL'                      => __('Pending', 'logestechs'),
            'APPROVED_BY_CUSTOMER_CARE_AND_WAITING_FOR_DISPATCHER'=> __('Being Processed', 'logestechs'),
            'CANCELLED'                                           => __('Cancelled', 'logestechs'),
            'ASSIGNED_TO_DRIVER_AND_PENDING_APPROVAL'             => __('Being Processed', 'logestechs'),
            'REJECTED_BY_DRIVER_AND_PENDING_MANGEMENT'            => __('Being Processed', 'logestechs'),
            'ACCEPTED_BY_DRIVER_AND_PENDING_PICKUP'               => __('Being Processed', 'logestechs'),
            'SCANNED_BY_DRIVER_AND_IN_CAR'                        => __('In Progress', 'logestechs'),
            'SCANNED_BY_HANDLER_AND_UNLOADED'                     => __('In Progress', 'logestechs'),
            'MOVED_TO_SHELF_AND_OUT_OF_HANDLER_CUSTODY'           => __('In Progress', 'logestechs'),
            'OPENED_ISSUE_AND_WAITING_FOR_MANAGEMENT'             => __('In Progress', 'logestechs'),
            'DELIVERED_TO_RECIPIENT'                              => __('Delivered', 'logestechs'),
            'POSTPONED_DELIVERY'                                  => __('Postponed', 'logestechs'),
            'RETURNED_BY_RECIPIENT'                               => __('Returned', 'logestechs'),
            'COMPLETED'                                           => __('Completed', 'logestechs'),
            'FAILED'                                              => __('Failed', 'logestechs'),
            'RESOLVED_FAILURE'                                    => __('Failed', 'logestechs'),
            'UNRESOLVED_FAILURE'                                  => __('Failed', 'logestechs'),
            'TRANSFERRED_OUT'                                     => __('In Progress', 'logestechs'),
            'PARTIALLY_DELIVERED'                                 => __('Partially Delivered', 'logestechs'),
            'SWAPPED'                                             => __('Swapped', 'logestechs'),
            'BROUGHT'                                             => __('Brought', 'logestechs'),
            'DELIVERED_TO_SENDER'                                 => __('Returned and received', 'logestechs')
        ];
    }

    // Can delete/edit the company if completed
    const ACCEPTABLE_CANCEL_STATUS = [
        'DRAFT',
        'PENDING_CUSTOMER_CARE_APPROVAL',
        'APPROVED_BY_CUSTOMER_CARE_AND_WAITING_FOR_DISPATCHER',
        'ASSIGNED_TO_DRIVER_AND_PENDING_APPROVAL'
    ];

    // Can Assign the company if acceptable
    const ACCEPTABLE_TRANSFER_STATUS = [
        'CANCELLED',
        null // null for new orders
    ];

    // Can Assign the company if acceptable
    const ACCEPTABLE_PICKUP_STATUS = [
        'COMPLETED',
        'DELIVERED_TO_RECIPIENT',
        null // null for new orders
    ];

    /**
     * Prevent instantiation.
     * This class is only for defining constants, it should not be instantiated.
     */
    private function __construct() {}
}
