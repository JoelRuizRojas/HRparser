#!/bin/bash

clusterName=$1

docker exec ${clusterName} mkdir workspace

# Copy docker files to kind cluster to build the images 
docker cp login-server.dockerfile ${clusterName}:/workspace
docker cp login-db-server.dockerfile ${clusterName}:/workspace

# Copy the login-server workspace to kind cluster to build the image
docker cp config        ${clusterName}:/workspace
docker cp public        ${clusterName}:/workspace
docker cp src           ${clusterName}:/workspace
docker cp templates     ${clusterName}:/workspace
docker cp vendor        ${clusterName}:/workspace
docker cp var           ${clusterName}:/workspace
docker cp composer.json ${clusterName}:/workspace/composer.json
docker cp composer.lock ${clusterName}:/workspace/composer.lock
docker cp .htaccess     ${clusterName}:/workspace/.htaccess

# Copy the login-db-server workspace to kind cluster to build the image
docker exec ${clusterName} mkdir -p /workspace/dbs/login/entrypoint-initdb.d
docker cp dbs/login/entrypoint-initdb.d ${clusterName}:/workspace/dbs/login

