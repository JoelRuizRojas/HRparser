# Use the official MariaDB base image
FROM mariadb:lts

LABEL author="Joel Ruiz"

# Specify the default storage engine
ENV MYSQL_DEFAULT_STORAGE_ENGINE=InnoDB

# Expose the MySQL port
EXPOSE 3306

# Install less, vim, touch and gettext package (which provides envsubst)
RUN apt-get update && \
    apt-get install -y less vim && \
    apt-get install -y procps && \
    apt-get install -y gettext-base && \
    rm -rf /var/lib/apt/lists/*

COPY dbs/login/entrypoint-initdb.d /docker-entrypoint-initdb.d

# Optionally, you can mount a volume for persistent data
#VOLUME /var/lib/mysql

# Start the MariaDB server
CMD ["mysqld"]

