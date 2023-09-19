<?php

/**
 * Handles storage of Logestechs credentials.
 *
 * Manages storage of Logestechs credentials like saving, updating, and deleting credentials in the database.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/include/credentials
 */

// Ensure direct access is prevented
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Logestechs_Credentials_Storage' ) ) {

    /**
     * Class Logestechs_Credentials_Storage
     *
     * @since 1.0.0
     */
    class Logestechs_Credentials_Storage {
        private static $instance;
        private $table_name;
        private $wpdb;

        /**
         * Logestechs_Credentials_Storage constructor.
         *
         * @since    1.0.0
         */
        private function __construct() {
            global $wpdb;
            $this->wpdb       = $wpdb;
            $this->table_name = $wpdb->prefix . 'logestechs_companies';
        }

        /**
         * Singleton instance.
         *
         * @since    1.0.0
         * @return   Logestechs_Credentials_Storage
         */
        public static function get_instance() {
            if ( null === self::$instance ) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        /**
         * Create database table for storing credentials.
         *
         * @since    1.0.0
         */
        public function create_table() {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            $charset_collate = $this->wpdb->get_charset_collate();

            $sql = "CREATE TABLE {$this->table_name} (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                domain varchar(55) NOT NULL,
                company_id varchar(55) NOT NULL,
                company_name varchar(255) NOT NULL,
                email varchar(55) NOT NULL,
                password varchar(255) NOT NULL,
                logo_url varchar(255) DEFAULT NULL,
                feedback varchar(255) DEFAULT NULL,
                currency varchar(10) DEFAULT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY company_email (company_id, email)
            ) {$charset_collate};";

            dbDelta( $sql );
        }

        /**
         * Retrieve all companies.
         *
         * @since    1.0.0
         * @return   array
         */
        public function fetch_companies() {
            return $this->wpdb->get_results( "SELECT id, company_id, domain, email, logo_url, feedback, currency FROM {$this->table_name} ORDER BY created_at DESC", ARRAY_A );
        }

        /**
         * Check if email exists for a given domain.
         *
         * @since    1.0.0
         * @param    string   $domain   The domain name.
         * @param    string   $email    The email address.
         * @return   bool
         */
        public function email_exists_for_domain( $domain, $email ) {
            $result = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE domain = %s AND email = %s", $domain, $email ) );

            return ! empty( $result );
        }

        /**
         * Drop the credentials table.
         *
         * @since    1.0.0
         */
        public function drop_table() {
            $this->wpdb->query( "DROP TABLE IF EXISTS {$this->table_name}" );
        }

        /**
         * Insert new company credentials.
         *
         * @since    1.0.0
         * @param    array   $data     The data to insert.
         * @param    array   $format   Data format.
         * @return   int
         */
        public function insert_credentials( $data, $format ) {
            $this->wpdb->insert( $this->table_name, $data, $format );

            return $this->wpdb->insert_id;
        }

        /**
         * Retrieve company data by ID.
         *
         * @since    1.0.0
         * @param    int     $id       Company ID.
         * @return   object
         */
        public function get_company( $id ) {
            return (object) $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE id = %d", $id ), ARRAY_A );
        }

        /**
         * Update stored company credentials.
         *
         * @since    1.0.0
         * @param    int     $id       The company ID.
         * @param    array   $data     The data to update.
         * @param    array   $format   Data format.
         * @return   false|int
         */
        public function update_credentials( $id, $data, $format ) {
            $where = ['id' => $id];

            return $this->wpdb->update( $this->table_name, $data, $where, $format, ['%d'] );
        }

        /**
         * Delete stored credentials for a company.
         *
         * @since    1.0.0
         * @param    int     $id       The company ID.
         * @return   false|int
         */
        public function delete_credentials( $id ) {
            return $this->wpdb->delete( $this->table_name, ['id' => $id], ['%d'] );
        }

        /**
         * Get Logestechs company ID using local ID.
         *
         * @since    1.0.0
         * @param    int     $local_company_id   The local company ID.
         * @return   string|NULL
         */
        public function get_logestechs_company_id_by_local_id( $local_company_id ) {
            $query = $this->wpdb->prepare( "SELECT company_id FROM {$this->table_name} WHERE id = %d", $local_company_id );

            return $this->wpdb->get_var( $query );
        }

        /**
         * Get the first Logestechs record.
         *
         * @since    1.0.0
         * @return   object|NULL  Returns the first record or NULL if no records exist.
         */
        public function get_first_record() {
            $domain = Logestechs_Config::COMPANY_DOMAIN;
            $query = $this->wpdb->prepare("SELECT * FROM {$this->table_name} WHERE domain = %s ORDER BY id ASC LIMIT 1", $domain);

            return $this->wpdb->get_row($query);
        }

    }
}