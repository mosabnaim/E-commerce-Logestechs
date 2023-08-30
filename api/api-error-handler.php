<?php

/**
 * The file that interacts with Logestechs API Errors
 *
 * This class defines all code necessary to handle errors received from the Logestechs API.
 * It provides methods for handling API error responses, storing them in a session,
 * and displaying them to the user using JavaScript alerts.
 *
 * @since      1.0.0
 * @package    Logestechs
 * @subpackage Logestechs/api
 */

if ( ! class_exists( 'Logestechs_Api_Error_Handler' ) ) {

    class Logestechs_Api_Error_Handler {

        /**
         * Registers the method to check for and display errors.
         *
         * @since    1.0.0
         */
        public function init() {
            // Register the action hook to display error if found
            add_action( 'admin_notices', [$this, 'display_error_if_found'] );
        }

        /**
         * Handles errors returned by the Logestechs API.
         *
         * This function takes the error response from the Logestechs API, extracts the error message,
         * and stores it in the session to be displayed to the user.
         *
         * @param array $error_response The error response from the API.
         *
         * @since    1.0.0
         */
        public function handle_api_error( $error_response ) {
            $error_message = $error_response['error_message'] ?? __( 'Failed.', 'logestechs' );

            // Store the error message in a session so it can be displayed to the user
            $_SESSION['logestechs_error'] = $error_message;
        }

        /**
         * Checks for an error message in the session and displays it using Swal if found.
         *
         * @since    1.0.0
         */
        public function display_error_if_found() {
            if ( isset( $_SESSION['logestechs_error'] ) ) {
                // Print the JavaScript code to display the error
                ?>
                <script type="text/javascript">
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: __( 'Oops...', 'logestechs' ),
                            text: '<?php echo esc_js( $_SESSION['logestechs_error'] ); ?>',
                            icon: 'error',
                            confirmButtonColor: '#f77935',
                        });
                    });
                </script>
                <?php

                // Clear the error to prevent it from being displayed again
                unset( $_SESSION['logestechs_error'] );
            }
        }
    }
}
