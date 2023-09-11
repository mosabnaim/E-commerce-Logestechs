<?php
/**
 * The file that manages Logestechs credentials.
 *
 * This file is used to manage Logestechs credentials such as saving, updating, and deleting credentials.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/include/credentials
 */

if ( ! class_exists( 'Logestechs_Credentials_Manager' ) ) {

    /**
     * Class to manage Logestechs credentials.
     *
     * This class defines all code necessary to manage Logestechs credentials.
     *
     * @since      1.0.0
     * @package    Logestechs
     * @subpackage Logestechs/include/credentials
     */
    class Logestechs_Credentials_Manager {
        /**
         * Holds the database instance.
         *
         * @since    1.0.0
         * @access   private
         * @var      object   $db    Database instance.
         */
        private $db;

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {
            $this->db = Logestechs_Credentials_Storage::get_instance();
        }

        /**
         * Save Logestechs credentials.
         *
         * @since    1.0.0
         * @param    array   $credentials   The credentials to be saved.
         * @return   array|WP_Error   The saved data or error on failure.
         */
        public function save_credentials( $credentials ) {
            if( Logestechs_Config::COMPANY_DOMAIN) {
                $domain = Logestechs_Config::COMPANY_DOMAIN;
            }else {
                // Sanitize credentials
                $domain   = sanitize_text_field( $credentials['domain'] );
                $domain = str_replace(['https://', 'http://', '/'], '', $domain);
            }
            $email    = sanitize_text_field($credentials['email']);
            $password = sanitize_text_field( $credentials['password'] );
            $id       = isset( $credentials['company_id'] ) ? intval( $credentials['company_id'] ) : null;

            $api_handler = new Logestechs_Api_Handler();

            // Use the API Handler to log in and get the company_id and logo_url
            $api_company_data = $api_handler->get_company_by_domain( $domain );
            if ( ! $api_company_data ) {
                return new WP_Error( 'invalid_credentials', __( 'The credentials you have provided are not correct.', 'logestechs' ) );
            }

            $credentials_response = $api_handler->check_credentials( $api_company_data['company_id'], $email, $password );
            
            if ( isset($credentials_response['error']) ) {
                return new WP_Error( 'credentials_mismatch', $credentials_response['error'] );
            }

            $security_manager = new Logestechs_Security_Manager();
            $encryptor        = $security_manager->get_encryptor();
            // Ensure that the password can be encrypted before proceeding
            if ( ! $encryptor ) {
                return new WP_Error( 'encryption_failure', __( 'Failed to initialize encryption.', 'logestechs' ) );
            }
            $encrypted_password = $encryptor->encrypt( $password );

            // Prepare the data for saving
            $data = [
                'domain'       => $domain,
                'email'        => $email,
                'password'     => $encrypted_password,
                'company_id'   => $api_company_data['company_id'],
                'company_name' => $api_company_data['name'],
                'logo_url'     => $api_company_data['logo_url'],
                'currency'     => $api_company_data['currency'],
                'created_at'   => current_time( 'mysql' )
            ];
            $format = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'];

            if ( Logestechs_Config::COMPANY_DOMAIN ) {
                $first_record = $this->db->get_first_record();
                if ($first_record) {
                    $db_response = $this->db->update_credentials($first_record->id, $data, $format);
                } else {
                    $db_response = $this->db->insert_credentials($data, $format);
                }
            } else {
                // Check for the existence of credentials based on domain and email
                if ( is_null( $id ) ) {
                    $exist = $this->db->email_exists_for_domain( $domain, $email );
                    if ( $exist ) {
                        return new WP_Error( 'company_exists', __( 'This company already exists!', 'logestechs' ) );
                    }
                    $db_response = $this->db->insert_credentials( $data, $format );
                    $id          = $db_response;
                } else {
                    $acceptable_cancel_status = Logestechs_Config::ACCEPTABLE_CANCEL_STATUS;
                    // Check if there are any WooCommerce orders associated with this company before updating
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
                                'value'   => $acceptable_cancel_status,
                                'compare' => 'IN'
                            ]
                        ],
                        'post_status'    => 'any',
                        'posts_per_page' => 1 // Checking if at least one order exists
                    ];
                    $query = new WP_Query( $args );
                    if ( $query->have_posts() ) {
                        wp_send_json_error( 'Cannot delete this company as it is associated with existing orders!' );
                        wp_die();
                    }
    
                    $db_response = $this->db->update_credentials( $id, $data, $format );
                }
            }

            if ( $db_response ) {
                unset( $data['password'], $data['created_at'] );
                $data = array_merge( ['id' => $id], $data );

                return $data;
            }

            return new WP_Error( 'db_error', __( 'There was an error saving the credentials to the database.', 'logestechs' ) );
        }
        
        /**
         * Delete Logestechs credentials.
         *
         * @since    1.0.0
         * @param    int   $id   The ID of the credentials to be deleted.
         * @return   bool         True if successful, false otherwise.
         */
        public function delete_credentials( $id ) {
            return $this->db->delete_credentials( $id );
        }

        /**
         * Fetch all companies.
         *
         * @since    1.0.0
         * @return   array|WP_Error   List of companies or error on failure.
         */
        public function fetch_companies() {
            return $this->db->fetch_companies();
        }

        /**
         * Get Logestechs company ID by local ID.
         *
         * @since    1.0.0
         * @param    int   $local_company_id   The local company ID.
         * @return   int|WP_Error              Logestechs company ID or error on failure.
         */
        public function get_logestechs_company_id_by_local_id( $local_company_id ) {
            return $this->db->get_logestechs_company_id_by_local_id( $local_company_id );
        }
    }
}