<?php
class Results {
    private $conn;
    private $table = 'results';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create(int $userId, int $wpm, int $accuracy): array
    {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (user_id, wpm, accuracy) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$userId, $wpm, $accuracy]);
            return ['status' => 'success'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->conn->prepare("SELECT id, wpm, accuracy, created_at FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLeaderboard(int $limit = 10): array
    {
        $sql = "SELECT r.id, r.wpm, r.accuracy, r.created_at, u.username
                FROM {$this->table} r
                JOIN users u ON u.id = r.user_id
                ORDER BY r.wpm DESC, r.accuracy DESC, r.created_at DESC
                LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
