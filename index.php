<?php
// ============================================================================
// MAIN INDEX PAGE - Entry point for TypeFast application
// ============================================================================
// This file serves as the homepage and handles:
// 1. Session management (check if user is logged in)
// 2. Navigation display (different menus for logged in/out users)
// 3. Main content area with welcome message

session_start();
$loggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';
$coins = 0;
if ($loggedIn) {
    require_once __DIR__ . '/includes/Database.php';
    require_once __DIR__ . '/includes/Coins.php';
    try {
        $db = (new Database())->connect();
        $coinsModel = new Coins($db);
        $coins = $coinsModel->get($_SESSION['user_id']);
    } catch (Exception $e) {
        $coins = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TypeFast</title>
    <link rel="stylesheet" href="assets/common.css">
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
    <!-- ============================================================================
         NAVIGATION BAR - Shows different options based on login status
         ============================================================================ -->
    <nav class="nav">
        <div>
            <!-- Main navigation - always visible -->
            <a href="index.php" class="active">Home</a>
            <?php if($loggedIn): ?>
                <!-- Additional pages only visible when logged in -->
                <a href="dashboard.php">Dashboard</a>
                <a href="test.php">Test</a>
                <a href="leaderboard.php">Leaderboard</a>
                <a href="features.php">Features</a>
            <?php endif; ?>
        </div>
        <div>
            <?php if($loggedIn): ?>
                <!-- User info and logout for logged in users -->
                <span>Hallo, <?= htmlspecialchars($username) ?></span>
                <a href="logout.php" class="btn">Logout</a>
            <?php else: ?>
                <!-- Login button for guests -->
                <a href="login.php" class="btn">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- ============================================================================
         MAIN CONTENT AREA - Different content based on login status
         ============================================================================ -->
    <div class="content">
        <h1>TypeFast - Tippgeschwindigkeit testen</h1>
        
        <?php if($loggedIn): ?>
            <!-- Content for logged in users -->
            <p>Willkommen zurück! Starte einen Test oder schaue dir das Leaderboard an.</p>
            <div style="margin:20px 0;">
                <a href="test.php" class="btn">Test starten</a>
                <a href="leaderboard.php" class="btn">Leaderboard anzeigen</a>
            </div>
        <?php else: ?>
            <!-- Content for guests -->
            <p>Melde dich an, um deine Tippgeschwindigkeit zu testen und dich mit anderen zu messen!</p>
            <a href="login.php" class="btn">Jetzt anmelden</a>
        <?php endif; ?>
    </div>
</body>
</html>
