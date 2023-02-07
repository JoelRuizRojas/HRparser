<?php

/**
 * SignInUser.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

namespace HRparser\SignInUpUser;                   // Namespace declaration

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
     * @param $errors Array to store the validation errors      (OUT)
     * @return $validationInvalid Result of user data validation
     */
    public function validateUserInputData(&$errors): string
    {
        // Validate the user inputed data
	    $this->userProcessed[$this->map['email']] = filter_var($this->userRaw[$this->map['email']], FILTER_VALIDATE_EMAIL);

        // Create error messages after data validation
	    $this->errors[$this->map['email']]  = $this->userProcessed[$this->map['email']] ?
                                              '' : 'Email format not fulfilled';
        $this->errors[$this->map['pwd']]    = $this->validateUserPassword($this->userRaw[$this->map['pwd']], $this->userProcessed[$this->map['pwd']]);

        // Retrieve validation errors
        $errors[$this->map['email']] = $this->errors[$this->map['email']];
        $errors[$this->map['pwd']] = $this->errors[$this->map['pwd']];

	    // Check if validation succeeded
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
        $this->userRaw[$this->map['email']] = filter_var($this->userRaw[$this->map['email']], 
                                                         FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        return $this->userRaw;
    }

    /**
     * Validates the collected user password
     *
     * @param $pwd Password to validate                         (IN)
     * @param $out Validated password                           (OUT)
     * @return $retVal Error in password validation
     */
    public static function validateUserPassword($pwd, &$out): string
    { 
        // Regular expressions to validate password
        $pwdRegExp = ['/^.{8,15}$/',
                      '/(?=.*[A-Z])(?=.*[a-z])[A-Za-z]/',
                      '/(?=.*\d)(?=.*[#?!@$%^&*-])[\d#?!@$%^&*-]/'];

        // Error to return
        $retVal = '';

        // Perform validation on password
        for($i = 0; $i < count($pwdRegExp); $i++){
            $settings['options']['regexp'] = $pwdRegExp[$i];
            $out = filter_var($pwd, FILTER_VALIDATE_REGEXP, $settings);

            if($out === false){
                $retVal = 'Invalid password';
                break;
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
    public function implodeArrayContent($array): string
    {
        // Check if array is empty
        $tmpArr = [];
        foreach ($array as $value){
            array_push($tmpArr, is_array($value) ? SignInUser::implodeArrayContent($value) : $value);
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
