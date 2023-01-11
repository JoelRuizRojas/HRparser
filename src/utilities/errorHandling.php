<?php

/**
 * errorHandling.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */


/**
 * Convert errors to exceptions
 *
 * @param $error_type, error type                  (IN)
 * @param $error_message, error message            (IN)
 * @param $error_file, file where error ocurred    (IN)
 * @param $error_line, line where error is located (IN)
 * @return none
 */
function handle_error($error_type, $error_message, $error_file, $error_line)
{
    throw new ErrorException($error_message, 0, $error_type, $error_file, $error_line); // Turn into ErrorException
}


/**
 * Handle exceptions - log exception and show error message 
 * (if server does not send error page listed in .htaccess)
 *
 * @param $e Error instance    (IN)
 * @return none
 */
function handle_exception($e)
{
    error_log($e);                        // Log the error
    http_response_code(500);              // Set the http response code
    echo "<h1>Sorry, a problem occurred</h1>
          The site's owners have been informed. Please try again later.";
}


/**
 * Handle fatal errors
 *
 * @param none
 * @return none
 */
function handle_shutdown()
{
    $error = error_get_last();            // Check for error in script
    if ($error !== null) {                // If there was an error next line throws exception
        $e = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
        handle_exception($e);             // Call exception handler
    }
}

?>
