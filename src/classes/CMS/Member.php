<?php

/**
 * Member.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

namespace HRparser\CMS;                            // Namespace declaration


class Member
{
    /**
     * @var Constants to define errors
     *
     **/
    const MEMBER_MISSING_MANDATORY_DATA = 2;
    const MEMBER_DUPLICATED_ENTRY = 1;
    const MEMBER_NO_ERROR = 0;

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
     * Retrieves a member data by id
     *
     * @param $id Id of user in member table (primary key)
     * @return $member MemberAttr instance with member info (if exist) otherwise return false
     */
    public function getById(int $id)
    {
        // Sql to get member by Id
        $sql = "SELECT m.id, m.forename, m.surname, 
                       m.email, m.country, m.joined, 
                       m.last_login, m.picture, 
                       r.name AS role_id
                  FROM member AS m
                  JOIN roles  AS r ON m.role_id = r.id
                 WHERE m.id = :id;";

        // Get member data
        $memberArr = $this->db->runSql($sql, [$id])->fetch();

        // If member is not registered return false
        if(!$memberArr){
            return false;
        }

        // Dump data into MemberAttr object
        $member = new MemberAttr($id = $memberArr['id'], $forename = $memberArr['forename'],
                                 $surname = $memberArr['surname'], $email = $memberArr['email'],
                                 $country = $memberArr['country'], $joined = $memberArr['joined'], 
                                 $last_login = $memberArr['last_login'], $picture = $memberArr['picture'], 
                                 $role_id = $memberArr['role_id']);

        // Return member data
        return $member;
    }

    /**
     * Retrieves all members data
     *
     * @param none
     * @return array with all members data
     */
    public function getAll(): array
    {
        // Sql to get all members data
        $sql = "SELECT m.id, m.forename, m.surname,
                       m.email, m.country, m.joined,
                       m.last_login, m.picture, 
                       r.name AS role_id
                  FROM member AS m
                  JOIN roles  AS r ON m.role_id = r.id;";

        // Run sql and retrieve all members data
        return $this->db->runSql($sql)->fetchAll();
    }

    /**
     * Retrieves id of member with given email
     *
     * @param $email Member email
     * @return $id Id of member (primary key)
     */
    public function getIdByEmail(string $email): int
    {
        // Sql to get id using member email
        $sql = "SELECT id
                  FROM member
                 WHERE email = :email;";

        // Run sql and retrieve id of member with given email
        return $this->db->runSql($sql, [$email])->fetchColumn();
    }

    /**
     * Signs in a member
     *
     * @param $email Member email
     * @param $password Member password
     * @return User data if authenticated, otherwise return false
     */
    public function signIn(string $email, string $input_password)
    {
        // Check if member is registered
        $sql = "SELECT m.id, m.forename, m.surname, 
                       m.email, m.country, m.password,
                       m.joined, m.last_login, m.picture,
                       r.name AS role_id
                  FROM member AS m
                  JOIN roles  AS r ON m.role_id = r.id
                 WHERE email = :email;";

        // Get member data
        $memberArr = $this->db->runSql($sql, [$email])->fetch();

        // If member is not registered return false
        if(!$memberArr){
            return false;
        }

        // Dump data into MemberAttr object
        $member = new MemberAttr($id = $memberArr['id'], $forename = $memberArr['forename'], 
                                 $surname = $memberArr['surname'], $email = $memberArr['email'],
                                 $country = $memberArr['country'], $password = $memberArr['password'],
                                 $joined = $memberArr['joined'], $last_login = $memberArr['last_login'],
                                 $picture = $memberArr['picture'], $role_id = $memberArr['role_id']);

        // Check for password match
        $authenticated = password_verify($input_password, $memberArr['password']);

        // Return member data if authentication succeeds, otherwise return false
        return ($authenticated ? $member : false);
    }

    /**
     * Signs up a user
     *
     * @param $member User data to sign up
     * @return sign up error
     */
    public function signUp(MemberAttr $member): int
    {
        // Check if mandatory attributes are provided
        if(!$member->areSignUpAttributesPopulated()){
            return self::MEMBER_MISSING_MANDATORY_DATA;
        }

        // Build the parameters array (php allows object to associative array casting)
        $memberArr = (array)$member;

        // Hash the password
        $memberArr['password'] = password_hash($memberArr['password'], PASSWORD_DEFAULT);

        // Remove unneeded elements from array since they will not be used in query
        unset($memberArr['id'], $memberArr['joined'], $memberArr['last_login'], $memberArr['picture']);

        // Try to add member
        try{
            $sql = "INSERT INTO member (forename, surname, email, country, password, role_id)
                    VALUES(:forename, :surname, :email, :country, :password, :role_id);";

            $this->db->runSql($sql, $memberArr);     // Run sql query
            return self::MEMBER_NO_ERROR;
        }
        catch(\PDOException $e){
            if($e->errorInfo[1] === 1062){
                return self::MEMBER_DUPLICATED_ENTRY;
            }
            throw $e;                                // Re-trown exception
        }
    }

    /**
     * Confirm account for given member by setting the joined field
     *
     * @param $id Id of member to confirm account
     * @return none
     */
    public function confirmAccount(int $id)
    {
        // Sql query
        $sql = "UPDATE member
                   SET joined = NOW()
                 WHERE id = :id;";

        // Execute sql query
        $this->db->runSql($sql, ['id' => $id]);
    }

    /**
     * Counts the number of members registered
     *
     * @param none
     * @return number of members
     */
    public function count(): int
    {
        // Query to get number of members
        $sql = "SELECT COUNT(id)
                FROM member;";

        // Execute query and retrieve number of members
        return $this->db->runSql($sql)->fetchColumn();
    }

    /**
     * Update member mandatory information
     *
     * @param $member User data to update
     * @return error updating the member data (if exists)
     */
    public function update(MemberAttr $member): int
    {
        /* These parameters can either not be update at all
         * or they have their own method to be updated */
        unset($member->password, $member->joined, $member->last_login, 
              $member->picture, $member->role_id);

        // Check if mandatory attributes are provided
        if(!$member->areSignUpAttributesPopulated()){
            return self::MEMBER_MISSING_MANDATORY_DATA;
        }

        // Build the parameters array (php allows object to associative array casting)
        $memberArr = (array)$member;

        // Update member
        try{
            $sql = "UPDATE member
                       SET forename = :forename, surname = :surname, email = :email,
                           country = :country
                     WHERE id = :id;";

            $this->db->runSql($sql, $memberArr);     // Run sql
            return self::MEMBER_NO_ERROR;
        }
        catch(\PDOException $e){
            if($e->errorInfo[1] == 1062){
                return self::MEMBER_DUPLICATED_ENTRY;
            }
            throw $e;                                // Re-trown exception
        }
    }

    /**
     * Get member hashed password by id
     *
     * @param $id Member id
     * @return Hashed password
     */
    public function getPasswordById(int $id)
    {
        // Build Sql query
        $sql = "SELECT password
                  FROM member
                 WHERE id = :id";

        return $this->db->runSql($sql, ['id' => $id])->fetchColumn();
    }

    /**
     * Update member password
     *
     * @param $id Member id to update password
     * @param $password Updated password
     * @return error updating the member password
     */ 
    public function updatePassword(int $id, string $password): int
    {
        // Hash the password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Build sql query
        $sql = "UPDATE member
                   SET password = :password
                 WHERE id = :id";

        // Run sql
        $this->db->runSql($sql, ['id' => $id, 'password' => $hash]);

        return self::MEMBER_NO_ERROR;
    }
}

?>
