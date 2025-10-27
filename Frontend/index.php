<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TypeFast</title>
    <link rel="stylesheet" href="common.css">
    <style>
        body { font-family:'Courier New', monospace; background:#1a1b26; color:#a9b1d6; }
        .nav { background:#16161e; border-bottom:1px solid #24283b; }
        .nav a { color:#a9b1d6; }
        .nav a:hover { background:#24283b; }
        .nav a.active { background:#7aa2f7; color:#1a1b26; }
        .btn { background:#7aa2f7; color:#1a1b26; }
        .btn:hover { background:#6272a4; }
    </style>
</head>
<body>
    <nav class="nav">
        <div>
            <a href="index.php" class="active">Home</a>
            <?php if($loggedIn): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="test.php">Test</a>
                <a href="leaderboard.php">Leaderboard</a>
                <a href="features.php">Features</a>
            <?php endif; ?>
        </div>
        <div>
            <?php if($loggedIn): ?>
                <span>Hallo, <?= htmlspecialchars($username) ?></span>
                <a href="logout.php" class="btn">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="content">
        <h1>TypeFast - Tippgeschwindigkeit testen</h1>
        
        <?php if($loggedIn): ?>
            <p>Willkommen zurück! Starte einen Test oder schaue dir das Leaderboard an.</p>
            <div style="margin:20px 0;">
                <a href="test.php" class="btn">Test starten</a>
                <a href="leaderboard.php" class="btn">Leaderboard anzeigen</a>
            </div>
        <?php else: ?>
            <p>Melde dich an, um deine Tippgeschwindigkeit zu testen und dich mit anderen zu messen!</p>
            <a href="login.php" class="btn">Jetzt anmelden</a>
        <?php endif; ?>
    </div>
</body>
</html>
