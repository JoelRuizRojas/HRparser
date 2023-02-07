<?php

/**
 * CMS.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

namespace HRparser\CMS;                            // Namespace declaration

class CMS
{
    /**
     * @var $db Reference to Database object
     */
    protected $db        = null;                   // Reference to database object
    protected $member    = null;                   // Reference to member object
    protected $token     = null;                   // Reference to token object

    /**
     * Class constructor
     *
     * @param $dsn Data Source Name to create connection to database instance
     * @param $username Username used for database connection
     * @param $password Password used for database connection
     * @return none
     */
    public function __construct(string $dsn, string $username, string $password)
    {
        // Create Database object
        $this->db = new Database($dsn, $username, $password);
    }

    /**
     * Wrapper to get access to Member object that handles the
     * member database
     *
     * @param none
     * @return $this->member Member instance
     */
    public function getMember()
    {
        // Allocate member instance if still not allocated
        if($this->member === null){
            $this->member = new Member($this->db);   // Create member object
        }

        return $this->member;                        // Return member object
    }

    /**
     * Wrapper to get access to Token object that handles the
     * token database
     *
     * @param none
     * @return $this->token Token instance
     */
    public function getToken()
    {
        // Allocate token instance if still not allocated
        if($this->token === null){
            $this->token = new Token($this->db);     // Create token object
        }

        return $this->token;                         // Return token object
    }

}

?>
