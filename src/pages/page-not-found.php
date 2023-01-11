<?php

/**
 * page-not-found.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

declare(strict_types=1);                                 // Use strict types

http_response_code(404);                                 // Set HTTP response code

$data = [];

echo $twig->render('page-not-found.html', $data);        // Render template

exit;
