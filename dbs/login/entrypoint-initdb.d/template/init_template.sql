-- Filename: init.sql

-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS ${MYSQL_DATABASE};
USE ${MYSQL_DATABASE};

-- Create a user and grant privileges
--CREATE USER '${MYSQL_USER}'@'%' IDENTIFIED BY '${MYSQL_PASSWORD}';
--GRANT ALL PRIVILEGES ON ${MYSQL_DATABASE}.* TO '${MYSQL_USER}'@'%';
--FLUSH PRIVILEGES;

-- Load the schema from the SQL file
SOURCE ${PARENT_DIR}/schema/schema.sql;

