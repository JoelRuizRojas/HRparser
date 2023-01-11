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
