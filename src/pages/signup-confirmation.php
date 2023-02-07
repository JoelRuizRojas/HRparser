<?php

/**
 * signup-confirmation.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

use HRparser\CMS\Token;

// If token is not provided, redirect to sign-in page
$token = $_GET['token'] ?? '';                       // Retrieve token
if(!$token){
    redirect('signin');
}

// Verify that token is valid:
//   1) Token has not expired
//   2) Token can be used for given reason
$id = null;
$result = $cms->getToken()->validate($token, 'signup_confirmation', $id);

// If token is not valid, go to sign-in page
// If token is valid, proceed to confirm the sign-up process for $id member
if($result == Token::TOKEN_VALID){
    // Token valid, now proceed to confirm the user account
    $cms->getMember()->confirmAccount($id);

    /* After member account is confirmed, delete the token immediately
     * Anyway, the token is valid only for 4 hours */
    $cms->getToken()->remove($token);

    // Go to sign-in page
    redirect('signin');
}
else{
    redirect('signin');
}

?>
