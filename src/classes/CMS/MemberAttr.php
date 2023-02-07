<?php
   
/**
 * MemberAttr.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

namespace HRparser\CMS;                            // Namespace declaration

class MemberAttr
{
    /**
     * @var Variables that reference to member database column attributes
     */
    public $id;
    public $forename;
    public $surname;
    public $email;
    public $country;
    public $password;
    public $joined;
    public $last_login;
    public $picture;
    public $role_id;

    /**
     * Class constructor
     *
     * @param none
     * @return none
     */
    public function __construct($id = null, $forename = null, $surname = null,
                                $email = null, $country = null, $password = null,
                                $joined = null, $last_login = null, $picture = null,
                                $role_id = null)
    {
        $this->id          = $id;
        $this->forename    = $forename;
        $this->surname     = $surname;
        $this->email       = $email;
        $this->country     = $country;
        $this->password    = $password;
        $this->joined      = $joined;
        $this->last_login  = $last_login;
        $this->picture     = $picture;
        $this->role_id     = $role_id;
    }

    /**
     * Checks if the mandatory attributes to signUp a member are
     * populated.
     *
     * @param none
     * @return true/false (attributes populated or not)
     */
    public function areSignUpAttributesPopulated(): bool
    {
        if(!isset($this->forename) or !isset($this->surname) or !isset($this->email) or 
           !isset($this->country) or !isset($this->password))
           return false;

        return true;
    }
}

?>
