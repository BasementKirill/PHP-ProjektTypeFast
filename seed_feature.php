<?php
//Voller AI genertierter TestLauf des programmes wegen redirecting problemen
// One-click seed: purchases a feature for the current logged-in user.
session_start();
header('Content-Type: text/html; charset=utf-8');
echo "<meta charset=\"utf-8\">";

if (!isset($_SESSION['user_id'])) {
    echo '<h1>Not logged in</h1>';
    echo '<p>Log in first and then open this page to add a test feature to your account.</p>';
    echo '<p><a href="index.php">Home</a></p>';
    exit;
}

$userId = (int)$_SESSION['user_id'];
$feature = $_GET['feature'] ?? 'challenge_mode';

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Features.php';

$db = (new Database())->connect();
$featuresModel = new Features($db);

$result = $featuresModel->purchaseFeature($userId, $feature);

echo '<h1>Seed Feature Result</h1>';
echo '<p>User ID: ' . $userId . ' | Feature: ' . htmlspecialchars($feature) . '</p>';
echo '<pre>' . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) . '</pre>';
echo '<p><a href="debug_features.php">Re-check debug page</a></p>';
echo '<p><a href="pages/dashboard.php">Open Dashboard</a></p>';
echo '<p><strong>Security:</strong> Remove this file after use to avoid accidental changes.</p>';

?>
