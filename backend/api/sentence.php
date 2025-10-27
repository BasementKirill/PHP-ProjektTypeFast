<?php
require_once "../core/Database.php";

header('Content-Type: application/json');
$db = (new Database())->connect();

// Hole zufällige Wörter
$count = isset($_GET['words']) ? (int)$_GET['words'] : 10;
$count = max(5, min($count, 50));

$stmt = $db->query("SELECT word FROM words ORDER BY RAND() LIMIT $count");
$words = $stmt->fetchAll(PDO::FETCH_COLUMN);
$sentence = implode('', $words); // Keine Leerzeichen!

echo json_encode(['sentence' => $sentence]);
?>
