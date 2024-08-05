FROM php

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    procps \
    && docker-php-ext-install sockets zip mysqli \
    && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /tmp/composer-setup.php

WORKDIR /Billing

RUN mkdir -p /Billing/logs && chmod -R 777 /Billing/logs
COPY . /Billing

RUN composer require php-amqplib/php-amqplib
RUN composer install

CMD ["sh", "-c", "php ./app/index.php"]
