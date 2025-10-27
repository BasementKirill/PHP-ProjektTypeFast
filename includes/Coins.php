<?php
class Coins {
    private $db;
    private $table = 'user_coins';

    public function __construct($database) {
        $this->db = $database;
    }

    public function get($userId) {
        $stmt = $this->db->prepare("SELECT coins FROM {$this->table} WHERE user_id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['coins'] : 0;
    }

    public function add($userId, $amount) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, coins) VALUES (?, ?) ON DUPLICATE KEY UPDATE coins = coins + ?");
        $stmt->execute([$userId, $amount, $amount]);
    }

    public function spend($userId, $amount) {
        $currentCoins = $this->get($userId);
        if ($currentCoins >= $amount) {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET coins = coins - ? WHERE user_id = ?");
            $stmt->execute([$amount, $userId]);
            return true;
        }
        return false;
    }
}
?>