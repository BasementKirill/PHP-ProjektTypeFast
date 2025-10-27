<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';

// Leaderboard-Daten laden
require_once '../includes/Database.php';
require_once '../includes/Results.php';

$db = (new Database())->connect();
$results = new Results($db);
$leaderboard = $results->getLeaderboard(10);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - TypeFast</title>
    <style>
        body { margin:0; font-family:Arial; background:#0f0f0f; color:#ddd; }
        .nav { display:flex; justify-content:space-between; align-items:center; padding:10px 20px; background:#111; border-bottom:1px solid #222; }
        .nav a { color:#ddd; text-decoration:none; padding:8px 12px; border-radius:6px; margin:0 4px; }
        .nav a:hover { background:#222; }
        .nav a.active { background:#2b7cff; color:#fff; }
        .content { max-width:800px; margin:20px auto; padding:20px; }
        .btn { padding:8px 16px; background:#2b7cff; color:#fff; border:none; border-radius:6px; cursor:pointer; text-decoration:none; display:inline-block; margin:4px; }
        .btn:hover { background:#1e5bb8; }
        table { width:100%; border-collapse: collapse; margin-top:20px; background:#111; border-radius:8px; overflow:hidden; }
        th, td { padding:12px; text-align:left; border-bottom:1px solid #222; }
        th { background:#222; font-weight:bold; }
        tr:hover { background:#1a1a1a; }
        .rank { font-weight:bold; color:#2b7cff; }
        .wpm { font-weight:bold; color:#51cf66; }
        .accuracy { color:#ffd43b; }
    </style>
</head>
<body>
    <nav class="nav">
        <div>
            <a href="../index.php">Home</a>
            <?php if($loggedIn): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="test.php">Test</a>
                <a href="leaderboard.php" class="active">Leaderboard</a>
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
        <h1>Leaderboard</h1>
        <p>Die besten Tippgeschwindigkeiten aller Spieler</p>
        
        <?php if(empty($leaderboard)): ?>
            <p style="color:#9ad; text-align:center; margin:40px 0;">Noch keine Ergebnisse vorhanden</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Spieler</th>
                        <th>WPM</th>
                        <th>Genauigkeit</th>
                        <th>Datum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($leaderboard as $index => $result): ?>
                        <tr>
                            <td class="rank"><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($result['username']) ?></td>
                            <td class="wpm"><?= $result['wpm'] ?></td>
                            <td class="accuracy"><?= $result['accuracy'] ?>%</td>
                            <td><?= date('d.m.Y H:i', strtotime($result['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <?php if($loggedIn): ?>
            <div style="margin-top:30px; text-align:center;">
                <a href="test.php" class="btn">Test starten</a>
                <a href="dashboard.php" class="btn">Mein Dashboard</a>
            </div>
        <?php else: ?>
            <div style="margin-top:30px; text-align:center;">
                <a href="login.php" class="btn">Anmelden und mitspielen</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
