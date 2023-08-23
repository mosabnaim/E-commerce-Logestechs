<?php
/**
 * The file that manages Logestechs credentials
 *
 * This file is used to manage Logestechs credentials such as saving, updating, and deleting credentials.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/include/credentials
 */

if ( ! class_exists( 'Logestechs_Credentials_Manager' ) ) {
    class Logestechs_Credentials_Manager {
        private $db;

        public function __construct() {
            $this->db = Logestechs_Credentials_Storage::get_instance();
        }

        public function save_credentials( $credentials ) {
            $domain      = $credentials['domain'];
            $email       = $credentials['email'];
            $password    = $credentials['password'];
            $id          = $credentials['company_id'] ?? null;

            $api_handler = new Logestechs_Api_Handler();

            // Use the API Handler to log in and get the company_id and logo_url
            $api_company_data = $api_handler->get_company_by_domain( $domain );

            $credentials_response = $api_handler->check_credentials( $api_company_data['company_id'], $email, $password );
            
            if ( !empty($credentials_response) ) {
                return ['error' => __('Provided credentials are not matching any record on the server!')];
            }

            $security_manager   = new Logestechs_Security_Manager();
            $encryptor          = $security_manager->get_encryptor();
            $encrypted_password = $encryptor->encrypt( $password ); // Encrypt the password

            if ( $api_company_data === false ) {
                // Handle the error
                return ['error' => __('The credentials you have provided are not correct.')];
            }

            $data = [
                'domain'       => $domain,
                'email'        => $email,
                'password'     => $encrypted_password,
                'company_id'   => $api_company_data['company_id'],
                'company_name' => $api_company_data['name'],
                'logo_url'     => $api_company_data['logo_url'],
                'created_at'   => current_time( 'mysql' )
            ];
            $format = ['%s', '%s', '%s', '%s', '%s', '%s', '%s'];

            if ( is_null( $id ) ) {
                $exist = $this->db->email_exists_for_domain( $domain, $email );
                if ( $exist ) {
                    return ['error' => __('This company already exists!')];
                }
                $db_response = $this->db->insert_credentials( $data, $format );
                $id          = $db_response;
            } else {
                // Check if there are any WooCommerce orders that are using the logestechs_order_id with the given company_id
                $args = [
                    'post_type'      => 'shop_order',
                    'meta_query'     => [
                        [
                            'key'     => '_logestechs_local_company_id',
                            'value'   => $id,
                            'compare' => '='
                        ],
                        [
                            'key'     => '_logestechs_order_status',
                            'value'   => 'Cancelled',
                            'compare' => '!='
                        ]
                    ],
                    'post_status'    => 'any',
                    'posts_per_page' => 1 // we only need to know if at least one exists
                ];
                $query = new WP_Query( $args );
                if ( $query->have_posts() ) {
                    wp_send_json_error( 'Cannot delete this company as it is associated with existing orders!' );
                    wp_die();
                }

                $db_response = $this->db->update_credentials( $id, $data, $format );
            }

            if ( $db_response ) {
                unset( $data['password'] );
                unset( $data['created_at'] );
                $data = array_merge( ['id' => $id], $data );

                return $data;
            }
        }

        public function delete_credentials( $id ) {
            return $this->db->delete_credentials( $id );
        }

        public function fetch_companies() {
            return $this->db->fetch_companies();
        }

        public function get_logestechs_company_id_by_local_id( $local_company_id ) {
            return $this->db->get_logestechs_company_id_by_local_id($local_company_id);
        }
    }
}