<?php 

/**
 * SignUpUser.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

/**
 * @var $this->map Mapping of html variables to script tags
 */
$sU_map = ['name'      => 'signUpFirstName',
           'lastName'  => 'signUpLastName',
           'country'   => 'country',
           'email'     => 'signUpEmail',
           'pwd'       => 'signUpPwd',
           'confPwd'   => 'signUpConfPwd',
           'terms'     => 'signUpTerms'];


/**
 * Class SignUpUser
 * 
 * Template to create the resources to help a user on the signing
 * process into the application.
 */
class SignUpUser
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
	$this->userRaw = [$this->map['name']      => "",
                          $this->map['lastName']  => "",
                          $this->map['country']   => "",
                          $this->map['email']     => "",
                          $this->map['pwd']       => "",
                          $this->map['confPwd']   => "",
                          $this->map['terms']     => false]; 

	// Initialize array to process the user input data
	$this->userProcessed = $this->userRaw; 

        // Initialize array of errors for each of the input fields
	$this->errors[$this->map['name']]        = "";
	$this->errors[$this->map['lastName']]    = "";
	$this->errors[$this->map['country']]     = "";
	$this->errors[$this->map['email']]       = "";
	$this->errors[$this->map['pwd']]         = ['', '', ''];
	$this->errors[$this->map['confPwd']]     = "";
	$this->errors[$this->map['terms']]       = "";

	// Initialize validation result
	$this->validationInvalid = "";
    }

    /**
     * Collects the user input data
     *
     * @param $name User name                                   (IN)
     * @param $lastName User last name                          (IN)
     * @param $country User country residency                   (IN)
     * @param $email User email                                 (IN)
     * @param $pwd User sign up password                        (IN)
     * @param $confPwd User confirmation password               (IN)
     * @param $terms Status of terms and conditions agreement   (IN)
     * @return none
     */
    public function collectUserInputData($name, $lastName, $country, $email, $pwd, $confPwd, $terms)
    {
        $this->userRaw[$this->map['name']]       = $name;
	$this->userRaw[$this->map['lastName']]   = $lastName;
	$this->userRaw[$this->map['country']]    = $country;
	$this->userRaw[$this->map['email']]      = $email;
	$this->userRaw[$this->map['pwd']]        = $pwd;
	$this->userRaw[$this->map['confPwd']]    = $confPwd;
	$this->userRaw[$this->map['terms']]      = $terms;
    }

    /**
     * Validates the user data already collected
     *
     * @param $errors Array to store the validation errors      (OUT)
     * @return $validationInvalid Result of user data validation
     */
    public function validateUserInputData(&$errors): string
    {
	// Set up validation filter matrix
	$validation_filters[$this->map['name']]['filter']                 = FILTER_VALIDATE_REGEXP;
	$validation_filters[$this->map['name']]['options']['regexp']      = '/^.{3,15}$/';
	$validation_filters[$this->map['lastName']]['filter']             = FILTER_VALIDATE_REGEXP;
	$validation_filters[$this->map['lastName']]['options']['regexp']  = '/^.{3,15}$/';
	$validation_filters[$this->map['email']]['filter']                = FILTER_VALIDATE_EMAIL;
	$validation_filters[$this->map['email']]['flags']                 = FILTER_FLAG_EMAIL_UNICODE;
	$validation_filters[$this->map['terms']]['filter']                = FILTER_VALIDATE_BOOLEAN;

	// Validate the user inputed data
	$this->userProcessed = filter_var_array($this->userRaw, $validation_filters);

	// Create error messages after data validation
	$this->errors[$this->map['name']]      = $this->userProcessed[$this->map['name']] ?
                                                 '' : 'First name must be between 3 and 15 characters long';
    	$this->errors[$this->map['lastName']]  = $this->userProcessed[$this->map['lastName']] ?
                                                 '' : 'Last name must be between 3 and 15 characters long';
    	$this->errors[$this->map['country']]   = $this->userRaw[$this->map['country']] != "null" ?
                                                 '' : 'You must select a country';
	$this->errors[$this->map['email']]     = $this->userProcessed[$this->map['email']] ?
                                                 '' : 'Email format not fulfilled';
	$this->errors[$this->map['pwd']]       = $this->validateUserPassword($this->userRaw[$this->map['pwd']], $this->userProcessed[$this->map['pwd']]);
	$this->errors[$this->map['confPwd']]   = $this->userRaw[$this->map['pwd']] == $this->userRaw[$this->map['confPwd']] ?
                                                 '' : 'Given passwords do not match each other';
	$this->errors[$this->map['terms']]     = $this->userProcessed[$this->map['terms']] ?
		                                 '' : 'You must agree to the terms and conditions';
 
	// Retrieve validation errors
	$errors[$this->map['name']] = $this->errors[$this->map['name']];
	$errors[$this->map['lastName']] = $this->errors[$this->map['lastName']];
	$errors[$this->map['country']] = $this->errors[$this->map['country']];
	$errors[$this->map['email']] = $this->errors[$this->map['email']];
	$errors[$this->map['pwd']] = $this->errors[$this->map['pwd']];
	$errors[$this->map['confPwd']] = $this->errors[$this->map['confPwd']];
	$errors[$this->map['terms']] = $this->errors[$this->map['terms']];

	// Check if signUp succeded
	$this->validationInvalid = $this->implodeArrayContent($this->errors);
	return $this->validationInvalid;
    }

    /**
     * Gets sanitized user data. Not all fields are sanitized.
     *
     * @param none
     * @return $userRaw Array of user data collected with sanitization
     */
    public function getUserDataToPublish(): array
    {
	// Sanitize the fields to be published directly to client browser
	$this->userRaw[$this->map['name']]      = filter_var($this->userRaw[$this->map['name']], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$this->userRaw[$this->map['lastName']]  = filter_var($this->userRaw[$this->map['lastName']], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$this->userRaw[$this->map['email']]     = filter_var($this->userRaw[$this->map['email']], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	// If validation has suceeded, then clear all fields from raw array
	if(!$this->validationInvalid){
	    $this->userRaw[$this->map['name']]         = "";
	    $this->userRaw[$this->map['lastName']]     = "";
	    $this->userRaw[$this->map['email']]        = "";
	    $this->userRaw[$this->map['country']]      = "null";
	    $this->userRaw[$this->map['pwd']]          = "";
	    $this->userRaw[$this->map['confPwd']]      = "";
	    $this->userRaw[$this->map['terms']]        = false;
	}

	return $this->userRaw;
    }

    /**
     * Validates the collected user password
     *
     * @param $pwd Password to validate                         (IN)
     * @param $out Validated password                           (OUT)
     * @return $retVal Array of errors of password validation
     */
    public static function validateUserPassword($pwd, &$out): array
    {
	// Define errors for password validation
	$errors = ['Password must be between 8 and 15 characters long',
                   'Password must have at least 1 capital and 1 lower case',
		   'Password must have at least 1 number and 1 special character'];// [#?!@$%^&*-]'];

	// Regular expressions to validate password
	$pwdRegExp = ['/^.{8,15}$/',
                      '/(?=.*[A-Z])(?=.*[a-z])[A-Za-z]/',
                      '/(?=.*\d)(?=.*[#?!@$%^&*-])[\d#?!@$%^&*-]/'];

	// Array of errors detected on password
	$retVal = ['', '', ''];
	$errorIdx = 0;

	// Perform validation on password
	for($i = 0; $i < count($pwdRegExp); $i++){
	    $settings['options']['regexp'] = $pwdRegExp[$i];
	    $out = filter_var($pwd, FILTER_VALIDATE_REGEXP, $settings);

	    if($out === false){
		$retVal[$errorIdx] = $errors[$i];
		$errorIdx++;
	    }
	}

	return $retVal;
    }

    /**
     * Recursively implodes the array content
     *
     * @param $array Input array                      (IN)
     * @return $retVal String with imploded contents
     */
    public static function implodeArrayContent($array): string
    {
	// Check if array is empty
	$tmpArr = [];
	foreach ($array as $value){
	    array_push($tmpArr, is_array($value) ? SignUpUser::implodeArrayContent($value) : $value);
	}

        return trim(implode($tmpArr));
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
     * @var $errors Array of validation errors
     */
    private array $errors;

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
