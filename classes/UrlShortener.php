<?php
require_once __DIR__ . '/Database.php';

class UrlShortener {
    private $db;
    private $baseUrl = 'http://shrt.est/';
    
    public function __construct() {
        $this->db = new Database();
    }
    
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