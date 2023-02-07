<?php

/**
 * signup.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */


// Includes
require APP_ROOT . '/src/utilities/criticalFields.php';              // Import critical fields to be used

use HRparser\SignInUpUser\SignUpUser;                                // Namespace usage
use HRparser\CMS\MemberAttr;                                   
use HRparser\CMS\Member;

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
 * @var Flag to track if signUp form has been submitted
 */
$h_sUDialogFormSubmitted = false;

/**
 * @var Array to collect the user inputs from form
 */
$h_sUUser = [$sU_map['name']      => "",
             $sU_map['lastName']  => "",
             $sU_map['country']   => "",
             $sU_map['email']     => "",
             $sU_map['pwd']       => "",
             $sU_map['confPwd']   => "",
             $sU_map['terms']     => false]; 

/*
 * @var Array to collect the errors from user data validation
 */
$h_sUErrors = [$sU_map['name']      => "",
               $sU_map['lastName']  => "",
               $sU_map['country']   => "",
               $sU_map['email']     => "",
               $sU_map['pwd']       => ['', '', ''],
               $sU_map['confPwd']   => "",
               $sU_map['terms']     => ""];

/**
 * @var User data validation invalid status
 */
$h_sUValidationInvSts = false;

/**
 * @var Link to resent email confirmation upon a successful registration
 *      By default go to sign-up confirmation page
 */
$h_sUResendVerifEmailLink = DOMAIN . DOC_ROOT . 'signup';


if($_SERVER['REQUEST_METHOD'] == 'POST'){

    // MEMBER REGISTRATION
    if(isset($_POST["signUpForm"])){
        // SignUp form submitted
        $h_sUDialogFormSubmitted = true;

        // Allocate signUpUser object instance
        $signUpUser = new SignUpUser($sU_map);

        // Collect the data from client form
        $h_sUUser = filter_input_array(INPUT_POST);

        // Collect the inputed user data into signUpUser instance
        $signUpUser->collectUserInputData($h_sUUser[$sU_map['name']],
                                          $h_sUUser[$sU_map['lastName']],
                                          $h_sUUser[$sU_map['country']],
                                          $h_sUUser[$sU_map['email']],
                                          $h_sUUser[$sU_map['pwd']],
                                          $h_sUUser[$sU_map['confPwd']],
                                          $h_sUUser[$sU_map['terms']]);

        // Validate the collected user data
        $h_sUValidationInvSts = $signUpUser->validateUserInputData($h_sUErrors);

	    // Get the user data to be displayed again in client form (in case validation fails)
        $h_sUUser = $signUpUser->getUserDataToPublish();

        // If no errors sign Up the member on databases
        if(!$h_sUValidationInvSts){                  // If no errors
            
            // Build object to populate in databases
            $member = new MemberAttr();
            $member->forename   = $h_sUUser[$sU_map['name']];
            $member->surname    = $h_sUUser[$sU_map['lastName']];
            $member->email      = $h_sUUser[$sU_map['email']];
            $member->country    = $h_sUUser[$sU_map['country']];
            $member->password   = $h_sUUser[$sU_map['pwd']];
            $member->role_id    = 4;  // For now only homeopath role is allowed to register

            // User CMS to write into database the new member data
            $result = $cms->getMember()->signUp($member);

            if($result === Member::MEMBER_NO_ERROR){
                // Get id of member
                $id = $cms->getMember()->getIdByEmail($member->email);

                /* Create token for user to confirm his/her account. 
                 * Valid for 4 hours only by default */
                $token = $cms->getToken()->create($id, 'signup_confirmation');

                // Create link that will be emailed to user to confirm his/her account
                $link = DOMAIN . DOC_ROOT . 'signup-confirmation?token=' . $token;

                // Build registration html email using resource from functions.php
                $email = buildRegistrationHtmlEmail($member->forename, $link, $email_config['admin_email']);

                // Build email object instance to send email
                $mail = new \HRparser\Email\Email($email_config);
                $sent = $mail->sendEmail($email_config['admin_email'], $member->email, $email['subject'], $email['body']);

                /* Build the resend confirmation email link so that it can
                 * call the sign-up page again with some query string params
                 * to be able to build the verification email mail again */
                $h_sUResendVerifEmailLink = $h_sUResendVerifEmailLink . 
                                            '?email=' . $member->email . '&' .
                                            'purpose=signup_confirmation';
            }
            else{
                // Raise error flag and populate the error to be shown on page
                $h_sUValidationInvSts = true;
                $h_sUErrors[$sU_map['email']] = 'Email address already used';
            }
        }

        // Delete variable that will not be needed in front-end
        unset($h_sUUser[$sU_map['pwd']], $h_sUUser[$sU_map['confPwd']], 
              $h_sUUser[$sU_map['terms']]);
    }
}
else if($_SERVER['REQUEST_METHOD'] == 'GET'){

    // RESEND EMAIL VERIFICATION TO MEMBER 
    if(isset($_GET['purpose']) == 'signup_confirmation'){
        $email = $_GET['email'];
        $purpose = $_GET['purpose'];

        // Check if email is provided
        if(isset($email)){
            // Get id of member
            $id = $cms->getMember()->getIdByEmail($email);

            // Confirm member exists
            if($id){
                // Get member data
                $member = $cms->getMember()->getById($id);

                // Only resend if member has not verified his/her account
                if(!isset($member->joined)){
                    // Retrieve a valid token
                    $token = $cms->getToken()->retrieve($id, $purpose);

                    // Create link that will be emailed to user to confirm his/her account
                    $link = DOMAIN . DOC_ROOT . 'signup-confirmation?token=' . $token;

                    // Build registration html email using resource from functions.php
                    $email = buildRegistrationHtmlEmail($member->forename, $link, $email_config['admin_email']);

                    // Build email object instance to send email
                    $mail = new \HRparser\Email\Email($email_config);
                    $sent = $mail->sendEmail($email_config['admin_email'], $member->email, $email['subject'], $email['body']);
                }
            }
        }
        else{
            // If email not provided, do not resend the email verification
        }
    }
}
else{
    // Do nothing
}

// Populate twig template
$twig_data['h_sUDialogFormSubmitted'] = $h_sUDialogFormSubmitted;
$twig_data['h_sUUser'] = $h_sUUser;
$twig_data['h_sUErrors'] = $h_sUErrors;
$twig_data['h_sUValidationInvSts'] = $h_sUValidationInvSts;
$twig_data['h_countries'] = $h_countries;
$twig_data['h_sUResendVerifEmailLink'] = $h_sUResendVerifEmailLink;

// Render Twig template
echo $twig->render('signInUp/signUp.html', $twig_data);

?>

