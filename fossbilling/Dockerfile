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

# Install PHP extensions
RUN docker-php-ext-install bcmath sockets

# Copy necessary files into the container
COPY ./modules/ ./modules/

# Install Composer dependencies
#RUN composer require php-amqplib/php-amqplib
RUN cd ./modules/Client/ && composer install
RUN cd ./modules/Product/ && composer install

# Ensure correct permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
