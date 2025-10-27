<?php
class Coins {
    private $conn;
    private $table = 'user_coins';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function get(int $userId) {
        $stmt = $this->conn->prepare("SELECT coins FROM {$this->table} WHERE user_id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['coins'] : 0;
    }

    public function add(int $userId, int $amount) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (user_id, coins) VALUES (?, ?) ON DUPLICATE KEY UPDATE coins = coins + ?");
        $stmt->execute([$userId, $amount, $amount]);
    }

    public function spend(int $userId, int $amount) {
        $current = $this->get($userId);
        if ($current >= $amount) {
            $stmt = $this->conn->prepare("UPDATE {$this->table} SET coins = coins - ? WHERE user_id = ?");
            $stmt->execute([$amount, $userId]);
            return true;
        }
        return false;
    }
}
?>
