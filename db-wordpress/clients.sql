-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS testing_mouise;

-- Use the database
USE testing_mouise;

-- Create the clients table
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

