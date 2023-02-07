<?php

/**
 * Token.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

namespace HRparser\CMS;                              // Declare namespace

use DateTime;

class Token
{
    /**
     * @var Constant definitions for error
     *
     **/
    const TOKEN_VALID   = 0;
    const TOKEN_INVALID = 1;
    const TOKEN_EXPIRED = 2;


    /**
     * @var $db Reference to Database object
     */
    protected $db;

    /**
     * Class constructor
     *
     * @param $db Database object
     * @return none
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Create a token for a given purpose
     *
     * @param $id, Member id that will use the token
     * @param $purpose, Purpose for which the token is created
     * @param $seed, Base seed to generate token
     * @param $expires, Unix timestamp for when token expires
     * @return token string
     */
    public function create(int $id, string $purpose, string $seed = null, string $expires = null): string
    {
        // Check if a seed is provided to generate the token in a different way
        if(isset($seed)){
            // Seed provided, create a token based on it
            $arguments['token']     = text_crypt($seed, 'encrypt');
        }
        else{
            // No seed provided, create a 64 bytes long token
            $arguments['token']     = bin2hex(random_bytes(64));  // Create token
        }
        $arguments['expires']       = date("Y-m-d H:i:s", 
                                           isset($expires) ? strtotime($expires) : strtotime('+4 hours'));
        $arguments['member_id']     = $id;                        // Member id to use token
        $arguments['purpose']       = $purpose;                   // Token purpose
        
        // Build Sql query
        $sql = "INSERT INTO token (token, expires, purpose, member_id)
                VALUES (:token, :expires, :purpose, :member_id);";

        // Run query
        $this->db->runSql($sql, $arguments);

        // Return the token
        return $arguments['token'];
    }

    /**
     * Checks if token is valid (not expired and purpose
     * matches the given one)
     *
     * @param $token, Token to validate
     * @param $purpose, Purpose for which the token was created
     * @param $id, Member id for which the token was validated
     * @return either an error value or success (0)
     */
    public function validate(string $token, string $purpose, int &$id = null)
    {
        // Build Sql query
        $sql = "SELECT expires, member_id
                  FROM token
                 WHERE token = :token AND purpose = :purpose;";
              
        // Run sql query
        $token = $this->db->runSql($sql, ['token' => $token, 'purpose' => $purpose])->fetch();

        // Check if token is invalid
        if(!$token['expires']){
            return self::TOKEN_INVALID;
        }
        else{
            // Evaluate if token has not expired
            if(strtotime($token['expires']) > strtotime('now')){
                // Retrieve member id
                $id = $token['member_id'];
                return self::TOKEN_VALID;
            }
            else{

                return self::TOKEN_EXPIRED;
            }
        }
    }

    /**
     * Deletes the given token
     *
     * @param $token Token to delete
     * @return none
     */
    public function remove(string $token)
    {
        // Build Sql query
        $sql = "DELETE FROM token
                 WHERE token = :token;";

        // Execute Sql query
        $this->db->runSql($sql, ['token' => $token]);
    }

    /**
     * Deletes all tokens generated for a given member/purpose
     *
     * @param $id, Members id 
     * @param $purpose, Purpose of token
     * @return none
     */
    public function removeAll(int $id, string $purpose)
    {
        // Build Sql query
        $sql = "DELETE FROM token
                 WHERE member_id = :id
                   AND purpose = :purpose;";
        
        // Execute Sql query
        $this->db->runSql($sql, ['id' => $id, 'purpose' => $purpose]);
    }

    /**
     * Retrieves a valid token for given member/purpose description.
     *
     * If multiple tokens exist, retrieves the most recent one if
     * still valid, otherwise deletes expired tokens and creates a 
     * new one(only if $createNewTokenFlag is set).
     *
     * @param $id, Member id                                                                   (IN)
     * @param $purpose, Token purpose                                                          (IN)
     * @param $createNewTokenFlag, Flag to indicate if new token has to be created (if needed) (IN)
     * @param $seed, Base seed to generate new token (if needed)                               (IN)
     * @param $expires, Unix timestamp for when new token will expire (if needed)              (IN)
     * @return target token or false(in case no valid token found nor new one created)
     */
    public function retrieve(int $id, string $purpose, bool $createNewTokenFlag = true, string $newTokenSeed = null, string $newTokenExpires = null)
    {
        // Build Sql
        $sql = "SELECT token, expires
                  FROM token
                 WHERE member_id = :id
                   AND purpose = :purpose;";

        /* Run Sql query. 
         * It is suppossed to exist only 1 token for a given member
         * and for a given purpose. Extract all tokens and retrieve 
         * the most recent token. Delete the rest of tokens */
        $tokenArr = $this->db->runSql($sql, ['id' => $id, 'purpose' => $purpose])->fetchAll();

        // Use /src/utilities/functions.php function to get release date timestamp
        $date = getReleaseTimestamp();

        /* If there exist multiple tokens, retrieve the most recent one
         * and delete the rest (since it is expected to only have 1 per 
         * member/purpose) */
        $targetToken = null;
        foreach($tokenArr as $token){
            // Keep track of most recent token
            if(strtotime($token['expires']) > $date){
                $targetToken = $token;
                $date = $token['expires'];
            }
            else{
                // Delete the rest of tokens
                $this->remove($token['token']);
            }
        }

        /* Validate if token has still not expired otherwise, 
         * delete it and create a new token */
        if(isset($targetToken) && 
           (strtotime($targetToken['expires']) > strtotime('now'))){
            // Token is still valid, return it
            return $targetToken['token'];
        }
        else{
            // Delete expired tokent (if exists)
            if(isset($targetToken)){
                // Token expired, delete it
                $this->remove($targetToken['token']);
            }

            // If not valid tokens exist, then create a new one only if createNewTokenFlag is set
            if($createNewTokenFlag == true){
                // Create a new token and return it
                return $this->create($id, $purpose, seed: $newTokenSeed, expires: $newTokenExpires);
            }
            else{
                return false;
            }
        }
    }
}

?>
