# 🔗 ShortLink – URL Shortening Service

**ShortLink** is a lightweight, production-ready URL shortening service built with raw PHP and MySQL. It features simple `/encode` and `/decode` API endpoints for shortening and retrieving URLs. Using a custom PDO-based database class and base-62 hashing, the system ensures secure, scalable, and efficient URL shortening. The project follows OOP principles and includes a Postman collection for testing.

---

## 📑 Table of Contents

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

* 🔐 **Encode Endpoint**: Converts long URLs (e.g., `https://example.com/long`) into short URLs (e.g., `http://shrt.est/ZeAK`).
* 📥 **Decode Endpoint**: Retrieves the original URL from a short code.
* 🛠 **Custom Database Class**: Built with PDO for secure and clean MySQL interactions.
* ⚙️ **Base-62 Hashing**: Generates unique 6-character short codes (supporting \~56.8 million entries).
* 🚫 **Robust Error Handling**: Validates input and returns meaningful JSON error messages.
* 🧪 **Postman Collection**: For quick testing of the API endpoints.

---

## 🧱 Architecture

```
shortlink/
├── config/
│   ├── database.php           # Database credentials (excluded from VCS)
│   └── database.php.example   # Example config
├── classes/
│   ├── Database.php           # PDO-based DB handler
│   └── UrlShortener.php       # Encoding/decoding logic
├── public/
│   ├── index.php              # Front controller
│   ├── encode.php             # Encode API
│   ├── decode.php             # Decode API
│   └── .htaccess              # Apache URL rewriting
├── tests/
│   └── ShortLinkAPI.postman_collection.json
├── README.md
├── DOCUMENTATION.md
└── .gitignore
```

---

## ⚙️ Prerequisites

* PHP ≥ 7.4
* MySQL ≥ 5.7
* Apache (with `mod_rewrite` enabled)
* Git (for version control)
* Postman (for testing)
* Optional: Composer (not required for core features)

---

## 🚀 Installation

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

### 3. Configure Database

```bash
cp config/database.php.example config/database.php
```

Edit `config/database.php`:

```php
<?php
return [
    'host' => 'localhost',
    'dbname' => 'shortlink',
    'user' => 'your_username',
    'password' => 'your_password',
    'charset' => 'utf8mb4'
];
```

### 4. Set Up Web Server

**Option A: Apache**

* Serve the `public/` directory.
* Ensure `mod_rewrite` is enabled.
* Set `AllowOverride All` in your Apache config.

**Option B: PHP Built-in Server (for development)**

```bash
cd public
php -S localhost:8000
```

---

## ⚙️ Configuration

* **Base URL**: Update the base short URL (`http://shrt.est/`) in `UrlShortener.php` to match your domain.
* **Permissions**:

  ```bash
  chmod 600 config/database.php
  chmod 755 public
  ```
* **Environment Variables (optional)**: Use environment variables for secure production deployment.

---

## 🔌 Running the Application

### Development

```bash
cd public
php -S localhost:8000
```

Visit: `http://localhost:8000`

### Production

* Use Apache to serve the `public/` folder.
* Enable `.htaccess`.
* Use HTTPS.

---

## 📡 API Endpoints

All endpoints accept `POST` requests with `Content-Type: application/json`.

### 🔸 /encode

**Request:**

```json
{
  "url": "https://sommalife.com/impact/"
}
```

**Response:**

```json
{
  "short_url": "http://shrt.est/ZeAK"
}
```

**Errors:**

```json
{ "error": "Invalid URL" }
{ "error": "URL is required" }
```

**Example:**

```bash
curl -X POST http://localhost:8000/encode \
  -H "Content-Type: application/json" \
  -d '{"url": "https://sommalife.com/impact/"}'
```

---

### 🔸 /decode

**Request:**

```json
{
  "url": "http://shrt.est/ZeAK"
}
```

**Response:**

```json
{
  "long_url": "https://sommalife.com/impact/"
}
```

**Errors:**

```json
{ "error": "Short URL not found" }
{ "error": "Short URL is required" }
```

**Example:**

```bash
curl -X POST http://localhost:8000/decode \
  -H "Content-Type: application/json" \
  -d '{"url": "http://shrt.est/ZeAK"}'
```

---

## 🧪 Testing

### Manual Testing with Postman

1. Import `tests/ShortLinkAPI.postman_collection.json`.
2. Set the `baseUrl` to `http://localhost:8000`.
3. Send requests to `/encode` and `/decode`.
4. Test error cases:

   * Invalid URL: `{"url": "invalid-url"}`
   * Missing URL: `{}`
   * Non-existent short code

### Automated Tests

* Run the full Postman collection.
* Validate that all tests pass.

### Database Validation

```sql
SELECT * FROM urls;
```

---

## 🛠 Troubleshooting

* **DB Errors**: Verify credentials and ensure MySQL is running.
* **404 Errors**: Ensure `.htaccess` is working and the web server points to `public/`.
* **JSON Errors**: Always set `Content-Type: application/json` and send valid JSON.
* **CORS Issues** (for browser testing):

```php
// In public/index.php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
```

* **Logs**: Check PHP/Apache logs or add `error_log()` statements for debugging.

---

## 🔒 Security Considerations

* ✅ **SQL Injection**: Mitigated with PDO prepared statements.
* ✅ **Input Validation**: URLs validated using `filter_var`.
* ✅ **Credential Safety**: Sensitive config is excluded via `.gitignore`.

### 🔐 Production Best Practices

* Use HTTPS
* Limit DB user permissions
* Rate-limit API requests
* Add CSRF protection for web UIs
* Regular backups

---

## ✨ Extending the Service

* **Redirection**: Add `GET /:shortCode` to redirect users to the original URL.
* **Analytics**: Track clicks by adding a `clicks` column.
* **Expiration**: Add `expires_at` for temporary links.
* **Frontend**: Build a basic web UI.
* **Testing**: Add PHPUnit tests for `Database` and `UrlShortener` classes.

---

## 📄 License

This project is licensed under the **MIT License**.
Feel free to use, modify, and share it.
