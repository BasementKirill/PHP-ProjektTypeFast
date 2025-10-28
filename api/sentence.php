<?php
// ============================================================================
// SENTENCE GENERATOR API
// ============================================================================
// Generates random sentences for typing tests
// Returns: JSON with 'sentence' field containing random words with spaces

require_once "../includes/Database.php";

header('Content-Type: application/json');
$db = (new Database())->connect();

// Get random words
$count = isset($_GET['words']) ? (int)$_GET['words'] : 10; // AI für random word benutzt
$count = max(5, min($count, 50));

$stmt = $db->query("SELECT word FROM words ORDER BY RAND() LIMIT $count");
$words = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Join words with spaces for proper typing test
$sentence = implode(' ', $words); // Add space between each word

echo json_encode(['sentence' => $sentence]);
?>
