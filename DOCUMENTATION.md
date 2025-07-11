# 📘 ShortLink URL Shortening Service Documentation

## 📝 Overview

**ShortLink** is a lightweight and production-ready URL shortening service built using raw PHP and MySQL. It provides robust `/encode` and `/decode` API endpoints, with a base-62 hashing algorithm and a custom PDO-based database class. The system follows object-oriented programming (OOP) principles and is designed for modularity, extensibility, and easy deployment.

---

## 📂 Table of Contents

* [Features](#features)
* [Architecture](#architecture)
* [Prerequisites](#prerequisites)
* [Installation](#installation)
* [Configuration](#configuration)
* [Running the Application](#running-the-application)
* [API Endpoints](#api-endpoints)
* [Testing](#testing)
* [Troubleshooting](#troubleshooting)
* [Security Considerations](#security-considerations)
* [Extending the Service](#extending-the-service)
* [License](#license)

---

## ✅ Features

* 🔗 **Encode Endpoint**: Converts long URLs (e.g., `https://sommalife.com/impact/`) into short URLs (e.g., `http://shrt.est/ZeAK`).
* 🔓 **Decode Endpoint**: Resolves a short URL back to its original long form.
* 🔐 **Secure Database Class**: Built with PDO for safe, structured MySQL interactions.
* 🧮 **Base-62 Hashing**: Generates unique 6-character codes (up to \~56.8 million entries).
* ⚠️ **Error Handling**: Validates input and returns structured JSON error responses.
* 🧪 **Postman Test Suite**: Included for endpoint testing.

---

## 🧱 Architecture

### 📁 Directory Structure

```
shortlink/
├── config/
│   └── database.php (Database config)
├── classes/
│   ├── Database.php (PDO-based DB handler)
│   └── UrlShortener.php (Core logic)
├── public/
│   ├── index.php (Front controller)
│   ├── encode.php (/encode endpoint)
│   ├── decode.php (/decode endpoint)
│   └── .htaccess (Apache rewrite rules)
├── tests/
│   └── ShortLinkAPI.postman_collection.json (Postman collection)
├── README.md
├── DOCUMENTATION.md
└── .gitignore
```

### 🔧 Key Components

* `Database.php`: Handles DB connections and queries using PDO.
* `UrlShortener.php`: Encodes and decodes URLs with base-62 logic.
* `index.php`: Acts as the entry point and routes requests.
* `.htaccess`: Enables clean URLs via Apache rewrite rules.

---

## 🧰 Prerequisites

* **PHP** ≥ 7.4
* **MySQL** ≥ 5.7
* **Apache** with `mod_rewrite` enabled
* **Git** (for cloning the project)
* **Postman** (for testing APIs)
* Optional: **Composer** (for adding dev dependencies, not used in core)

---

## ⚙️ Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yorliabdulai/shortlink.git
cd shortlink
```

### 2. Set Up the Database

```sql
CREATE DATABASE shortlink;
USE shortlink;

CREATE TABLE urls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    long_url VARCHAR(2048) NOT NULL,
    short_code VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (short_code)
);
```

### 3. Configure the Database

```bash
cp config/database.php.example config/database.php
```

Edit `config/database.php`:

```php
return [
    'host' => 'localhost',
    'dbname' => 'shortlink',
    'user' => 'your_username',
    'password' => 'your_password',
    'charset' => 'utf8mb4'
];
```

### 4. Set Up the Web Server

#### Option A: Apache

* Point your Apache `DocumentRoot` to the `public/` folder.
* Enable `mod_rewrite`.
* Ensure `AllowOverride All` is enabled.

#### Option B: PHP Built-in Server (Development)

```bash
cd public
php -S localhost:8000
```

---

## ⚙️ Configuration

* **Database**: Ensure `config/database.php` has valid credentials.
* **Base URL**: Edit `UrlShortener.php` to set your domain (e.g., `http://shrt.est/`).
* **Permissions**:

  ```bash
  chmod 600 config/database.php
  chmod 755 public
  ```

---

## 🚀 Running the Application

### For Development

```bash
cd public
php -S localhost:8000
```

Visit: `http://localhost:8000`

### For Production

* Deploy the `public/` directory using Apache or Nginx.
* Use `.htaccess` for URL routing.
* Enforce HTTPS for secure API access.

---

## 📡 API Endpoints

All endpoints use `POST` with `application/json`.

### 🔸 `/encode`

**Request**

```json
{
  "url": "https://sommalife.com/impact/"
}
```

**Response (Success)**

```json
{
  "short_url": "http://shrt.est/ZeAK"
}
```

**Error Responses**

```json
{ "error": "Invalid URL" }
{ "error": "URL is required" }
```

**Example**

```bash
curl -X POST http://localhost:8000/encode \
  -H "Content-Type: application/json" \
  -d '{"url": "https://sommalife.com/impact/"}'
```

---

### 🔸 `/decode`

**Request**

```json
{
  "url": "http://shrt.est/ZeAK"
}
```

**Response (Success)**

```json
{
  "long_url": "https://sommalife.com/impact/"
}
```

**Error Responses**

```json
{ "error": "Short URL not found" }
{ "error": "Short URL is required" }
```

**Example**

```bash
curl -X POST http://localhost:8000/decode \
  -H "Content-Type: application/json" \
  -d '{"url": "http://shrt.est/ZeAK"}'
```

---

## 🧪 Testing

### ✅ Manual Testing

* Import `tests/ShortLinkAPI.postman_collection.json` into Postman.
* Set environment variable `baseUrl = http://localhost:8000`.
* Use `/encode` and `/decode` with example payloads.
* Test error responses (e.g., missing or invalid URLs).

### 🔁 Automated Testing

* The Postman collection contains automated tests:

  * Verifies status codes.
  * Checks presence of `short_url` or `long_url`.

**Run It:**

* Click **Run Collection** in Postman and review the test results.

### 📊 Database Verification

```sql
SELECT * FROM urls;
```

Ensure records are saved correctly.

---

## 🛠 Troubleshooting

### Database Errors

* Verify MySQL is running:

  ```bash
  systemctl status mysql
  ```
* Confirm DB credentials and table exist.

### 404 Errors

* Ensure `.htaccess` is enabled and processed.
* Confirm Apache points to `public/`.

### JSON Errors

* Set `Content-Type: application/json` in requests.
* Validate JSON syntax.

### CORS Issues

Add headers to `public/index.php`:

```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
```

### Logs

* Check PHP or Apache logs (e.g., `/var/log/php_errors.log`).

---

## 🔒 Security Considerations

* **SQL Injection**: Prevented with PDO prepared statements.
* **Input Validation**: URLs validated with `filter_var`.
* **Sensitive Files**: `config/database.php` excluded via `.gitignore`.

### Production Tips

* Use **HTTPS** for all requests.
* Apply **rate limiting** to prevent abuse.
* Restrict DB user to only `shortlink` database.
* Add **CSRF protection** if building a web interface.
* Schedule **automatic backups**.

---

## 🚀 Extending the Service

* 🔁 **Redirect Endpoint**: Add a `GET /:shortCode` for instant redirection.
* 📊 **Analytics**: Add a `clicks` column to track usage.
* ⏳ **Expiration**: Use `expires_at` for time-limited URLs.
* 🌐 **Frontend**: Build a web UI with HTML/CSS/JS.
* ✅ **Unit Tests**: Implement PHPUnit for logic testing.

---

## 📄 License

This project is open-sourced under the **MIT License**.
You're free to use, modify, and distribute it.
