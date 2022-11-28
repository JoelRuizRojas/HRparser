<?php

/**
 * HRparser_loginUtils.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */


// Includes
include 'CriticalFields.php';
include 'SignUpUser.php';
include 'SignInUser.php';

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

/***** SIGN UP PROCESS VARIABLES *****/

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

/***** END SIGN UP PROCESS VARIABLES *****/

/***** FORGOT PASSWORD PROCESS VARIABLES *****/

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
 */
$h_fPUserEmail = "";

/***** END FORGOT PASSWORD PROCESS VARIABLES *****/

/***** VERIFICATION EMAIL PROCESS VARIABLES *****/

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
           
/***** END VERIFICATION EMAIL PROCESS VARIABLES *****/

/***** CREATE NEW PASSWORD PROCESS VARIABLES *****/

/**
 * @var Flag to track if createNewPwd dialog form has been submitted
 */
$h_nPDiailogFormSubmitted = false;

/**
 * @var Array to collect the user new password inputs from form
 */
$h_nPUser = [$sU_map['pwd']       => "",
	     $sU_map['confPwd']   => ""];

/**
 * @var $h_nPErrors Errors from CreateNewPassword dialog form submitted
 * */
$h_nPErrors = [$sU_map['pwd']        => ['', '', ''],
	       $sU_map['confPwd']    => ""];

/***** END CREATE NEW PASSWORD PROCESS VARIABLES *****/

if($_SERVER['REQUEST_METHOD'] == 'POST'){

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
    }
    else if(isset($_POST["forgotPwdForm"])){
	// Forgot Pwd dialog form submitted
	$h_fPDialogFormSubmitted = true;

	// Collect user email
	$h_fPUserEmail = filter_input(INPUT_POST, $sI_map['email']);

	// Validate given email
	$h_fPDialogError = SignInUser::rcvrUsrPwd_emailValidation($h_fPUserEmail);

	// Sanitize the fields to be published directly to client browser
        $h_fPUserEmail  = filter_var($h_fPUserEmail, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    else if(isset($_POST["verifyEmailForm"])){
	// Verify Email dialog form submitted
	$h_vEDialogFormSubmitted = true;

	// Collect verification codes
	$h_vECodes = filter_input_array(INPUT_POST);

	// Validate given verification codes
	$h_vEDialogError = SignInUser::rcvrUsrPwd_codesValidation($h_vECodes);

	// Sanitize the email provided in hidden input to be displayed again
	$h_fPUserEmail  = filter_var($h_vECodes[$sI_map['email']], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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

	/* Sanitize the email provided in hidden input to be displayed again.
	 * Email track can be lost if createNewDialog is submitted with errors */
        $h_fPUserEmail  = filter_var($h_nPUser[$sI_map['email']], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

}

?>
