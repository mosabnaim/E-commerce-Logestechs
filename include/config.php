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
    // const COMPANY_DOMAIN   = null;
    // const COMPANY_ID   = null;

    const COMPANY_DOMAIN   = 'ksademo.logestechs.com';
    const COMPANY_ID   = 214;

    
    const PLUGIN_STYLES = [
        '--logestechs-primary-color'  => '#F97F35',
        '--logestechs-gradient'       => 'linear-gradient(270deg, #FBA229 0%, #F87E34 100%);',
        '--logestechs-btn-gradient'   => 'linear-gradient(128deg, #F87E34 0%, #F24844 100%);',
        '--logestechs-font'           => 'Almarai, roboto, sans-serif',
        '--logestechs-secondary-font' => 'roboto, sans-serif'
    ];

    const STATUS_ARRAY = [
        'DRAFT'                                                => 'Draft',
        'PENDING_CUSTOMER_CARE_APPROVAL'                       => 'Pending Acceptance',
        'APPROVED_BY_CUSTOMER_CARE_AND_WAITING_FOR_DISPATCHER' => 'Ready for dispatching',
        'CANCELLED'                                            => 'Cancelled',
        'ASSIGNED_TO_DRIVER_AND_PENDING_APPROVAL'              => 'Assigned to Drivers',
        'REJECTED_BY_DRIVER_AND_PENDING_MANGEMENT'             => 'Rejected By Drivers',
        'ACCEPTED_BY_DRIVER_AND_PENDING_PICKUP'                => 'Pending Pickup',
        'SCANNED_BY_DRIVER_AND_IN_CAR'                         => 'Picked',
        'SCANNED_BY_HANDLER_AND_UNLOADED'                      => 'Pending Sorting',
        'MOVED_TO_SHELF_AND_OUT_OF_HANDLER_CUSTODY'            => 'Sorted on Shelves',
        'OPENED_ISSUE_AND_WAITING_FOR_MANAGEMENT'              => 'Reported to Management',
        'DELIVERED_TO_RECIPIENT'                               => 'Delivered',
        'POSTPONED_DELIVERY'                                   => 'Postponed delivery',
        'RETURNED_BY_RECIPIENT'                                => 'Returned by recipient',
        'COMPLETED'                                            => 'Completed',
        'FAILED'                                               => 'Failed',
        'RESOLVED_FAILURE'                                     => 'Resolved Failure',
        'UNRESOLVED_FAILURE'                                   => 'Unresolved Failure',
        'TRANSFERRED_OUT'                                      => 'Transferred out',
        'PARTIALLY_DELIVERED'                                  => 'Partially delivered',
        'SWAPPED'                                              => 'Swapped',
        'BROUGHT'                                              => 'Brought',
        'DELIVERED_TO_SENDER'                                  => 'Delivered to sender'
    ];

    // Can delete/edit the company if completed
    const COMPLETED_STATUS = [
        'CANCELLED',
        'RETURNED_BY_RECIPIENT',
        'COMPLETED',
        'FAILED',
        'RESOLVED_FAILURE',
        'UNRESOLVED_FAILURE',
        'TRANSFERRED_OUT',
        'PARTIALLY_DELIVERED',
        'SWAPPED',
        'BROUGHT',
        'DELIVERED_TO_SENDER'
    ];

    // Can Assign the company if acceptable
    const ACCEPTABLE_TRANSFER_STATUS = [
        'CANCELLED',
        'RETURNED_BY_RECIPIENT',
        'COMPLETED',
        'FAILED',
        'RESOLVED_FAILURE',
        'UNRESOLVED_FAILURE',
        'TRANSFERRED_OUT',
        'PARTIALLY_DELIVERED',
        'SWAPPED',
        'BROUGHT',
        'DELIVERED_TO_SENDER'
    ];

    // Can Assign the company if acceptable
    const ACCEPTABLE_PICKUP_STATUS = [
        'CANCELLED',
        'RETURNED_BY_RECIPIENT',
        'COMPLETED',
        'FAILED',
        'RESOLVED_FAILURE',
        'UNRESOLVED_FAILURE',
        'TRANSFERRED_OUT',
        'PARTIALLY_DELIVERED',
        'SWAPPED',
        'BROUGHT',
        'DELIVERED_TO_SENDER'
    ];

    /**
     * Prevent instantiation.
     * This class is only for defining constants, it should not be instantiated.
     */
    private function __construct() {}
}
