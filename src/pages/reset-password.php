<?php

/**
 * reset-password.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */


// Includes
require APP_ROOT_PATH . '/src/utilities/criticalFields.php';         // Import critical fields to be used

use HRparser\SignInUpUser\SignUpUser;                                // Import Validate class

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
 * @var Flag to track if forgotPwd dialog form has been submitted
 */
$h_fPDialogFormSubmitted = false;

/**
 * @var Traced error from forgotPwdDialog submission
 */
$h_fPDialogError = "";

/**
 * @var User email to be populated again in forgot password form
 *      [0] Raw&cured user email
 *      [1] Truncated user email
 */
$h_fPUserEmail = ["", ""];

/**
 * @var Flag to track if verifyEmail dialog form has been submitted
 */
$h_vEDialogFormSubmitted = false;

/**
 * @var Array to collect the verification codes from form
 */
$h_vECodesArr = [$vE_map['code0']   => "",
                 $vE_map['code1']   => "",
                 $vE_map['code2']   => "",
                 $vE_map['code3']   => "",
                 $vE_map['code4']   => ""];

/**
 * @var Traced error from verification email dialog
 */
$h_vEDialogError = "";

/**
 * @var Data needed to perform the "Resend verification code" in javascript
 */
$h_vEResendVerifCodePostRequestData = ['name'  => 'forgotPwdForm',
                                       'email' => '',
                                       'url'   => DOMAIN . DOC_ROOT . 'reset-password'];

/**
 * @var Flag to track if createNewPwd dialog form has been submitted
 */
$h_nPDialogFormSubmitted = false;

/**
 * @var Array to collect the user new password inputs from form
 */
$h_nPUser = [$sU_map['pwd']       => "",
             $sU_map['confPwd']   => ""];

/**
 * @var $h_nPErrors Errors from CreateNewPassword dialog form submitted
 * */
