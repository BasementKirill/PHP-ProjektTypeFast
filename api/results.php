<?php
require_once "../includes/Database.php";
require_once "../includes/Session.php";
require_once "../includes/Results.php";
require_once "../includes/Coins.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

Session::start();
$userId = Session::currentUserId();
if (!$userId) { http_response_code(401); echo json_encode(['status'=>'unauthenticated']); exit; }

$db = (new Database())->connect();
$model = new Results($db);
$coinsModel = new Coins($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $in = json_decode(file_get_contents('php://input'), true) ?? [];
    $wpm = (int)($in['wpm'] ?? 0);
    $accuracy = (int)($in['accuracy'] ?? 0);
    $charsTyped = (int)($in['charsTyped'] ?? 0);
    $completed = $in['completed'] ?? false;
    
    if ($wpm <= 0 || $accuracy < 0 || $accuracy > 100) {
        http_response_code(400);
        echo json_encode(['status'=>'error','message'=>'Invalid result values']);
        exit;
    }
    
    $result = $model->create($userId, $wpm, $accuracy);
    
    // Coins vergeben: 1 pro Zeichen + 10 wenn fertig
    $coinsEarned = $charsTyped + ($completed ? 10 : 0);
    $coinsModel->add($userId, $coinsEarned);
    
    // Coins wurden bereits live vergeben, nur finale Coins zurückgeben
    echo json_encode([
        'status' => 'success',
        'coinsEarned' => $coinsEarned,
        'totalCoins' => $coinsModel->get($userId)
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($in['action']) && $in['action'] === 'saveCoins') {
    // Coins während des Tests speichern
    $amount = (int)($in['amount'] ?? 0);
    if ($amount > 0) {
        $coinsModel->add($userId, $amount);
    }
    echo json_encode(['status' => 'success', 'totalCoins' => $coinsModel->get($userId)]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['status'=>'success','items'=>$model->getByUser($userId)]);
    exit;
}

http_response_code(405);
echo json_encode(['status'=>'error','message'=>'Method not allowed']);
?>
