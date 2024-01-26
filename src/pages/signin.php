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
 * @var Array to collect the user inputs from form
 */
$h_sIUser = [$sI_map['email']   => "",
             $sI_map['pwd']     => ""];

/*
 * @var Variable that hast the data validation error to display.
 *      We use a generic error to avoid giving details
 */
$h_sIError  = "";

/**
 * @var Flag to track if signIn form has been submitted
 */
$signInDialogFormSubmitted = false;

/*
 * @var Array to collect the errors from user data validation and
 *      sign in process (not used yet)
 */
$signInErrors = [$sI_map['email']     => "",
                 $sI_map['pwd']       => ""];

/**
 * @var User data validation invalid status
 */
$signInValidationInvSts = false;

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    if(isset($_POST["signInForm"])){
        // SignUp form submitted
        $signInDialogFormSubmitted = true;

        // Allocate signUpUser object instance
        $signInUser = new SignInUser($sI_map);

        // Collect the data from client form
        $h_sIUser = filter_input_array(INPUT_POST);

        // Collect the inputed user data into signUpUser instance
        $signInUser->collectUserInputData($h_sIUser[$sI_map['email']],
                                          $h_sIUser[$sI_map['pwd']]);

        // Validate the collected user data
        $signInValidationInvSts = $signInUser->validateUserInputData($signInErrors);

        // Get the user data to be displayed again in client form (in case validation fails)
        $h_sIUser = $signInUser->getUserDataToPublish();

        // If no errors sign In the member on databases
        if(!$signInValidationInvSts){             // If no errors

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
                // Populate the error detail (not used so far)
                $signInErrors[$sI_map['email']] = 'The user is not signed up or password is incorrect';

                // Show a generic error on page to avoid displaying error details
                $h_sIError = 'The email or password are incorrect';
            }
        }
        else{
            // Show a generic error on page to avoid displaying error details
            $h_sIError = 'The email or password are incorrect';
        }

        // Delete variable that will not be needed in front-end
        unset($h_sIUser[$sI_map['pwd']]);
    }

}

// Populate twig template
$twig_data['h_sIUser'] = $h_sIUser;
$twig_data['h_sIError'] = $h_sIError;
$twig_data['h_minioServer'] = MINIO_SERVER;
$twig_data['h_minioPort'] = MINIO_PORT;
$twig_data['h_myResourcesBucketName'] = MINIO_RESOURCES_BUCKT_NAME;

// Render Twig template
echo $twig->render('signInUp/signIn.html', $twig_data);

?>

