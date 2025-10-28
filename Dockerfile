FROM php:8.2-apache

# Enable PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Copy project files
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose port 10000
EXPOSE 10000

# Start PHP server
CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
