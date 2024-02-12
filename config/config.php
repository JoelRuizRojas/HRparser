<?php

/**
 * config.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

define('DEPLOYMENT_ENV', getenv('DEPLOYMENT_ENV'));// In development or prod?
define('ENABLE_LOGS', getenv('ENABLE_LOGS'));      // Enable logs?
define('DOMAIN', 'http://localhost:8092');         // Domain (used to create links in emails)

// DOC_ROOT is created because there are several page implementations below htdocs folder
// On a live site a single forward slash / would indicate the document root folder
define('DOC_ROOT', DIRECTORY_SEPARATOR);

/* Get IP address of minio server. At this point this container
 * must be running */
define('MINIO_SERVER', gethostbyname("minio"));
define('MINIO_PORT', getenv('MINIO_PORT'));
define('MINIO_RESOURCES_BUCKT_NAME', getenv('MY_RESOURCES_BUCKET_NAME'));

// Database settings
$type     = 'mysql';                               // Type of database
$server   = 'login-db-server';                     // Server the database is on
$db       = getenv('MYSQL_DATABASE');              // Name of the database
$port     = getenv('MYSQL_SERVER_PORT');           // Port for sql database management application
$charset  = 'utf8mb4';                             // UTF-8 encoding using 4 bytes of data per character
$username = 'root';                                // Admin username
$password = 'domingo.18.noviembre';                // Password

// Values for production
if(DEPLOYMENT_ENV == "production"){
    $server   = 'login-db-server-svc';             // Cluster IP service to communicate with a db pod
    $port     = 'mariadbport';                     // Port defined in ClusterIP service
}

// DO NOT CHANGE NEXT LINE
$dsn = "$type:host=$server;dbname=$db;port=$port;charset=$charset"; // Create Data Source Name

// SMTP Server settings
// TO DO: Set debug mode to 2. Debug was turned off even when ENABLE_LOGS = true, to avoid debug messages being printed in page
$email_config = [
    'server'       => 'smtp.outlook.com',
    'port'         => '587',
    'username'     => 'hrparser@outlook.com',
    'password'     => 'h8XaL1mSHUv',
    'security'     => 'tls',
    'admin_email'  => 'hrparser@outlook.com',
    'debug'        => (ENABLE_LOGS) ? 0 : 0
];

?>
