# define base php-apache image
FROM php:8.4-apache

# set container working directory
WORKDIR /var/www

# copy project files to container
# COPY . /var/www/

# install required packages
RUN apt-get update && apt-get install -y \
    libicu-dev \
    rpm
    
# install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo intl

# install OPCache extension
RUN docker-php-ext-install opcache
