<?php
require_once "../core/Database.php";
require_once "../models/Results.php";

header('Content-Type: application/json');
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (strpos($origin, 'http://localhost') === 0 || strpos($origin, 'https://localhost') === 0) {
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    header('Access-Control-Allow-Origin: http://localhost:5173');
}
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$db = (new Database())->connect();
$model = new Results($db);

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$limit = max(1, min($limit, 50));

echo json_encode(['status'=>'success', 'items'=>$model->getLeaderboard($limit)]);
?>
