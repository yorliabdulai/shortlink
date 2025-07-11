<?php
class Database {
    private $conn;
    
/**
 * Constructs a new Database object and establishes a PDO connection.
 *
 * Loads database configuration from a file and attempts to connect to a MySQL
 * database using the PDO extension. If the connection fails, an error message
 * is returned in JSON format.
 *
 * @throws PDOException if the connection to the database fails.
 */

    public function __construct() {
        $config = require __DIR__ . '/../config/database.php';
        
        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
            $this->conn = new PDO($dsn, $config['user'], $config['password']);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }
    
    /**
     * Executes a SQL query and returns the PDOStatement object.
     *
     * @param string $sql The SQL query to execute.
     * @param array $params An array of parameters to be bound to the query.
     *
     * @return PDOStatement
     *
     * @throws PDOException if the query fails.
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Query failed: ' . $e->getMessage()]));
        }
    }
    
    /**
     * Retrieves the ID of the last inserted row.
     *
     * @return string The ID of the last inserted row.
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
}