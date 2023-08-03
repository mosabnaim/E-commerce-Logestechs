<?php

/**
 * File: utils/debugger.php
 *
 * The class that handles debugging utilities.
 */

class Logestechs_Debugger {
    static $has_been_run = false; 

    public $file_path;
    public $log_data = [];

    private $start_time;
    private $end_time;
    private $last_split_time;
    private $split_times = [];

    public function __construct() {
        $date_today      = date( 'Y-m-d' );
        $this->file_path = LOGESTECHS_PLUGIN_PATH . 'logs/log-' . $date_today . '.json';
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
            $this->output_modal_code();
        }
    }
    
    // Adds a log message
    public function log( $msg, $log_type = 'Log' ) {
        $timestamp = date( 'd-m-Y h:i:sa' );
    
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $debug     = $backtrace[0];
        $file_data = basename( $debug['file'] ) . ' ( line: ' . $debug['line'] . ' )';
    
        if (isset($debug['file']) && isset($debug['line'])) {
            $filepath = str_replace("\\", "/", $debug['file']);
            $line_number = $debug['line'];
    
            $vscode_link = "vscode://file/{$filepath}:{$line_number}";
            $file_data = "<a href=\"$vscode_link\">$file_data</a>";
        }
    
        if ( is_array( $msg ) ) {
            $msg = array_change_key_case( $msg, CASE_LOWER );
            $msg = [
                'Timestamp' => $timestamp,
                'Type'      => $log_type,
                'File'      => $file_data
            ] + $msg;
        } else {
            $msg = [
                'Timestamp' => $timestamp,
                'Message'   => $msg,
                'Type'      => $log_type,
                'File'      => $file_data
            ];
        }
    
        $this->log_data[] = $msg;
    
        return $this;
    }
    

    // Writes logs to the file
    public function write() {
        $file_content = [];

        if ( file_exists( $this->file_path ) ) {
            $existing_content = file_get_contents( $this->file_path );
            $file_content     = json_decode( $existing_content, true );
        }

        $file_content = array_merge( $file_content, $this->log_data );
        file_put_contents( $this->file_path, json_encode( $file_content, JSON_PRETTY_PRINT ), LOCK_EX );

        return $this;
    }

    public function display( $from_file = false ) {
        // Choose the data source based on the method argument
        $errors      = $from_file ? $this->get_logs() : $this->log_data;
        $errors_json = json_encode( $errors, JSON_PRETTY_PRINT );

        ob_start();
        ?>
        <script>
            let errorsData = <?php echo $errors_json; ?>;
            let formattedErrors = "";

            function iterateAndFormat(data, indent = '') {
                for (let key in data) {
                    if (typeof data[key] === 'object' && data[key] !== null) {
                        formattedErrors += `${indent}<span style="color: #5ccfe6;">${key}</span>:<br/>`;
                        iterateAndFormat(data[key], indent + '   ');
                    } else {
                        formattedErrors += `${indent}<span style="color: #5ccfe6;">${key}</span>: <span style="color: #caff75;">${data[key]}</span><br/>`;
                    }
                }
                formattedErrors += '<br/>'; // add line break after each error object
            }

            iterateAndFormat(errorsData);
            let oldContent = document.getElementById("logestechs-debugger-modal-content").innerHTML;
            document.getElementById("logestechs-debugger-modal-content").innerHTML = oldContent + formattedErrors;
            document.getElementById("logestechs-debugger-modal").style.display = "block";
        </script>

        <?php
        echo ob_get_clean();
    }

    public function output_modal_code() {
        // If the function has already been run once, just return
        if (self::$has_been_run) {
            return;
        }
        
        ob_start();
        ?>
        <div id="logestechs-debugger-modal" style="display: none; position: fixed; z-index: 200000; padding-top: 2vh; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.8);">
            <style>
                #logestechs-debugger-modal-content a {
                    color: #c2e4ff;
                }
                #logestechs-debugger-modal-content a:hover {
                    color: #8fc0e4;
                }
            </style>
            <div style="background-color: #1f2430; margin: auto; padding: 20px; border-radius: 10px; width: 80%; max-height: 96vh; overflow: auto; color: #fff; font-size: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #4a4a4a;">
                    <h2 style="margin: 0; color: #fff; text-align: center; width: 100%;">Logestechs Debugger</h2>
                    <span id="logestechs-debugger-modal-close" style="color: #aaaaaa; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
                </div>
                <pre id="logestechs-debugger-modal-content" style="max-height: calc(85vh - 50px); overflow: auto; padding-inline: 20px; background-color: #1f2430; box-shadow: none;"></pre>
            </div>
        </div>

        <script>
            document.getElementById("logestechs-debugger-modal-close").onclick = function() {
                document.getElementById("logestechs-debugger-modal").style.display = "none";
            };
        </script>
        <?php
        echo ob_get_clean();

        // The function has now been run, so set our static variable to true
        self::$has_been_run = true;
    }

    // Clears the logs
    public function clear() {
        // Check if the file is writable or can be created
        if ( is_writable( $this->file_path ) || touch( $this->file_path ) ) {
            // Empty the log_data array
            $this->log_data = [];

            file_put_contents( $this->file_path, json_encode( [], JSON_PRETTY_PRINT ), LOCK_EX );
        } else {
            throw new Exception( "Unable to clear log file: {$this->file_path} is not writable." );
        }

        return $this;
    }

    // Logs the WordPress database queries
    public function log_db_queries( $msg = 'Database queries total execution time' ) {
        if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) {
                global $wpdb;
                $total_execution_time = 0;
    
                foreach ( $wpdb->queries as $query_info ) {
                    $total_execution_time += $query_info[1];
    
                    $this->log( [
                        'SQL Query'       => $query_info[0],
                        'Execution Time'  => $this->format_execution_time($query_info[1]),
                        'Caller Function' => $this->parse_call_stack( $query_info[2] )
                    ], 'Database' );
                }
    
                // Log total execution time
                $this->log([
                    'Message' => $msg,
                    'Execution Time' => $this->format_execution_time($total_execution_time)
                ], 'Performance');
        } else {
            $this->log( 'SAVEQUERIES is not defined or set to true. No queries logged.', 'Database' );
        }
    
        return $this;
    }
    

    private function parse_call_stack( $call_stack_str ) {
        // Split the string into an array by the ', ' delimiter
        $call_stack_arr = explode( ', ', $call_stack_str );

        // Create an array to hold the parsed call stack
        $parsed_call_stack = [];

        // Loop through each entry in the array
        foreach ( $call_stack_arr as $call ) {
            // Split each entry by the '(' delimiter to separate the function name from the arguments
            $call_parts = explode( '(', $call, 2 );

            // Get the function name
            $function_name = trim( $call_parts[0] );

            // Check if there are any arguments, if yes, remove the closing bracket
            $arguments = isset( $call_parts[1] ) ? rtrim( $call_parts[1], ')' ) : '';

            // Format the function call as a readable string
            $formatted_call = $arguments ? "$function_name ($arguments)" : $function_name;

            // Add the formatted function call to the parsed call stack array
            $parsed_call_stack[] = $formatted_call;
        }

        return $parsed_call_stack;
    }

    // Logs HTTP requests and responses
    public function log_http( $request, $response ) {
        $this->log( [
            'HTTP Request'  => $request,
            'HTTP Response' => $response
        ], 'HTTP' );

        return $this;
    }

    // Logs key environment details
    public function log_env_info() {
        $this->log( [
            'PHP Version'       => phpversion(),
            'WordPress Version' => get_bloginfo( 'version' ),
            'Active Theme'      => wp_get_theme()->get( 'Name' ),
            'Active Plugins'    => get_option( 'active_plugins' )
        ], 'Environment' );

        return $this;
    }

    // Time the execution of a function and log it
    public function time_function_execution( callable $function, string $msg = '' ) {
        try {
            $start_time = microtime( true );
            $function(); // Run the function
            $end_time = microtime( true );

            $time_taken = $end_time - $start_time;
            $this->log( [
                'Execution time' => $this->format_execution_time($time_taken),
                'Message'        => $msg
            ], 'Performance' );
        } catch ( Exception $e ) {
            $this->log( $e, 'Error' );
        }

        return $this;
    }

    // Logs the current memory usage
    public function log_memory_usage( $msg = 'Current memory usage' ) {
        $memory_usage = memory_get_usage();
        $this->log( [
            'Memory usage' => $this->format_memory_usage( $memory_usage ),
            'Message'      => $msg
        ], 'Performance' );

        return $this;
    }

    public function start_timer() {
        $this->start_time = $this->last_split_time = microtime( true );
    
        return $this;
    }
    
    public function split_timer( string $msg = '' ) {
        $time_since_last_split = microtime( true ) - $this->last_split_time;
        $this->split_times[] = [
            'Execution time' => $this->format_execution_time( $time_since_last_split ),
            'Message'        => $msg
        ];
        $this->last_split_time = microtime( true );
    
        return $this;
    }
    
    public function stop_timer( string $msg = '' ) {
        $this->end_time = microtime( true );
        $time_taken     = $this->end_time - $this->start_time;
    
        if (count($this->split_times) > 1) {
            $this->log( [
                'Execution time' => $this->format_execution_time( $time_taken ),
                'Message'        => $msg,
                'Split Times'    => $this->split_times
            ], 'Performance' );
        } else {
            $this->log( [
                'Execution time' => $this->format_execution_time( $time_taken ),
                'Message'        => $msg
            ], 'Performance' );
        }
    
        $this->split_times = []; // Reset split times
        return $this;
    }
    

    public static function get_logs( $date = '' ) {
        // Retrieves the log file path.
        $file_path = LOGESTECHS_PLUGIN_PATH . 'logs/log-' . ( $date ? $date : date( 'Y-m-d' ) ) . '.json';

        // Fetches the log file contents.
        $file_content = file_get_contents( $file_path );

        return $file_content ? json_decode( $file_content, true ) : [];
    }

    private function format_execution_time( $time_in_seconds ) {
        if ( $time_in_seconds < 0.001 ) {  // less than 1 ms
            return round( $time_in_seconds * 1000000 ) . ' µs';  // convert to microseconds
        } elseif ( $time_in_seconds < 1 ) {  // less than 1 second
            return round( $time_in_seconds * 1000 ) . ' ms';  // convert to milliseconds
        } else {
            return round( $time_in_seconds, 2 ) . ' s';  // keep as seconds
        }
    }
    private function format_memory_usage( $memory_in_bytes ) {
        if ( $memory_in_bytes < 1024 ) {  // less than 1 KB
            return $memory_in_bytes . ' B';  // keep as bytes
        } elseif ( $memory_in_bytes < 1048576 ) {  // less than 1 MB
            return round( $memory_in_bytes / 1024, 2 ) . ' KB';  // convert to kilobytes
        } elseif ( $memory_in_bytes < 1073741824 ) {  // less than 1 GB
            return round( $memory_in_bytes / 1048576, 2 ) . ' MB';  // convert to megabytes
        } else {
            return round( $memory_in_bytes / 1073741824, 2 ) . ' GB';  // convert to gigabytes
        }
    }
}
