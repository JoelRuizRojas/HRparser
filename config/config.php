<?php
define('DEV','true');                              // In development or live? Development = true | Live = false
define('DOMAIN', 'http://localhost:8888');         // Domain (used to create links in emails)
define('ROOT_FOLDER', 'public');                   // Name of document root folder (e.g. public, content, htdocs)

// DOC_ROOT is created because there are several page implementations below htdocs folder
// On a live site a single forward slash / would indicate the document root folder
$this_folder   = substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT']));
$parent_folder = dirname($this_folder);            // Get parent directory
define('DOC_ROOT', $parent_folder . DIRECTORY_SEPARATOR . ROOT_FOLDER . DIRECTORY_SEPARATOR);

?>
