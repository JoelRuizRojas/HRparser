#!/bin/bash

PARENT_DIR="$(cd "$(dirname "$0")" && pwd )"

export PARENT_DIR=$PARENT_DIR

# Replace variables in the template
envsubst < "$PARENT_DIR/template/init_template.sql" > $PARENT_DIR/init.sql

# Replace variables in the template
#sed -i "s|{PARENT_DIR}|$PARENT_DIR|g" "${PARENT_DIR}/init.sql"

# Execute the SQL file
mysql -h ${MYSQL_HOST} -u ${MYSQL_ROOT_USER} -p${MYSQL_ROOT_PASSWORD} < $PARENT_DIR/init.sql