$h_nPErrors = [$sU_map['pwd']        => ['', '', ''],
               $sU_map['confPwd']    => "",
               "implodedErrors"      => ""];

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    if(isset($_POST["forgotPwdForm"])){

        // Forgot Pwd dialog form submitted
        $h_fPDialogFormSubmitted = true;

        // Collect user email
        $h_fPUserEmail[0] = filter_input(INPUT_POST, $sI_map['email']);

        // Validate given email
        $h_fPDialogError = SignUpUser::rcvrUsrPwd_emailValidation($h_fPUserEmail[0]);

        // If email format is valid
        if(!$h_fPDialogError){
            // Get id of member
            $id = $cms->getMember()->getIdByEmail($h_fPUserEmail[0]);

            // Confirm member exist
            if($id){
                // Get member data
                $member = $cms->getMember()->getById($id);

                // Only member with account confirmed can reset password
                if(isset($member->joined)){

                    /* Create token for user to get verification code to change the password.
                     * Valid for 5 minutes only */
                    $verificationCode = strval(random_int(10000, 99999));      // Verification code is 5 digits long
                    $token = $cms->getToken()->retrieve($id, 'reset_password', newTokenSeed: $verificationCode, newTokenExpires: '+5 minutes');

                    // Decrypt to get verification code again
                    $verificationCode = text_crypt($token, 'decrypt');

                    // Build reset password html email using resource from functions.php
                    $email = buildResetPasswordHtmlEmail($member->forename, $verificationCode);

                    // Build email object instance to send email
                    $mail = new \HRparser\Email\Email($email_config);
                    $sent = $mail->sendEmail($email_config['admin_email'], $member->email, $email['subject'], $email['body']);

                    /* Populate the data that will be needed by front-end to perform
                     * the http POST request to resend the verification code */
                    $h_vEResendVerifCodePostRequestData['name'] = 'forgotPwdForm';
                    $h_vEResendVerifCodePostRequestData['email'] = $h_fPUserEmail[0];
                    $h_vEResendVerifCodePostRequestData['url'] = DOMAIN . DOC_ROOT . 'reset-password';

                    // Sanitize the fields to be published directly to client browser
                    $h_fPUserEmail = SignUpUser::sanitizeUserEmail($h_fPUserEmail[0]);
                }
                else{
                    // Member account not activated yet
                    $h_fPDialogError = "User account not activated yet";
                }
            }
            else{
                // Member does not exist, populate error
                $h_fPDialogError = "User not registered";
            }
        }        
    }
    else if(isset($_POST["verifyEmailForm"])){
        // Verify Email dialog form submitted
        $h_vEDialogFormSubmitted = true;

        // Collect verification codes and hidden input email
        $verifyEmailFormInput = array_chunk(filter_input_array(INPUT_POST), SignUpUser::VE_INPUTS_NUM);
        $h_vECodesArr = $verifyEmailFormInput[0];
        $email = $verifyEmailFormInput[1][0];

        // Validate given verification codes
        $h_vEDialogError = SignUpUser::rcvrUsrPwd_codesValidation($h_vECodesArr);

        // If verification code format is valid
        if(!$h_vEDialogError){

            // Get id of member
            $id = $cms->getMember()->getIdByEmail($email);

            // Confirm member exist
            if($id){
                // Get member data
                $member = $cms->getMember()->getById($id);

                // Only member with account confirmed can reset password
                if(isset($member->joined)){

                    // Build token with given verification codes
                    $verificationCodeStr = implode($h_vECodesArr);
                    $token = text_crypt($verificationCodeStr, 'encrypt');

                    // Verify that token is valid:
                    //   1) Token has not expired
                    //   2) Token can be used for given reason
                    $result = $cms->getToken()->validate($token, 'reset_password');

                    // Actions on token verification
                    if($result == $cms->getToken()::TOKEN_EXPIRED){
                        $h_vEDialogError = "Verification code expired";

                        // Delete useless token
                        $cms->getToken()->remove($token);
                    }
                    elseif($result == $cms->getToken()::TOKEN_INVALID){
                        // No need to delete since given token does not exist
                        $h_vEDialogError = "Verification code invalid";
                    }
                    else{
                        /* After verification code is confirmed, delete the given token
                         * and also all tokens that might be created and are not deleted
                         * Anyway, the token is valid only for 5 minutes */
                        $cms->getToken()->removeAll($member->id, 'reset_password');

                        /* Create internal token to change password in next front-end dialog.
                         * Valid for 10 minutes. To be validated when user actually changes the password. */
                        $token = $cms->getToken()->retrieve($member->id, 'change_password', newTokenExpires: '+10 minutes');
                    }
                }
                else{
                    // Member account not activated yet
                    $h_vEDialogError = "User account not activated yet";
                }
            }
            else{
                // Member does not exist, populate error
                $h_vEDialogError = "User not registered";
            }
 
            // Sanitize the email provided in hidden input to be displayed again
            $h_fPUserEmail = SignUpUser::sanitizeUserEmail($email);
        }
        
        /* Populate the data that will be needed by front-end to perform
         * the http POST request to resend the verification code */
        $h_vEResendVerifCodePostRequestData['name'] = 'forgotPwdForm';
        $h_vEResendVerifCodePostRequestData['email'] = $email;
        $h_vEResendVerifCodePostRequestData['url'] = DOMAIN . DOC_ROOT . 'reset-password';
    }
    else if(isset($_POST["createNewPwdForm"])){
        // Validated pwd
        $validatedPwd = "";

        // Create New Pwd dialog form submitted
        $h_nPDialogFormSubmitted = true;

        // Collect new password and confirmation password inputs, and hidden input email
        $h_nPUser = filter_input_array(INPUT_POST);
        $email = $h_nPUser[$sI_map['email']];

        // Test if password fulfills the security criteria
        $h_nPErrors[$sU_map['pwd']] = SignUpUser::validateUserPassword($h_nPUser[$sU_map['pwd']], $validatedPwd);

        // Test if confirmation password matches the first given password
        $h_nPErrors[$sU_map['confPwd']] =  $h_nPUser[$sU_map['confPwd']] == $validatedPwd ?
                                            '' : 'Given passwords do not match each other';
        
        // Last element of array shows the errors imploded recursively (helps twig wrapper)
        $h_nPErrors["implodedErrors"] = SignUpUser::implodeArrayContent($h_nPErrors);

        // If verification code format is valid
        if(!$h_nPErrors["implodedErrors"]){

            // Get id of member
            $id = $cms->getMember()->getIdByEmail($email);

            // Confirm member exist
            if($id){
                // Get member data
                $member = $cms->getMember()->getById($id);

                // Only member with account confirmed can change password
                if(isset($member->joined)){
                    /* Check if there is a valid token for member to proceed and save new given password */
                    $token = $cms->getToken()->retrieve($member->id, 'change_password', createNewTokenFlag: false);

                    // Was a valid token found?
                    if($token){
                        // Get current password
                        $currentHashedPwd = $cms->getMember()->getPasswordById($member->id);

                        // Check if new vs old are the same 
                        $samePassword = password_verify($validatedPwd, $currentHashedPwd);

                        // Verify that new password is different than previous one
                        if(!$samePassword){
                            // Save new password
                            $result = $cms->getMember()->updatePassword($member->id, $validatedPwd); 

                            // Verify password was saved successfully
                            if($result != $cms->getMember()::MEMBER_NO_ERROR){
                                // Error saving password
                                $h_nPErrors[$sU_map['confPwd']] = "Error saving new password. Contact us at " . 
                                                                  $email_config['admin_email'];

                                // Create url to reset password, to be added to email
                                $resetPwdUrl = DOMAIN . DOC_ROOT . 'reset-password';

                                // Send email to member about password being changed
                                $email = buildFailedChangedPasswordHtmlEmail($member->forename, $member->email, $resetPwdUrl, $email_config['admin_email']);

                                // Build email object instance to send email
                                $mail = new \HRparser\Email\Email($email_config);
                                $sent = $mail->sendEmail($email_config['admin_email'], $member->email, $email['subject'], $email['body']);
                            }
                            else{
                                // After successfully saving the new password, delete change_password token
                                $cms->getToken()->remove($token);

                                // Create url to reset password, to be added to email
                                $resetPwdUrl = DOMAIN . DOC_ROOT . 'reset-password';

                                // Send email to member about password being changed
                                $email = buildSuccessChangedPasswordHtmlEmail($member->forename, $member->email, $resetPwdUrl, $email_config['admin_email']);

                                // Build email object instance to send email
                                $mail = new \HRparser\Email\Email($email_config);
                                $sent = $mail->sendEmail($email_config['admin_email'], $member->email, $email['subject'], $email['body']);
                            }
                        }
                        else{
                            // Same password given
                            $h_nPErrors[$sU_map['confPwd']] = "New password must be different than current one";
                        }
                    }
                    else{
                        // change_password token for member expired
                        $h_nPErrors[$sU_map['confPwd']] = "Time to save password expired, go back and insert new verification code";
                    }
                }
                else{
                    // Member account not activated yet
                    $h_nPErrors[$sU_map['confPwd']] = "User account not activated yet";
                }
            }
            else{
                // Member does not exist, populate error
                $h_nPErrors[$sU_map['confPwd']] = "User not registered";
            }
        }

        // Implode errors again, in case new ones were detected 
        $h_nPErrors["implodedErrors"] = SignUpUser::implodeArrayContent($h_nPErrors);

        /* Sanitize the email provided in hidden input to be displayed again.
         * Email track can be lost if createNewDialog is submitted with errors */
        $h_fPUserEmail  = SignUpUser::sanitizeUserEmail($h_nPUser[$sI_map['email']]);

        /* In case member goes backward to generate a new verification code, 
         * populate the data that will be needed by front-end to perform
         * the http POST request to resend the verification code */
        $h_vEResendVerifCodePostRequestData['name'] = 'forgotPwdForm';
        $h_vEResendVerifCodePostRequestData['email'] = $email;
        $h_vEResendVerifCodePostRequestData['url'] = DOMAIN . DOC_ROOT . 'reset-password';
    }
}

// Populate twig template
$twig_data['h_fPDialogFormSubmitted'] = $h_fPDialogFormSubmitted;
$twig_data['h_fPUserEmail'] = $h_fPUserEmail;
$twig_data['h_fPDialogError'] = $h_fPDialogError;

$twig_data['h_vEDialogFormSubmitted'] = $h_vEDialogFormSubmitted;
$twig_data['h_vECodes'] = $h_vECodesArr;
$twig_data['h_vEDialogError'] = $h_vEDialogError;
$twig_data['h_vEResendVerifCodePostRequestData'] = $h_vEResendVerifCodePostRequestData;

$twig_data['h_nPDialogFormSubmitted'] = $h_nPDialogFormSubmitted;
$twig_data['h_nPUser'] = $h_nPUser;
$twig_data['h_nPErrors'] = $h_nPErrors;

$twig_data['h_minioServer'] = MINIO_SERVER;
$twig_data['h_minioPort'] = MINIO_PORT;
$twig_data['h_myResourcesBucketName'] = MINIO_RESOURCES_BUCKT_NAME;

// Render Twig template
echo $twig->render('signInUp/resetPassword.html', $twig_data);

?>
