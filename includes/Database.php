<?php
// ============================================================================
// DATABASE CONNECTION CLASS
// ============================================================================
// This class handles all database connections for the TypeFast application.
// It uses PDO (PHP Data Objects) for secure database operations.
// 
// Usage:
// $db = new Database();
// $connection = $db->connect();
// $stmt = $connection->prepare("SELECT * FROM users WHERE id = ?");
// $stmt->execute([$userId]);

class Database {
    // Database configuration - matches XAMPP default settings
    private $host = "localhost";        // XAMPP MySQL server
    private $dbname = "typefast";       // Our application database
    private $username = "root";         // XAMPP default username
    private $password = "";             // XAMPP default (no password)
    public $connection;                 // Public property to store connection

    // ============================================================================
    // CONNECT TO DATABASE
    // ============================================================================
    // Creates a new PDO connection to the MySQL database
    // Returns: PDO connection object or dies with error message
    public function connect() {
        try {
            // Create PDO connection string
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->username,
                $this->password
            );
            // Set error mode to throw exceptions (better error handling)
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->connection;
        } catch(PDOException $e) {
            // If connection fails, stop execution and show error
            die("Database error: " . $e->getMessage());
        }
    }
}
?>
