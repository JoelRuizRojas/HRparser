<?php

/**
 * signin.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */


// Includes
require APP_ROOT_PATH . '/src/utilities/criticalFields.php'; // Import critical fields to be used

use HRparser\SignInUpUser\SignInUser;                   // Namespace usage
use HRparser\CMS\MemberAttr;

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

/**
 * @var Flag to track if signIn form has been submitted
 */
$h_sIDialogFormSubmitted = false;

/**
 * @var Array to collect the user inputs from form
 */
$h_sIUser = [$sI_map['email']   => "",
             $sI_map['pwd']     => ""];

/*
 * @var Array to collect the errors from user data validation (not used yet)
 */
$h_sIErrors = [$sI_map['email']     => "",
               $sI_map['pwd']       => ""];

/*
 * @var Variable that hast the data validation error to display
 */
$h_sIError  = "";

/**
 * @var User data validation invalid status
 */
$h_sIValidationInvSts = false;

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    if(isset($_POST["signInForm"])){
        // SignUp form submitted
        $h_sIDialogFormSubmitted = true;

        // Allocate signUpUser object instance
        $signInUser = new SignInUser($sI_map);

        // Collect the data from client form
        $h_sIUser = filter_input_array(INPUT_POST);

        // Collect the inputed user data into signUpUser instance
        $signInUser->collectUserInputData($h_sIUser[$sI_map['email']],
                                          $h_sIUser[$sI_map['pwd']]);

        // Validate the collected user data
        $h_sIValidationInvSts = $signInUser->validateUserInputData($h_sIErrors);

        // Get the user data to be displayed again in client form (in case validation fails)
        $h_sIUser = $signInUser->getUserDataToPublish();

        // If no errors sign In the member on databases
        if(!$h_sIValidationInvSts){                  // If no errors

            // Use CMS to signIn the user
            $member = $cms->getMember()->signIn($h_sIUser[$sI_map['email']],
                                                $h_sIUser[$sI_map['pwd']]);

            // Check the result from sign in process
            if($member && ($member->role_id == 'suspended')){
                $h_sIError = 'Account suspended';
            }
            else if($member && !isset($member->joined)){
                $h_sIError = 'Account not activated. Verification email sent';

                // Resend a verification account email
                // Retrieve a valid token
                $token = $cms->getToken()->retrieve($member->id, 'signup_confirmation');

                // Create link that will be emailed to user to confirm his/her account
                $link = DOMAIN . DOC_ROOT . 'signup-confirmation?token=' . $token;

                // Build registration html email using resource from functions.php
                $email = buildRegistrationHtmlEmail($member->forename, $link, $email_config['admin_email']);

                // Build email object instance to send email
                $mail = new \HRparser\Email\Email($email_config);
                $sent = $mail->sendEmail($email_config['admin_email'], $member->email, $email['subject'], $email['body']);
            }
            else if($member){
                // LOGIN SUCCESSFULLY
            }
            else{
                // Raise error flag and populate the error to be shown on page
                $h_sIValidationInvSts = true;
                $h_sIErrors[$sI_map['email']] = 'The email or password are incorrect';
                $h_sIError = 'The email or password are incorrect';
            }
        }
        else{
            $h_sIError = 'The email or password are incorrect';
        }

        // Delete variable that will not be needed in front-end
        unset($h_sIUser[$sI_map['pwd']]);
    }

}

// Populate twig template
$twig_data['h_sIDialogFormSubmitted'] = $h_sIDialogFormSubmitted;
$twig_data['h_sIUser'] = $h_sIUser;
$twig_data['h_sIError'] = $h_sIError;
$twig_data['h_sIValidationInvSts'] = $h_sIValidationInvSts;

// Render Twig template
echo $twig->render('signInUp/signIn.html', $twig_data);

?>

