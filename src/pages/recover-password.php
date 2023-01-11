<?php

/**
 * recover-password.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */


// Includes
require APP_ROOT . '/src/utilities/criticalFields.php';              // Import critical fields to be used

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
$h_vECodes = [$vE_map['code0']   => "",
              $vE_map['code1']   => "",
              $vE_map['code2']   => "",
              $vE_map['code3']   => "",
              $vE_map['code4']   => "",
              $sI_map['email']   => ""];

/**
 * @var Traced error from verification email dialog
 */
$h_vEDialogError = "";

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

        // Sanitize the fields to be published directly to client browser
        $h_fPUserEmail = SignUpUser::sanitizeUserEmail($h_fPUserEmail[0]);
    }
    else if(isset($_POST["verifyEmailForm"])){
        // Verify Email dialog form submitted
        $h_vEDialogFormSubmitted = true;

        // Collect verification codes
        $h_vECodes = filter_input_array(INPUT_POST);

        // Validate given verification codes
        $h_vEDialogError = SignUpUser::rcvrUsrPwd_codesValidation($h_vECodes);

        // Sanitize the email provided in hidden input to be displayed again
        $h_fPUserEmail = SignUpUser::sanitizeUserEmail($h_vECodes[$sI_map['email']]);
    }
    else if(isset($_POST["createNewPwdForm"])){
        // Validated pwd
        $validatedPwd = "";

        // Create New Pwd dialog form submitted
        $h_nPDialogFormSubmitted = true;

        // Collect new password and confirmation password inputs
        $h_nPUser = filter_input_array(INPUT_POST);

        // Test if password fulfills the security criteria
        $h_nPErrors[$sU_map['pwd']] = SignUpUser::validateUserPassword($h_nPUser[$sU_map['pwd']], $validatedPwd);

        // Test if confirmation password matches the first given password
        $h_nPErrors[$sU_map['confPwd']] =  $h_nPUser[$sU_map['confPwd']] == $validatedPwd ?
                                            '' : 'Given passwords do not match each other';
        
        // Last element of array shows the errors imploded recursively (helps twig wrapper)
        $h_nPErrors["implodedErrors"] = SignUpUser::implodeArrayContent($h_nPErrors);

        /* Sanitize the email provided in hidden input to be displayed again.
         * Email track can be lost if createNewDialog is submitted with errors */
        $h_fPUserEmail  = SignUpUser::sanitizeUserEmail($h_nPUser[$sI_map['email']]);
    }
}

// Populate twig template
$twig_data['h_fPDialogFormSubmitted'] = $h_fPDialogFormSubmitted;
$twig_data['h_fPUserEmail'] = $h_fPUserEmail;
$twig_data['h_fPDialogError'] = $h_fPDialogError;

$twig_data['h_vEDialogFormSubmitted'] = $h_vEDialogFormSubmitted;
$twig_data['h_vECodes'] = $h_vECodes;
$twig_data['h_vEDialogError'] = $h_vEDialogError;

$twig_data['h_nPDialogFormSubmitted'] = $h_nPDialogFormSubmitted;
$twig_data['h_nPUser'] = $h_nPUser;
$twig_data['h_nPErrors'] = $h_nPErrors;

// Render Twig template
echo $twig->render('signInUp/recoverPassword.html', $twig_data);

?>
