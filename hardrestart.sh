#!/bin/bash

# Bring down the Docker Compose services
docker-compose down

# Remove the specified Docker volumes
#docker volume rm billingsync_fossbilling
#docker volume rm billingsync_mysql
docker volume rm billingsync_wordpress_db

docker system prune -a
docker volume prune 
# Build the Docker Compose services
#docker-compose build --no-cache
docker-compose build 
# Start the Docker Compose services in detached mode
docker-compose up -d

# Print a message indicating the operations are complete
echo "Docker operations complete: services rebuilt and restarted."

