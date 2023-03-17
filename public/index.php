<?php

/**
 * index.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

include '../src/bootstrap.php';                                     // Setup file

/* Split path in parts for validation. 
 * Query string was removed. This one can be retrieved in the 
 * corresponding PHP page */
$path  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);          // Get path without query string
$path  = mb_strtolower($path);                                      // Get path in lowercase
$path  = substr($path, strlen(DOC_ROOT));                           // Remove up to DOC_ROOT
$parts = explode('/', $path);                                       // Split into array at /

if($parts[0] != 'admin'){                                           // If not an admin page
    $page = $parts[0] ?: 'index';                                   // Page name (or use index)
    //$id   = $parts[1] ?? null;                                      // Get ID (or use null)
}
else{                                                               // If an admin page
    $page = 'admin/' . ($parts[1] ?? '');                           // Page name
    //$id   = $parts[2] ?? null;                                      // Get ID
}

// TO REVIEW IF THIS IS REALLY USEFUL
//$id = filter_var($id, FILTER_VALIDATE_INT);                         // Validate ID

// Path to PHP page.
$php_page = APP_ROOT_PATH . '/src/pages/' . $page . '.php'; 

// Check if PHP page exists
if(!file_exists($php_page)){                                        // If page does not exist, display custom page
    $php_page = APP_ROOT_PATH . '/src/pages/page-not-found.php';    // Include page not found
}

// If everything worked ok, display intended page
include $php_page;

?>
