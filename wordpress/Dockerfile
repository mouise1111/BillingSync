# Use the official WordPress image as a base
FROM wordpress:latest

# Install dependencies for Composer and PHP AMQP - EersteZit
RUN apt-get update && apt-get install -y \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install Composer - EersteZit
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# Copy the custom plugin directory to the WordPress plugins directory
COPY ./wp-content/plugins/  ./wp-content/plugins/

# Install Composer dependencies for the plugin
RUN composer require php-amqplib/php-amqplib # from EersteZit
RUN composer install --working-dir=/var/www/html/wp-content/plugins/

# Ensure correct permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html
# Expose port 80 for the web server
EXPOSE 80

