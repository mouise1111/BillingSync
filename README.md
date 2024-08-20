# BillingSync Project Documentation

## Table of Contents

1. [Project Overview](#project-overview)
2. [Architecture and Design](#architecture-and-design)
3. [Docker Setup](#docker-setup)
4. [Service Descriptions](#service-descriptions)
   - [FOSSBilling](#fossbilling)
   - [WordPress](#wordpress)
   - [RabbitMQ](#rabbitmq)
   - [Queue Consumer](#queue-consumer)
5. [Database Structure](#database-structure)
6. [Message Broker Integration](#message-broker-integration)
7. [API Endpoints](#api-endpoints)
8. [Installation and Setup](#installation-and-setup)
9. [Running the Application](#running-the-application)
10. [Troubleshooting and Debugging](#troubleshooting-and-debugging)
11. [Future Enhancements](#future-enhancements)

## Project Overview

**BillingSync** is a microservices-based application designed to integrate billing management between FOSSBilling and WordPress. The project leverages RabbitMQ for message brokering between the services and MySQL for data storage. The primary goal is to synchronize clients, products and orders between the two platforms, ensuring that all data remains consistent and up to date.

## Architecture and Design

BillingSync is designed with a modular architecture, where each component serves a distinct purpose:
- **FOSSBilling**: Manages clients, products, and orders through modules.
- **WordPress**: Manages clients, products, and orders through custom plugins.
- **RabbitMQ**: Acts as the message broker to facilitate communication between FOSSBilling and WordPress.
- **Queue Consumer**: Consumes messages from RabbitMQ to synchronize data between the services.

This modular architecture allows for flexibility and scalability, making it easier to maintain and extend the system.

## Docker Setup

BillingSync uses Docker to containerize the different services, making it easier to deploy and manage. The project includes a `docker-compose.yml` file to orchestrate the services. The services defined include:

- **FOSSBilling**: A custom service built from the `fossbilling` directory.
- **WordPress**: A custom service built from the `wordpress` directory.
- **RabbitMQ**: A standard RabbitMQ service with the management plugin enabled.
- **Queue Consumer**: A custom service that handles the consumption of messages from RabbitMQ.
- **MySQL**: Two instances of MySQL for FOSSBilling and WordPress respectively.

### Docker Volumes

Docker volumes are used to persist data across container restarts. The following volumes are defined:
- `fossbilling`: Stores FOSSBilling data.
- `mysql`: Stores MySQL data for FOSSBilling.
- `wordpress_db`: Stores MySQL data for WordPress.

## Service Descriptions

### FOSSBilling

- **Location**: `./fossbilling`
- **Purpose**: Manages client data, products, and orders.
- **Components**:
  - **Modules**: Custom modules for Client, Order, and Product management.
  - **API**: RESTful APIs to manage clients, orders, and products.

### WordPress

- **Location**: `./wordpress`
- **Purpose**: Extends WordPress with custom plugins to manage clients, products, and orders.
- **Components**:
  - **Plugins**:
    - `client-rest-api`: Manages client data.
    - `order-rest-api`: Manages order data.
    - `product-rest-api`: Manages product data.
  - **Frontend**: Simple interfaces for managing clients, products, and orders using HTML and JavaScript.

### RabbitMQ

- **Image**: `rabbitmq:3-management`
- **Purpose**: Facilitates communication between FOSSBilling and WordPress via message queues.

### Queue Consumer

- **Location**: `./queue-consumer`
- **Purpose**: Listens to RabbitMQ for messages and performs necessary actions to synchronize data between FOSSBilling and WordPress.
- **Components**:
  - **Message Broker**: Consumes messages from RabbitMQ and routes them to the appropriate service.

## Database Structure

The project uses MySQL for data storage. Each service has its own database, which is initialized using the provided SQL scripts:
- **WordPress**: Database initialized using `clients.sql`, `orders.sql` and `products.sql`.
- **FOSSBilling**: Database initialized using `structure.sql`.

Each database is structured to store the necessary data for its respective service, such as client information, orders, and products.

## Message Broker Integration

RabbitMQ is used to synchronize data between FOSSBilling and WordPress. The integration is handled by the Queue Consumer, which listens to specific queues and triggers actions based on the messages received.

### Example Workflow

1. A new client is created in WordPress.
2. The client data is sent to RabbitMQ.
3. The Queue Consumer picks up the message and makes a FOSSBilling API call.
4. FOSSBilling processes the data and updates its records.

## API Endpoints

### FOSSBilling API

- **Client Management**: `/clientmanager/v1/clients`
- **Order Management**: `/ordermanager/v1/orders`
- **Product Management**: `/productmanager/v1/products`

### WordPress API

- **Client Management**: `/wp-json/clientmanager/v1/clients`
- **Order Management**: `/wp-json/ordermanager/v1/orders`
- **Product Management**: `/wp-json/productmanager/v1/products`

## Installation and Setup

### Prerequisites

- Docker and Docker Compose must be installed on your system.

### Steps to Install

1. Clone the repository to your local machine.
2. Navigate to the project directory.
3. Run `docker-compose up --build` to build and start the services.
4. Access the services via the provided ports (e.g., WordPress on port 9500).

## Running the Application

Once the Docker containers are up and running, you can interact with the services as follows:
- **WordPress**: Open `http://192.168.122.79:9500` to access the WordPress site.
- **FOSSBilling**: Access FOSSBilling at `http://192.168.122.79:9500`.
- **RabbitMQ Management Console**: Access the console at `http://192.168.122.79:15673`.

### Wordpress pages

- **client page**: `http://192.168.122.79:9500/client-management/`
- **product page:** `http://192.168.122.79:9500/product-management/`
- **order page**: `http://192.168.122.79:9500/order/`

### FOSSBilling pages 

- **clients**: `http://192.168.122.79/admin/client`
- **products**: `http://192.168.122.79/admin/product`
- **orders:** `http://192.168.122.79/admin/order`

## Troubleshooting and Debugging

### Common Issues

- **Service Not Starting**: Ensure that Docker is running and that the ports defined in `docker-compose.yml` are not in use.
- **RabbitMQ Queue Issues**: Ensure that the API-KEY is correct.

## Resources used
https://github.com/Integration-Project-Team-1/Billing/blob/main/Dockerfile
https://github.com/Integration-Project-Team-1/Billing/blob/main/app/index.php
https://github.com/Integration-Project-Team-1/Billing/blob/main/app/message-broker/consumers/queue_consumer.php
https://github.com/Integration-Project-Team-1/Billing/blob/main/docker-compose.yml
