<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
echo "<meta charset=\"utf-8\">";

if (!isset($_SESSION['user_id'])) {
    echo '<h1>Not logged in</h1>';
    echo '<p>Please log in first, then open this page again to inspect features for that user.</p>';
    echo '<p><a href="index.php">Go home</a></p>';
    exit;
}

$userId = (int)$_SESSION['user_id'];
$username = htmlspecialchars($_SESSION['username'] ?? '');

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Features.php';
require_once __DIR__ . '/includes/Coins.php';

$db = (new Database())->connect();
$featuresModel = new Features($db);
$coinsModel = new Coins($db);

$userFeaturesFromModel = $featuresModel->getUserFeatures($userId);

// Raw DB rows
try {
    $stmt = $db->prepare('SELECT * FROM user_features WHERE user_id = ?');
    $stmt->execute([$userId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $rows = ['error' => $e->getMessage()];
}

echo '<h1>Debug: Purchased Features</h1>';
echo '<p><strong>User ID:</strong> ' . $userId . ' &nbsp; <strong>Username:</strong> ' . $username . '</p>';
echo '<p><strong>Coins (model):</strong> ' . htmlspecialchars((string)$coinsModel->get($userId)) . '</p>';

echo '<h2>getUserFeatures() output</h2>';
echo '<pre>' . htmlspecialchars(json_encode($userFeaturesFromModel, JSON_PRETTY_PRINT)) . '</pre>';

echo '<h2>Raw DB rows (user_features)</h2>';
echo '<pre>' . htmlspecialchars(json_encode($rows, JSON_PRETTY_PRINT)) . '</pre>';

echo '<p>If the array above is empty but you expect rows, please ensure you inserted rows into the same database the app uses.';
echo '<br>Run this SQL in phpMyAdmin for the database configured in `includes/Database.php`:';
echo '<pre>SELECT * FROM user_features WHERE user_id = ' . $userId . ';</pre>';

echo '<p><a href="pages/dashboard.php">Open Dashboard</a> | <a href="pages/leaderboard.php">Open Leaderboard</a></p>';

?>
