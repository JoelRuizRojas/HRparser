<?php

/**
 * signin.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */


// Includes
require APP_ROOT . '/src/utilities/criticalFields.php'; // Import critical fields to be used

use HRparser\SignUpUser\SignUpUser;                     // Import Validate class

/* Naming convention rules:
 * Example $h_sUUser
 *          1 2 3
 *
 * 1)  h    ->  Indicates that this variable will be used in html page
 * 2)  sU   ->  Indicates that this variable relates to signUp process
 *              (see below dictionary for all options available)
 * 3)  User ->  Custom name for variable
 *
 *
 * Dictionary:
 *      sU  ->  Relates to SignUp process
 *      sI  ->  Relates to SignIn process
 *      fP  ->  Relates to ForgotPwd process 
 *      vE  ->  Relates to VerifyEmail process 
 *      nP  ->  Relates to Create New Password process */

// Populate twig template
$twig_data = [];

// Render Twig template
echo $twig->render('signInUp/signIn.html', $twig_data);

?>

