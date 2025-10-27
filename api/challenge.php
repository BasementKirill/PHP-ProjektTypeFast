<?php
require_once "../includes/Database.php";
require_once "../includes/Session.php";
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
$coinsModel = new Coins($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $targetId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // Check coins
    $coins = $coinsModel->get($userId);
    if ($coins < 100) {
        echo json_encode(['status'=>'error', 'message'=>'Not enough coins. Need 100.']);
        exit;
    }
    
    // Spend coins
    $coinsModel->spend($userId, 100);
    
    // Get target result
    $stmt = $db->prepare("SELECT id, user_id, wpm, accuracy, created_at FROM results WHERE id = ?");
    $stmt->execute([$targetId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        echo json_encode(['status'=>'error', 'message'=>'Result not found']);
        exit;
    }
    
    echo json_encode(['status'=>'success', 'target'=>$result]);
    exit;
}

http_response_code(405);
echo json_encode(['status'=>'error','message'=>'Method not allowed']);
?>
