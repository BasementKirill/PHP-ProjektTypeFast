<?php
class Results {
    private $db;
    private $table = 'results';

    public function __construct($database) {
        $this->db = $database;
    }

    public function create($userId, $wpm, $accuracy) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, wpm, accuracy) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$userId, $wpm, $accuracy]);
            return ['status' => 'success'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Failed to save result'];
        }
    }

    public function getByUser($userId) {
        $stmt = $this->db->prepare("SELECT id, wpm, accuracy, created_at FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLeaderboard($limit = 10) {
        $sql = "SELECT r.id, r.wpm, r.accuracy, r.created_at, u.username
                FROM {$this->table} r
                JOIN users u ON u.id = r.user_id
                ORDER BY r.wpm DESC, r.accuracy DESC, r.created_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>