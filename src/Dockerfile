FROM fossbilling/fossbilling:latest

WORKDIR /var/www/html

COPY ./modules/Customclientsync/ /var/www/html/modules

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]

