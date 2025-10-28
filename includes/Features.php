<?php
// This class handles user features (buyable functionality)
// Features include: error_display, progress_bar, customization, challenge_mode
//AI hilfe für userFeatures bekommen und zugreifbare features

class Features {
    private $db;
    private $table = 'user_features';

    public function __construct($database) {
        $this->db = $database;
    }

    // Returns array of feature names that the user has purchased
    public function getUserFeatures($userId) {
        try {
            $stmt = $this->db->prepare("SELECT feature_name FROM {$this->table} WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            // If the table doesn't exist or other DB errors occur, return empty list
            return [];
        }
    }


    // Returns true if user has purchased the specified feature
    public function hasFeature($userId, $featureName) {
        try {
            $stmt = $this->db->prepare("SELECT 1 FROM {$this->table} WHERE user_id = ? AND feature_name = ?");
            $stmt->execute([$userId, $featureName]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }


    // Adds a feature to the user's account
    public function purchaseFeature($userId, $featureName) {
        try {
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, feature_name) VALUES (?, ?)");
            $stmt->execute([$userId, $featureName]);
            return ['status' => 'success'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Feature already purchased'];
        }
    }


    // Returns array of all available features with their prices
    public function getAvailableFeatures() {
        return [
            'error_display' => [
                'name' => 'Fehleranzeige',
                'description' => 'Zeigt Fehler während des Tests an',
                'price' => 50,
                'icon' => '❌'
            ],
            'progress_bar' => [
                'name' => 'Fortschrittsbalken',
                'description' => 'Zeigt Fortschritt während des Tests',
                'price' => 75,
                'icon' => '📊'
            ],
            'customization' => [
                'name' => 'Anpassung',
                'description' => 'Farben und Schriftarten ändern',
                'price' => 100,
                'icon' => '🎨'
            ],
            'challenge_mode' => [
                'name' => '1v1 Modus',
                'description' => 'Fordere andere Spieler heraus',
                'price' => 200,
                'icon' => '⚔️'
            ]
        ];
    }
}
?>
