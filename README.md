ShortLink URL Shortening Service Documentation
Overview
ShortLink is a lightweight, production-ready URL shortening service implemented in raw PHP and MySQL. It provides /encode and /decode API endpoints to shorten and retrieve URLs, using a custom PDO-based database class and a base-62 hashing algorithm for generating unique short codes. The project adheres to object-oriented programming principles and includes a Postman collection for API testing.
Table of Contents

Features
Architecture
Prerequisites
Installation
Configuration
Running the Application
API Endpoints
Testing
Troubleshooting
Security Considerations
Extending the Service
License

Features

Encode Endpoint: Converts long URLs (e.g., https://sommalife.com/impact/) to short URLs (e.g., http://shrt.est/ZeAK).
Decode Endpoint: Retrieves the original URL from a short URL.
Custom Database Class: Uses PDO for secure MySQL interactions, avoiding third-party libraries.
Base-62 Hashing: Generates unique, 6-character short codes (supports ~56.8 million URLs).
Error Handling: Validates inputs and returns JSON error responses.
Postman Tests: Includes a collection for automated API testing.

Architecture
The project is organized for modularity and maintainability. The file structure is as follows:

config/
database.php: Database configuration (excluded from version control).
database.php.example: Template for database configuration.


classes/
Database.php: Custom PDO-based class for MySQL interactions.
UrlShortener.php: Logic for URL encoding and decoding.


public/
index.php: Front controller for routing requests.
encode.php: /encode endpoint implementation.
decode.php: /decode endpoint implementation.
.htaccess: Apache URL rewriting configuration.


tests/
ShortLinkAPI.postman_collection.json: Postman collection for API testing.


DOCUMENTATION.md: This file, detailing setup and usage.
README.md: Project overview and quick start guide.
.gitignore: Excludes sensitive files from version control.

Components:

Database.php: Handles MySQL connections and queries using PDO.
UrlShortener.php: Implements encoding/decoding with a base-62 hashing algorithm.
index.php: Routes requests to /encode or /decode.
.htaccess: Rewrites URLs to the front controller for clean API endpoints.

Prerequisites

PHP: Version 7.4 or higher
MySQL: Version 5.7 or higher
Apache: With mod_rewrite enabled
Postman: For API testing
Git: For version control
Optional: Composer for development dependencies (not used in core functionality)

Installation

Clone the Repository:git clone https://github.com/yorliabdulai/shortlink.git
cd shortlink


Set Up the Database:
Create a MySQL database:CREATE DATABASE shortlink;
USE shortlink;


Create the urls table:CREATE TABLE urls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    long_url VARCHAR(2048) NOT NULL,
    short_code VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (short_code)
);




Configure Database:
Copy the example configuration:cp config/database.php.example config/database.php


Edit config/database.php with your MySQL credentials:<?php
return [
    'host' => 'localhost',
    'dbname' => 'shortlink',
    'user' => 'your_username',
    'password' => 'your_password',
    'charset' => 'utf8mb4'
];




Set Up the Web Server:
Apache:
Configure Apache to serve the public/ directory.
Ensure mod_rewrite is enabled and AllowOverride All is set in your Apache configuration (e.g., /etc/apache2/sites-available/000-default.conf).


PHP Built-in Server (for development):cd public
php -S localhost:8000





Configuration

