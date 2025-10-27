<?php
session_start();
if(isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if($_POST) {
    $action = $_POST['action'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if($username && $password) {
        require_once '../backend/core/Database.php';
        require_once '../backend/models/User.php';
        
        $db = (new Database())->connect();
        $user = new User($db);
        
        if($action === 'login') {
            $result = $user->login($username, $password);
            if($result['status'] === 'success') {
                $_SESSION['user_id'] = $result['user']['id'];
                $_SESSION['username'] = $result['user']['username'];
                header('Location: index.php');
                exit;
            } else {
                $error = 'Ungültige Anmeldedaten';
            }
        } elseif($action === 'register') {
            $result = $user->register($username, $password);
            if($result['status'] === 'success') {
                $_SESSION['user_id'] = $result['user']['id'];
                $_SESSION['username'] = $result['user']['username'];
                header('Location: index.php');
                exit;
            } else {
                $error = 'Registrierung fehlgeschlagen: ' . ($result['message'] ?? 'Unbekannter Fehler');
            }
        }
    } else {
        $error = 'Bitte alle Felder ausfüllen';
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TypeFast</title>
    <style>
        body { margin:0; font-family:Arial; background:#0f0f0f; color:#ddd; }
        .nav { display:flex; justify-content:space-between; align-items:center; padding:10px 20px; background:#111; border-bottom:1px solid #222; }
        .nav a { color:#ddd; text-decoration:none; padding:8px 12px; border-radius:6px; }
        .content { max-width:400px; margin:40px auto; padding:20px; }
        .form { background:#111; padding:20px; border-radius:8px; border:1px solid #222; }
        .tabs { display:flex; margin-bottom:20px; }
        .tab { flex:1; padding:10px; text-align:center; background:#222; color:#bbb; cursor:pointer; border:1px solid #333; }
        .tab.active { background:#2b7cff; color:#fff; }
        .tab:first-child { border-radius:6px 0 0 6px; }
        .tab:last-child { border-radius:0 6px 6px 0; }
        input { width:100%; padding:10px; margin:8px 0; border-radius:6px; border:1px solid #333; background:#0f0f0f; color:#ddd; }
        button { width:100%; padding:12px; background:#2b7cff; color:#fff; border:none; border-radius:6px; cursor:pointer; margin-top:10px; }
        button:hover { background:#1e5bb8; }
        .error { color:#ff6b6b; margin:10px 0; }
        .success { color:#51cf66; margin:10px 0; }
    </style>
</head>
<body>
    <nav class="nav">
        <div>
            <a href="index.php">← Zurück zur Startseite</a>
        </div>
    </nav>

    <div class="content">
        <div class="form">
            <div class="tabs">
                <div class="tab active" onclick="switchTab('login')">Login</div>
                <div class="tab" onclick="switchTab('register')">Registrieren</div>
            </div>
            
            <?php if($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" id="loginForm">
                <input type="hidden" name="action" value="login">
                <input type="text" name="username" placeholder="Benutzername" required>
                <input type="password" name="password" placeholder="Passwort" required>
                <button type="submit">Anmelden</button>
            </form>
            
            <form method="POST" id="registerForm" style="display:none;">
                <input type="hidden" name="action" value="register">
                <input type="text" name="username" placeholder="Benutzername" required>
                <input type="password" name="password" placeholder="Passwort" required>
                <button type="submit">Registrieren</button>
            </form>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            const tabs = document.querySelectorAll('.tab');
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            
            tabs.forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
            
            if(tab === 'login') {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
            } else {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
            }
        }
    </script>
</body>
</html>
