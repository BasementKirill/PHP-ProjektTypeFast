<?php

// This class handles all user-related database operations:
// - User registration (create new accounts)
// - User login (verify credentials)
// - User data retrieval (get user info by ID)
//
// Security features:
// - Passwords are hashed using PHP's password_hash()
// - Passwords are verified using password_verify()
// - SQL injection protection via prepared statements

class User {
    private $db;                    // Database connection
    private $table = "users";       // Database table name

    // ============================================================================
    // CONSTRUCTOR - Initialize with database connection
    // ============================================================================
    public function __construct($database) {
        $this->db = $database;
    }

    // Creates a new user account with hashed password
    // Parameters: $username (string), $password (string)
    // Returns: Array with 'status' and either 'user' data or 'message' error

    public function register($username, $password) {
        // Hash the password for secure storage (never store plain text passwords!)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare SQL statement to prevent SQL injection
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (username, password) VALUES (?, ?)");
        try {
            $stmt->execute([$username, $hashedPassword]);
            $userId = (int)$this->db->lastInsertId();
            $user = $this->getById($userId);
            return ["status" => "success", "user" => $user];
        } catch (PDOException $e) {
            // Username already exists or other database error
            return ["status" => "error", "message" => "Username already exists"];
        }
    }

    // Verifies user credentials and returns user data if valid
    // Parameters: $username (string), $password (string)
    // Returns: Array with 'status' and either 'user' data or 'message' error
    public function login($username, $password) {
        // Find user by username
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password using PHP's secure password verification
        if ($user && password_verify($password, $user['password'])) {
            return ["status" => "success", "user" => $user];
        }
        return ["status" => "error", "message" => "Invalid username or password"];
    }

    // Retrieves user data by their unique ID
    // Parameters: $userId (integer)
    // Returns: User array or null if not found
    public function getById($userId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }
}
?>