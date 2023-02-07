<?php

/**
 * config.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

define('DEV','true');                              // In development or live? Development = true | Live = false
define('DOMAIN', 'http://localhost:8092');         // Domain (used to create links in emails)
define('ROOT_FOLDER', 'public');                   // Name of document root folder (e.g. public, content, htdocs)

// DOC_ROOT is created because there are several page implementations below htdocs folder
// On a live site a single forward slash / would indicate the document root folder
$this_folder   = substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT']));
$parent_folder = dirname($this_folder);            // Get parent directory
define('DOC_ROOT', $parent_folder . DIRECTORY_SEPARATOR . ROOT_FOLDER . DIRECTORY_SEPARATOR);

// Database settings
$type     = 'mysql';                               // Type of database
$server   = '127.0.0.1';                           // Server the database is on
$db       = 'HRparserDB';                          // Name of the database
$port     = '8093';                                // Port for sql database management application
$charset  = 'utf8mb4';                             // UTF-8 encoding using 4 bytes of data per character
$username = 'admin';                               // Admin username
$password = 'domingo.18.noviembre';                // Password

// DO NOT CHANGE NEXT LINE
$dsn = "$type:host=$server;dbname=$db;port=$port;charset=$charset"; // Create Data Source Name

// SMTP Server settings
// TO DO: Set debug mode to 2. Debug was turned off even when in DEV to avoid debug messages being printed in page
$email_config = [
    'server'       => 'smtp.outlook.com',
    'port'         => '587',
    'username'     => 'hrparser@outlook.com',
    'password'     => 'h8XaL1mSHUv',
    'security'     => 'tls',
    'admin_email'  => 'hrparser@outlook.com',
    'debug'        => (DEV) ? 0 : 0
];

?>