Database: Verify config/database.php contains valid MySQL credentials.
Base URL: The short URL prefix (http://shrt.est/) is defined in classes/UrlShortener.php. Update it for production (e.g., your domain).
File Permissions:
Restrict access to config/database.php:chmod 600 config/database.php


Ensure the public/ directory is web-accessible (e.g., chmod 755 public).


Environment Variables (optional): For production, consider using environment variables for sensitive data instead of database.php.

Running the Application

Development:cd public
php -S localhost:8000

Access the API at http://localhost:8000.
Production:
Configure Apache to serve the public/ directory.
Ensure .htaccess is processed for URL rewriting.
Use HTTPS to secure API requests.



API Endpoints
Both endpoints accept POST requests with JSON payloads and return JSON responses.
/encode

Method: POST
URL: http://<your-domain>/encode
Request Body:{
    "url": "https://sommalife.com/impact/"
}


Response (Success):{
    "short_url": "http://shrt.est/ZeAK"
}


Response (Error):{
    "error": "Invalid URL"
}

or{
    "error": "URL is required"
}


Example:curl -X POST http://localhost:8000/encode -H "Content-Type: application/json" -d '{"url": "https://sommalife.com/impact/"}'



/decode

Method: POST
URL: http://<your-domain>/decode
Request Body:{
    "url": "http://shrt.est/ZeAK"
}


Response (Success):{
    "long_url": "https://sommalife.com/impact/"
}


Response (Error):{
    "error": "Short URL not found"
}

or{
    "error": "Short URL is required"
}


Example:curl -X POST http://localhost:8000/decode -H "Content-Type: application/json" -d '{"url": "http://shrt.est/ZeAK"}'



Testing
Manual Testing

Import Postman Collection:
Open Postman and import tests/ShortLinkAPI.postman_collection.json.
Set the baseUrl environment variable to http://localhost:8000 (or your domain).


Test Endpoints:
Send a POST request to /encode with a valid URL (e.g., {"url": "https://sommalife.com/impact/"}).
Copy the short_url from the response and send it to /decode.
Verify the response contains the original URL.


Test Error Cases:
Invalid URL: {"url": "invalid-url"} → {"error": "Invalid URL"}
Missing URL: {} → {"error": "URL is required"}
Non-existent short URL: {"url": "http://shrt.est/xyz123"} → {"error": "Short URL not found"}



Automated Testing

The Postman collection includes tests:
Encode URL: Verifies 200 OK and presence of short_url or error.
Decode URL: Verifies 200 OK and presence of long_url or error.


Run the collection:
In Postman, click Run Collection and select all requests.
Review test results to ensure all pass.



Database Verification

Check the urls table:SELECT * FROM urls;


Verify that long_url, short_code, and created_at match the expected values.

Troubleshooting

Database Connection Errors:
Verify MySQL is running:systemctl status mysql


Check credentials in config/database.php.
Ensure the shortlink database and urls table exist.


404 Errors:
Confirm .htaccess is processed (AllowOverride All in Apache config).
Verify the web server points to public/.
Check the request URL (e.g., http://localhost:8000/encode).


JSON Errors:
Ensure the Content-Type: application/json header is set.
Validate JSON payload syntax (e.g., no missing quotes or commas).


CORS Issues (browser-based testing):
Add CORS headers to public/index.php:header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');


Restart the server after adding headers.


Logs:
Check PHP error logs (e.g., /var/log/php_errors.log or Apache logs).
Enable error_log('message') in PHP files for debugging.



Security Considerations

SQL Injection: Prevented using PDO prepared statements in Database.php.
Input Validation: URLs are validated with filter_var in UrlShortener.php.
Sensitive Data: config/database.php is excluded from version control via .gitignore.
Production Recommendations:
Use HTTPS to encrypt API requests.
Implement rate limiting to prevent abuse.
Restrict database user permissions to the shortlink database.
Add CSRF protection if extending to a web interface.
Regularly back up the database.



Extending the Service

Redirect Endpoint: Add a GET /:shortCode endpoint to redirect short URLs to their original URLs:// In public/index.php
if (preg_match('/^\/[a-zA-Z0-9]{6}$/', $requestUri)) {
    // Handle redirect
}


Analytics: Add a clicks column to the urls table to track usage.
Expiration: Add an expires_at column for temporary URLs.
Frontend: Develop an HTML/CSS/JS interface for user-friendly interaction.
Unit Tests: Use PHPUnit to test Database and UrlShortener classes.

License
This project is licensed under the MIT License.