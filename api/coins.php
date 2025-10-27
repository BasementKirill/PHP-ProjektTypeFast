<?php
require_once "../includes/Database.php";
require_once "../includes/Session.php";
require_once "../includes/Coins.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

Session::start();
$userId = Session::currentUserId();
if (!$userId) { http_response_code(401); echo json_encode(['status'=>'unauthenticated']); exit; }

$db = (new Database())->connect();
$coinsModel = new Coins($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['coins' => $coinsModel->get($userId)]);
    exit;
}

http_response_code(405);
echo json_encode(['status'=>'error','message'=>'Method not allowed']);
?>
