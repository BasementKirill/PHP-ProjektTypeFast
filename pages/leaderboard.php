<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';

// Leaderboard-Daten laden
require_once '../includes/Database.php';
require_once '../includes/Results.php';
require_once '../includes/Coins.php';
require_once '../includes/Features.php';

$db = (new Database())->connect();
$results = new Results($db);
$leaderboard = $results->getLeaderboard(10);
// load coins and features for display
$coinsModel = new Coins($db);
$coins = 0;
$userFeatures = [];
if ($loggedIn) {
    $userId = $_SESSION['user_id'];
    $coins = $coinsModel->get($userId);
    $featuresModel = new Features($db);
    $userFeatures = $featuresModel->getUserFeatures($userId);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - TypeFast</title>
    <link rel="stylesheet" href="../assets/common.css">
    <style>
        /* Page tweaks */
        .content { max-width:800px; }
        .rank { font-weight:bold; color:#7aa2f7; }
        .wpm { font-weight:bold; color:#9ece6a; }
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
                <a href="features.php">Features</a>
            <?php endif; ?>
        </div>
        <div>
            <?php if($loggedIn): ?>
                <span class="coins">💰 Coins: <?= $coins ?? 0 ?></span>
                <span style="margin:0 10px;">Hallo, <?= htmlspecialchars($username) ?></span>
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
        
        <h2>Gekaufte Features</h2>
        <?php if(!$loggedIn): ?>
            <p style="color:#9ad;">Melde dich an, um Features anzuzeigen.</p>
        <?php elseif(empty($userFeatures)): ?>
            <p style="color:#9ad;">Du hast noch keine Features gekauft.</p>
        <?php else: ?>
            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px;">
                <?php foreach($userFeatures as $f): ?>
                    <div style="background:#111; padding:10px 14px; border-radius:8px; border:1px solid #222;">
                        <?= htmlspecialchars($f) ?>
                    </div>
                <?php endforeach; ?>
            </div>
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
