<?php
/**
 * The file that handles storage of Logestechs credentials
 *
 * This file is used to handle storage of Logestechs credentials such as saving, updating, and deleting credentials in the database.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/include/credentials
 */
require_once ABSPATH . 'wp-admin/includes/upgrade.php';

if ( ! class_exists( 'Logestechs_Credentials_Storage' ) ) {
    class Logestechs_Credentials_Storage {
        private static $instance;
        private $table_name;
        private $wpdb;

        private function __construct() {
            global $wpdb;
            $this->wpdb       = $wpdb;
            $this->table_name = $wpdb->prefix . 'logestechs_companies';
        }

        public static function get_instance() {
            if ( null === self::$instance ) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        public function create_table() {
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
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY company_email (company_id, email)
            ) {$charset_collate};";

            dbDelta( $sql );
        }

        public function fetch_companies() {
            return $this->wpdb->get_results( "SELECT id, company_id, domain, email, logo_url, feedback FROM {$this->table_name} ORDER BY created_at DESC", ARRAY_A );
        }

        public function email_exists_for_domain( $domain, $email ) {
            $result = $this->wpdb->get_results(
                $this->wpdb->prepare(
                    "SELECT * FROM {$this->table_name} WHERE domain = %s AND email = %s",
                    $domain,
                    $email
                )
            );

            // If the result is not empty, that means the email already exists for the domain
            if ( ! empty( $result ) ) {
                return true;
            } else {
                return false;
            }
        }

        public function drop_table() {
            $this->wpdb->query( "DROP TABLE IF EXISTS {$this->table_name}" );
        }

        public function insert_credentials( $data, $format ) {
            $this->wpdb->insert( $this->table_name, $data, $format );

            return $this->wpdb->insert_id;
        }

        public function get_company( $id ) {
            return (object) $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE id = %s", $id ), ARRAY_A );
        }
        

        public function update_credentials( $id, $data, $format ) {
            global $wpdb;

            // Where clause array
            $where        = ['id' => $id];
            $where_format = ['%d']; // Assuming that id is an integer

            return $wpdb->update( $this->table_name, $data, $where, $format, $where_format );
        }

        public function delete_credentials( $id ) {
            return $this->wpdb->delete( $this->table_name, [ 'id' => $id ], [ '%d' ] );
        }

        public function get_logestechs_company_id_by_local_id( $local_company_id ) {
            global $wpdb;
        
            // Prepare the SQL statement and query the database
            $query = $wpdb->prepare( "SELECT company_id FROM {$this->table_name} WHERE id = %d", $local_company_id );
            return $wpdb->get_var( $query );
        }
    }
}