-- Filename: init.sql

-- Check if the database exists
SELECT SCHEMA_NAME
FROM INFORMATION_SCHEMA.SCHEMATA
WHERE SCHEMA_NAME = ${MYSQL_DATABASE};

-- If the database does not exist, create it and source the script
SET @db_exists = FOUND_ROWS();

IF @db_exists = 0 THEN
    CREATE DATABASE IF NOT EXISTS ${MYSQL_DATABASE};
    USE ${MYSQL_DATABASE};

    -- Source your SQL script
    source ${PARENT_DIR}/schema/schema.sql;
END IF;

