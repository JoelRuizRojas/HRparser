#!/bin/bash

chmod -R 777 /var/lib/mysql

PARENT_DIR="$(cd "$(dirname "$0")" && pwd )"

export PARENT_DIR=$PARENT_DIR

echo -e "\nPARENT_DIR = $PARENT_DIR\n"

# Check if the database exists
db_exists=$(mysql -u"${MYSQL_ROOT_USER}" -p"${MYSQL_ROOT_PASSWORD}" -h"${MYSQL_HOST}" -e "SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '${MYSQL_DATABASE}';" --skip-column-names)

if [ "$db_exists" -eq 0 ]; then
    # If the database does not exist, create it
    echo "Creating database ${MYSQL_DATABASE}..."
    mysql -u"${MYSQL_ROOT_USER}" -p"${MYSQL_ROOT_PASSWORD}" -h"${MYSQL_HOST}" -e "CREATE DATABASE ${MYSQL_DATABASE};"
else
    echo "Database ${MYSQL_DATABASE} already exists."
fi

# Check if tables exist in the database
tables_exist=$(mysql -h "${MYSQL_HOST}" -u "${MYSQL_ROOT_USER}" -p"${MYSQL_ROOT_PASSWORD}" "${MYSQL_DATABASE}" -e "SHOW TABLES LIKE 'member';" | wc -l)

if [ "$tables_exist" -eq 0 ]; then
    # Import schema from SQL file
    echo "Importing schema from $SCHEMA_FILE..."
    mysql -h "${MYSQL_HOST}" -u "${MYSQL_ROOT_USER}" -p"${MYSQL_ROOT_PASSWORD}" "${MYSQL_DATABASE}" < "${PARENT_DIR}/schema/schema.sql"
    echo "Schema imported successfully."
else
    echo "Tables exist in the database. No need to import schema."
fi
