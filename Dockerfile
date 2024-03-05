FROM php:7.4.1-apache

USER root

WORKDIR /var/www/html

# RUN apt update && apt install -y \
#         nodejs \
#         npm \
#         libpng-dev \
#         zlib1g-dev \
#         libxml2-dev \
#         libzip-dev \
#         libonig-dev \
#         zip \
#         curl \
#         unzip \
#     && docker-php-ext-configure gd \
#     && docker-php-ext-install -j$(nproc) gd \
#     && docker-php-ext-install pdo_mysql \
#     && docker-php-ext-install mysqli \
#     && docker-php-ext-install zip \
#     && docker-php-source delete

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli 


COPY ./vhost.conf /etc/apache2/sites-available/000-default.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chown -R www-data:www-data /var/www/html && a2enmod rewrite