<?php

/**
 * SignInUser.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

/**
 * @var $sI_map Mapping of SignIn form html variables to script tags
 */
$sI_map = ['email'     => 'signInEmail',
           'pwd'       => 'signInPwd'];

/**
 * @var $vE_map Mapping of verifyEmail Form html variables to script tags
 */
$vE_map = ['code0'     => 'verificationCode0',
           'code1'     => 'verificationCode1',
           'code2'     => 'verificationCode2',
           'code3'     => 'verificationCode3',
           'code4'     => 'verificationCode4'];

/**
 * @var $vE_INPUTS_NUM Number of inputs in verify email form
 *                     5 codes + hidden email + submit button
 * */
define("vE_INPUTS_NUM", 7);

/**
 * Class SignInUser
 * 
 * Template to create the resources to help a user on the signing
 * process into the application.
 */
class SignInUser
{
    /**
     * Class constructor
     *
     * @param $map Mapping of html form inputs to shorter tags (IN)
     * @return none
     */
    public function __construct($map)
    {
        // Get the mapping of html form inputs
        $this->map = $map;

	// Initialize array to collect user data
        $this->userRaw = [$this->map['email']     => "",
                          $this->map['pwd']       => ""];

        // Initialize array to process the user input data
	$this->userProcessed = $this->userRaw;

	// Initialize validation result
        $this->validationInvalid = "";
    }

    /**
     * Collects the user input data
     *
     * @param $email User email                                 (IN)
     * @param $pwd User sign up password                        (IN)
     * @return none
     */
    public function collectUserInputData($email, $pwd)
    {
        $this->userRaw[$this->map['email']]      = $email;
        $this->userRaw[$this->map['pwd']]        = $pwd;
    }

    /**
     * Validates the user data already collected
     *
     * @param none
     * @return $validationInvalid Result of user data validation
     */
    public function validateUserInputData(): string
    {
        // Validate the user inputed data
	$this->userProcessed[$this->map['email']] = filter_var($this->userRaw[$this->map['email']], FILTER_VALIDATE_EMAIL);

	$this->validationInvalid = $this->userProcessed[$this->map['email']] ?
		                   '' : 'Email format not fulfilled';

	if($this->validationInvalid){
	    return $this->validationInvalid;
	}
	else{
	   
	}
    }

    /**
     * Validates the input email
     *   Confirm the given email is part of a user registered
     *     Send recovery code to email
     *
     * @param $email User email        (IN)
     * @return $validationError Result of user data validation
     */
    public static function rcvrUsrPwd_emailValidation($email): string
    {
	// Validate the given email
	$validatedEmail = filter_var($email, FILTER_VALIDATE_EMAIL);

	$validationError = $validatedEmail ?
                           '' : 'Email not valid';

	// Check the given email belongs to a registered user
	
	// Send recovery code to email

	return $validationError;
    }

    /**
     * Verifies the given codes to recover user password
     *   Confirm the given verification codes match the ones temporarily store in dbs
     *
     * @param $verificationCodes Array of verification codes    (IN)
     * @return $validationError Result of the codes validation
     */
    public static function rcvrUsrPwd_codesValidation($verificationCodes): string
    {
	// Check the length of input array. 5 codes + Hidden email + SubmitButton
	if(count($verificationCodes) != vE_INPUTS_NUM)
	    return 'Invalid Code';

	// Iterate over every code and apply validation
	$i = 0;
	foreach($verificationCodes as $key => $code){
	    if($i >= (vE_INPUTS_NUM - 2))
		break;

            $settings['options']['regexp'] = '/^[0-9]$/';
            $out = filter_var($code, FILTER_VALIDATE_REGEXP, $settings);

            if($out === false){
		return 'Invalid Code';
	    }
	    $i++;
	}

	/* Confirm that given codes match the ones temporarily
	 * generated for the user to recover his/her password */
	$validationError = '';

	return $validationError;
    }

    /**
     * @var $map Mapping of html form input names to shorter names
     */
    private array $map;

    /**
     * @var $userRaw Array used to collect client form inputs
     */
    private array $userRaw;

    /**
     * @var $userProcessed Array used to keep the validated collected inputs
     */
    private array $userProcessed;

    /**
     * @var $validationInvalid Result of user data validation
     */
    private string $validationInvalid;
}

?>
