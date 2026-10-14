FROM php:8.2-apache

# Install required packages
RUN apt-get update && \
    apt-get install -y unzip curl git zip && \
    docker-php-ext-install mysqli

# Set working directory
WORKDIR /var/www/html

# Copy all files, including hidden ones (make sure to not use `.dockerignore`)
COPY . /var/www/html

# Fix permissions (optional)
RUN chown -R www-data:www-data /var/www/html

# Expose Apache port
EXPOSE 80
