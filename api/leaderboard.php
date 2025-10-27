<?php
require_once "../includes/Database.php";
require_once "../includes/Results.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$db = (new Database())->connect();
$model = new Results($db);

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$limit = max(1, min($limit, 50));

echo json_encode(['status'=>'success', 'items'=>$model->getLeaderboard($limit)]);
?>
