<?php

/**
 * bootstrap.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

define('APP_ROOT_PATH', dirname(__FILE__, 2));          // Application root full path

require APP_ROOT_PATH . '/config/config.php';           // Configuration data
require APP_ROOT_PATH . '/vendor/autoload.php';         // Autoload libraries
require APP_ROOT_PATH . '/src/utilities/functions.php'; // General functions

if(DEV == false){                                       // If not in development
    set_exception_handler('handle_exception');          // Set exception handler
    set_error_handler('handler_error');                 // Set error handler
    register_shutdown_function('handle_shutdown');      // Set shutdown handler
}

// Create CMS object
$cms = new \HRparser\CMS\CMS($dsn, $username, $password);

// After this point we do not need the database config data
unset($dsn, $username, $password);

// Twig extension configuration
$twig_options['cache'] = APP_ROOT_PATH . '/var/cache';  // Path to Twig cache folder. Cache php template pages
$twig_options['debug'] = DEV;                           // If dev mode, turn debug on

$loader = new Twig\Loader\FilesystemLoader(APP_ROOT_PATH . '/templates'); // Twig loader
$twig   = new Twig\Environment($loader, $twig_options); // Twig environment
$twig->addGlobal('doc_root', DOC_ROOT);                 // Document root

if(DEV == true){                                        // If in development
    $twig->addExtension(new \Twig\Extension\DebugExtension()); // Add Twig debug extension
}

?>
