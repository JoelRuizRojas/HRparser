<?php

/**
 * Database.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

namespace HRparser\CMS;                       // Namespace declaration

class Database extends \PDO
{
    /**
     * Class constructor
     *
     * @param $dsn Data Source Name to create connection to database instance
     * @param $username Username used for database connection
     * @param $password Password used for database connection
     * @return none
     */
    public function __construct(string $dsn, string $username, string $password, array $options = [])
    {
        // Retrieve each row of data as an associative array
        $default_options[\PDO::ATTR_DEFAULT_FETCH_MODE] = \PDO::FETCH_ASSOC;

        // To ensure data is returned using the correct data type
        $default_options[\PDO::ATTR_EMULATE_PREPARES]   = false;

        // Create an exceptionif an problem is encountered
        $default_options[\PDO::ATTR_ERRMODE]            = \PDO::ERRMODE_EXCEPTION;

        // Replace defaults if provided
        $options = array_replace($default_options, $options);
        
        // Create PHP Data Object (PDO)
        parent::__construct($dsn, $username, $password, $options);
    }
    
    /**
     * Executes an SQL query
     *
     * @param $sql Query to execute
     * @param $arguments Arguments to be used in query
     * @return $statement PDOStatement object
     */
    public function runSql(string $sql, $arguments = null)
    {
        if(!$arguments)                       // If no arguments
            return $this->query($sql);        // Build query and return PDOStatement object

        $statement = $this->prepare($sql);    // Build query
        $statement->execute($arguments);      // Replace arguments in query and execute PDO statement

        return $statement;                    // Return PDO statement object
    }

}

?>
