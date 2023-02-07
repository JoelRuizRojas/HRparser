<?php

/**
 * functions.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

/**
 * Redirects to given location
 *
 * @param $location, new page to visit              (IN)
 * @param $parameters, list with query parameters   (IN)
 * @param $response_code, response code             (IN)
 * @return none
 */
function redirect(string $location, array $parameters = [], $response_code = 302)
{
    $qs = $parameters ? '?' . http_build_query($parameters) : '';  // Create query string
    $location = $location . $qs;                                   // Create new path
    header('Location: ' . DOC_ROOT . $location, $response_code);   // Redirect to new page
    exit;                                                          // Stop code
}

/**
 * Gets the HR parser release time stamp
 *
 * @param none
 * @return Unix timestamp of HRparser release date
 */
function getReleaseTimestamp(): int
{
    // Define our release date
    return strtotime('2023-01-01 00:00:00');
}

/**
 * Builds up the registration email for new member
 *
 * @param $memberName, Name of new member                      (IN)
 * @param $link, Link for member to validate his/her account   (IN)
 * @param $senderEmail, Email from sender                      (IN)
 * @return array with subject, and body of email
 */
function buildRegistrationHtmlEmail(string $memberName, string $link, string $senderEmail): array
{
    // Create subject
    $subject = "Thank you for registering, access your account";

    // Create body
    $body = "<h2>Hello " . $memberName . ",</h2><br>" .
            "<p>Thank you for joining HR Parser portal.</p><br>" .
            "<p>We'd like to confirm that your account was created successfully.
                To confirm your account in HR Parser portal, click the link below</p><br>" .
            '<a href="' . $link . '">' . $link . "</a><br>" .
            "<p>If you experience any issues logging into your account, reach out to us at " .
                $senderEmail . ". </p><br>" .
            "<p>Best,<br>" .
            "The HR Parser team</p>";

    // Create array and return the registration email
    return ['subject' => $subject, 'body' => $body];
}

/**
 * Builds up the reset password verification email member
 *
 * @param $memberName, Name of new member (IN)
 * @param $verificationCode, Verification code for member to change the password (IN)
 * @return array with subject, and body of email
 */
function buildResetPasswordHtmlEmail(string $memberName, string $verificationCode): array
{
    // Create subject
    $subject = "Reset your password";

    // Create body
    $body = "<h2>Hello " . $memberName . ",</h2><br>" .
            "<p>Forgot your password?.</p><br>" .
            "<p>We received a request to reset the password for you account.
                Use next verification code to reset your password:</p><br>" .
            "<h3>" . $verificationCode . "</h3>" .
            "<p>If you did not make this request then please ignore this email.</p><br>" .
            "<p>Best,<br>" .
            "The HR Parser team</p>";

    // Create array and return the reset password verification email
    return ['subject' => $subject, 'body' => $body];
}

/**
 * Encrypts/Decrypts the given text
 *
 * @param $text, Text to encrypt             (IN)
 * @param $action, Either encrypt or decrypt (IN)
 * @return the encrypted or decrypted text or false condition (failure)
 */
function text_crypt(string $text, $action = 'encrypt')
{
    // Define your own secret key and initializer vector   
    $secret_key = 'h8XaL1mSHUv';
    $secret_iv = 'domingo.18.noviembre';

    // Default error value
    $retVal = false;

    // Define encryption/decryption method and hash the secret key and iv
    $encrypt_method = "AES-256-CBC";
    $key = hash('sha256', $secret_key );
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if( $action == 'encrypt' ) {
        $retVal = base64_encode(openssl_encrypt($text, $encrypt_method, $key, 0, $iv));
    }
    else if( $action == 'decrypt' ){
        $retVal = openssl_decrypt(base64_decode($text), $encrypt_method, $key, 0, $iv);
    }

    return $retVal;
}

?>