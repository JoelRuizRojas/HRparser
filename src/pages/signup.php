<?php

/**
 * signup.php
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
}

// Populate twig template
$twig_data['h_sUDialogFormSubmitted'] = $h_sUDialogFormSubmitted;
$twig_data['h_sUUser'] = $h_sUUser;
$twig_data['h_sUErrors'] = $h_sUErrors;
$twig_data['h_sUValidationInvSts'] = $h_sUValidationInvSts;

$twig_data['h_countries'] = $h_countries;

// Render Twig template
echo $twig->render('signInUp/signUp.html', $twig_data);

?>

