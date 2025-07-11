ShortLink URL Shortening Service
A lightweight, production-ready URL shortening service built with raw PHP and MySQL, featuring /encode and /decode API endpoints to shorten and retrieve URLs. The service uses a custom database class and a base-62 hashing algorithm to generate unique, short URLs.
Features

Encode Endpoint: Converts long URLs (e.g., https://sommalife.com/impact/) to short URLs (e.g., http://shrt.est/ZeAK).
Decode Endpoint: Retrieves the original URL from a short URL.
Custom Database Class: Uses PDO for secure MySQL interactions without third-party libraries.
Object-Oriented Design: Modular and maintainable codebase.
Postman Collection: Includes tests for API endpoints.

Getting Started
Prerequisites

PHP 7.4 or higher
MySQL 5.7 or higher
Apache with mod_rewrite enabled
Postman (for testing)

Quick Setup

Clone the Repository:git clone https://github.com/<your-username>/shortlink.git
cd shortlink


Set Up the Database:
Create a MySQL database named shortlink.
Run the SQL in DOCUMENTATION.md to create the urls table.


Configure Database:
Copy config/database.php.example to config/database.php and update with your MySQL credentials.


Run the Application:
Using PHP’s built-in server:cd public
php -S localhost:8000


Or configure Apache to point to the public/ directory.


Test the API:
Import tests/ShortLinkAPI.postman_collection.json into Postman.
Send POST requests to http://localhost:8000/encode and http://localhost:8000/decode.



Documentation
Detailed setup, usage, and testing instructions are available in DOCUMENTATION.md.
License
This project is licensed under the MIT License.