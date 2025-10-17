<?php
require_once "../core/Database.php";
require_once "../core/Session.php";
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
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

Session::start();
$userId = Session::currentUserId();
if (!$userId) { http_response_code(401); echo json_encode(['status'=>'unauthenticated']); exit; }

$db = (new Database())->connect();
$model = new Results($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $in = json_decode(file_get_contents('php://input'), true) ?? [];
    $wpm = (int)($in['wpm'] ?? 0);
    $accuracy = (int)($in['accuracy'] ?? 0);
    if ($wpm <= 0 || $accuracy < 0 || $accuracy > 100) {
        http_response_code(400);
        echo json_encode(['status'=>'error','message'=>'Invalid result values']);
        exit;
    }
    echo json_encode($model->create($userId, $wpm, $accuracy));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['status'=>'success','items'=>$model->getByUser($userId)]);
    exit;
}

http_response_code(405);
echo json_encode(['status'=>'error','message'=>'Method not allowed']);
?>
