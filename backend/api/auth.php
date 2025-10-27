<?php
require_once "../core/Database.php";
require_once "../core/Session.php";
require_once "../models/User.php";

// CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

Session::start();

$db = (new Database())->connect();
$userModel = new User($db);
$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true) ?? [];

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';
    $result = $userModel->login($username, $password);
    if ($result['status'] === 'success') {
        Session::loginUser($result['user']);
        $user = $result['user'];
        unset($user['password']);
        echo json_encode(['status' => 'success', 'user' => $user]);
    } else {
        http_response_code(401);
        echo json_encode($result);
    }
    exit;
}

if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';
    $result = $userModel->register($username, $password);
    if ($result['status'] === 'success') {
        Session::loginUser($result['user']);
        $user = $result['user'];
        unset($user['password']);
        echo json_encode(['status' => 'success', 'user' => $user]);
    } else {
        http_response_code(400);
        echo json_encode($result);
    }
    exit;
}

if ($action === 'logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    Session::logout();
    echo json_encode(['status' => 'success']);
    exit;
}

if ($action === 'me' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = Session::currentUserId();
    if ($userId) {
        $u = $userModel->getById($userId);
        if ($u) {
            unset($u['password']);
            echo json_encode(['status' => 'authenticated', 'user' => $u]);
            exit;
        }
    }
    http_response_code(401);
    echo json_encode(['status' => 'unauthenticated']);
    exit;
}

http_response_code(404);
echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
?>
