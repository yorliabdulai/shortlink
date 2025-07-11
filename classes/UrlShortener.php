<?php
require_once __DIR__ . '/Database.php';

class UrlShortener {
    private $db;
    private $baseUrl = 'http://shrt.est/';
    
    /**
     * Initializes the UrlShortener instance with a new Database instance.
     *
     * @return void
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Generates a short code (6 characters long) from a given ID using base-62 encoding.
     *
     * @param int $id The ID to be encoded.
     *
     * @return string The short code.
     */
    private function generateShortCode($id) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base = strlen($characters);
        $shortCode = '';
        
        while ($id > 0) {
            $shortCode = $characters[$id % $base] . $shortCode;
            $id = floor($id / $base);
        }
        
        return str_pad($shortCode, 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Encodes a given long URL into a shortened URL.
     *
     * Validates the provided URL and checks if it already exists in the database.
     * If it exists, returns the existing short URL. If not, inserts the URL into
     * the database, generates a unique short code, updates the record with the
     * short code, and returns the newly created short URL.
     *
     * @param string $longUrl The long URL to be shortened.
     *
     * @return array An associative array containing the short URL or an error message.
     */

    public function encode($longUrl) {
        if (!filter_var($longUrl, FILTER_VALIDATE_URL)) {
            return ['error' => 'Invalid URL'];
        }
        
        $stmt = $this->db->query('SELECT id, short_code FROM urls WHERE long_url = ?', [$longUrl]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return ['short_url' => $this->baseUrl . $result['short_code']];
        }
        
        $this->db->query('INSERT INTO urls (long_url) VALUES (?)', [$longUrl]);
        $id = $this->db->lastInsertId();
        $shortCode = $this->generateShortCode($id);
        
        $this->db->query('UPDATE urls SET short_code = ? WHERE id = ?', [$shortCode, $id]);
        
        return ['short_url' => $this->baseUrl . $shortCode];
    }
    
    /**
     * Decodes a given short URL into its original long URL.
     *
     * Extracts the short code from the provided short URL, queries the database
     * for the corresponding long URL, and returns it if found. If the short code
     * does not exist in the database, returns an error message.
     *
     * @param string $shortUrl The short URL to be decoded.
     *
     * @return array An associative array containing the long URL or an error message.
     */

    public function decode($shortUrl) {
        $shortCode = str_replace($this->baseUrl, '', $shortUrl);
        
        $stmt = $this->db->query('SELECT long_url FROM urls WHERE short_code = ?', [$shortCode]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return ['long_url' => $result['long_url']];
        }
        
        return ['error' => 'Short URL not found'];
    }
}