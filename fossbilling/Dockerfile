FROM fossbilling/fossbilling:latest

# Install dependencies for Composer and PHP AMQP
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# Install PHP extensions
RUN docker-php-ext-install bcmath sockets

# Copy necessary files into the container
COPY ./modules/Client/Api/Admin.php /var/www/html/modules/Client/Api/
COPY ./composer.json /var/www/html/
COPY ./php.ini /usr/local/etc/php/conf.d/custom-php.ini
COPY ./load.php /var/www/html

# Install Composer dependencies
RUN composer require php-amqplib/php-amqplib
RUN composer install

# Ensure correct permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]

