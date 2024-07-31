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

COPY ./modules/example-module/src/ /var/www/html/modules/Customclientsync
COPY ./modules/Client/Api/Admin.php /var/www/html/modules/Client/Api/

RUN composer require php-amqplib/php-amqplib:2.7.0 
RUN composer install

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]

