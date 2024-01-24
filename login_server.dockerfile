# Use the official PHP Apache base image
FROM php:8.3.0-apache

LABEL author="Joel Ruiz"

# Set working directory to the Apache document root
WORKDIR /var/www/html

# Enable the rewrite and mime modules
RUN a2enmod rewrite && \
    a2enmod mime && \
    a2enmod headers

# Set the path for the custom PHP error log file
RUN mkdir -p /var/log/php
RUN touch /var/log/php/php_errors.log
RUN chmod 755 /var/log/php/php_errors.log
RUN chown www-data:www-data /var/log/php/php_errors.log

# Configure PHP to log errors to the custom file
RUN { \
    echo 'error_reporting = E_ALL'; \
    echo 'display_errors = Off'; \
    echo 'log_errors = On'; \
    echo 'error_log = /var/log/php/php_errors.log'; \
} > /usr/local/etc/php/php.ini

# Install PDO MySQL extension
RUN docker-php-ext-install pdo_mysql

# Copy the PHP application files into the container. 
# Handled with volumes for now
#COPY . /var/www/html/

# Install less, vim, touch and other tools
RUN apt-get update && \
    apt-get install -y less vim && \
    apt-get install -y procps && \
    apt-get install -y libzip-dev && \
    docker-php-ext-install zip && \
    rm -rf /var/lib/apt/lists/*

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Twig extension
COPY composer.json /var/www/html/
RUN composer install --no-scripts --no-autoloader

# Run Composer scripts (including autoloader generation)
RUN composer dump-autoload --optimize

# Expose port 80 for web traffic
EXPOSE 80

# Start the Apache web server
CMD ["apache2-foreground"]

