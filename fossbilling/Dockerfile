FROM fossbilling/fossbilling:latest

# Install dependencies for Composer and PHP AMQP
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    vim \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# RUN docker-php-ext-install bcmath
RUN docker-php-ext-install bcmath sockets
COPY ./modules/example-module/src/ /var/www/html/modules/Customclientsync
COPY ./modules/Client/Api/Admin.php /var/www/html/modules/Client/Api/
COPY ./composer.json ./
COPY ./php.ini /usr/local/etc/php/conf.d/custom-php.ini
COPY ./load.php /var/www/html
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf \
    && sed -i 's/Listen 80/Listen 0.0.0.0:80/' /etc/apache2/ports.conf \
    && echo '<VirtualHost *:80>\n    DocumentRoot /var/www/html\n    <Directory /var/www/html>\n        AllowOverride All\n        Require all granted\n    </Directory>\n</VirtualHost>' > /etc/apache2/sites-available/000-default.conf
RUN composer install

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]

