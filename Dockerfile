# Use the official PHP image with PHP-FPM (FastCGI Process Manager)
FROM php:8.1-fpm-alpine

# Install necessary PHP extensions
RUN docker-php-ext-install pdo pdo_mysql sockets

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html

# Copy the application code into the container
COPY . .

# Expose port 9000 (used by PHP-FPM)
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
