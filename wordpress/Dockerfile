# Use the official WordPress image as a base
FROM wordpress:latest

# Copy the custom plugin directory to the WordPress plugins directory
COPY ./wp-content/plugins/client-rest-api /var/www/html/wp-content/plugins/client-rest-api
COPY ./wp-content/plugins/client-table /var/www/html/wp-content/plugins/client-table

# Expose port 80 for the web server
EXPOSE 80

