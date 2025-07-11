ShortLink URL Shortening Service Documentation
Overview
ShortLink is a lightweight URL shortening service implemented in raw PHP and MySQL, designed to provide reliable /encode and /decode API endpoints. It uses a custom PDO-based database class and a base-62 hashing algorithm to generate unique short URLs. This project adheres to object-oriented programming principles and is optimized for production use.
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

Encode Endpoint: Converts long URLs to short URLs (e.g., https://sommalife.com/impact/ → http://shrt.est/ZeAK).
Decode Endpoint: Retrieves the original URL from a short URL.
Custom Database Class: Secure MySQL interactions using PDO, avoiding third-party libraries.
Base-62 Hashing: Generates unique, 6-character short codes (supports ~56.8 million URLs).
Error Handling: Robust validation and JSON error responses.
Postman Tests: Includes a Postman collection for automated API testing.

Architecture

Directory Structure:shortlink/
├── config/
│   └── database.php (Database configuration)
├── classes/
│   ├── Database.php (Custom PDO-based database class)
│   └── UrlShortener.php (URL encoding/decoding logic)
├── public/
│   ├── index.php (Front controller)
│   ├── encode.php (/encode endpoint)
│   ├── decode.php (/decode endpoint)
│   └── .htaccess (URL rewriting)
├── tests/
│   └── ShortLinkAPI.postman_collection.json (Postman test collection)
├── DOCUMENTATION.md
├── README.md
└── .gitignore


Components:
Database.php: Handles MySQL connections and queries using PDO.
UrlShortener.php: Implements encoding/decoding logic with a base-62 hashing algorithm.
index.php: Routes requests to /encode or /decode.
.htaccess: Rewrites URLs to the front controller.



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
Copy config/database.php.example to config/database.php:cp config/database.php.example config/database.php


Edit config/database.php with your MySQL credentials:return [
    'host' => 'localhost',
    'dbname' => 'shortlink',
    'user' => 'your_username',
    'password' => 'your_password',
    'charset' => 'utf8mb4'
];




Set Up the Web Server:
Apache:
Point Apache to the public/ directory.
Ensure mod_rewrite is enabled and AllowOverride All is set in your Apache configuration.


PHP Built-in Server (for development):cd public
php -S localhost:8000





Configuration

Database: Ensure config/database.php contains valid MySQL credentials.
Base URL: The short URL prefix (http://shrt.est/) is defined in classes/UrlShortener.php. Update it for production (e.g., your domain).
File Permissions:
Restrict access to config/database.php (e.g., chmod 600 config/database.php).
Ensure the public/ directory is web-accessible.



Running the Application

Development:cd public
php -S localhost:8000


Production:
Configure Apache to serve the public/ directory.
Ensure .htaccess is processed for URL rewriting.
Use HTTPS for security.



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

Use Postman to test the API:
Import tests/ShortLinkAPI.postman_collection.json into Postman.
Set the baseUrl environment variable to http://localhost:8000 (or your domain).
Send requests to /encode and /decode with the payloads above.
Verify responses match the expected format.



Automated Testing

The Postman collection includes tests:
Encode URL: Checks for 200 OK and either short_url or error in the response.
Decode URL: Checks for 200 OK and either long_url or error in the response.


Run the collection in Postman:
Click Run Collection and select all requests.
Review test results.



Database Verification

Check the urls table:SELECT * FROM urls;


Ensure long_url, short_code, and created_at are correct.

Troubleshooting

Database Connection Errors:
Verify MySQL is running (systemctl status mysql).
Check credentials in config/database.php.
Ensure the shortlink database and urls table exist.


404 Errors:
Confirm .htaccess is processed (AllowOverride All in Apache config).
Verify the web server points to public/.


JSON Errors:
Ensure Content-Type: application/json header is set in requests.
Validate JSON payload syntax.


CORS Issues (browser-based testing):
Add CORS headers to public/index.php:header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');




Logs:
Check PHP error logs (e.g., /var/log/php_errors.log or Apache logs).



Security Considerations

SQL Injection: Prevented using PDO prepared statements.
Input Validation: URLs are validated with filter_var.
Sensitive Data: config/database.php is excluded from version control.
Production:
Use HTTPS to secure API requests.
Implement rate limiting to prevent abuse.
Restrict database user permissions to the shortlink database.
Add CSRF protection if extending to a web interface.



Extending the Service

Redirect Endpoint: Add a GET /:shortCode endpoint to redirect short URLs to their original URLs.
Analytics: Track clicks by adding a clicks column to the urls table.
Expiration: Add an expires_at column for temporary URLs.
Frontend: Build a simple HTML interface for user interaction.
Unit Tests: Add PHPUnit tests for Database and UrlShortener classes.

License
This project is licensed under the MIT License.