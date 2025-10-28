<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$userId = $_SESSION['user_id'];

// Benutzer-Ergebnisse laden
require_once '../includes/Database.php';
require_once '../includes/Results.php';
require_once '../includes/Coins.php';
require_once '../includes/Features.php';

$db = (new Database())->connect();
$results = new Results($db);
$userResults = $results->getByUser($userId);

// Load coins and purchased features
$coinsModel = new Coins($db);
$coins = $coinsModel->get($userId);
$featuresModel = new Features($db);
$userFeatures = $featuresModel->getUserFeatures($userId);

// Statistiken berechnen
//Berrechnung der Werte mit Hilfe von AI
$totalTests = count($userResults);
$bestWpm = $totalTests > 0 ? max(array_column($userResults, 'wpm')) : 0;
$avgWpm = $totalTests > 0 ? round(array_sum(array_column($userResults, 'wpm')) / $totalTests) : 0;
$avgAccuracy = $totalTests > 0 ? round(array_sum(array_column($userResults, 'accuracy')) / $totalTests) : 0;
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TypeFast</title>
    <link rel="stylesheet" href="../assets/common.css">
    <style>
        /* Kleine Css änderungen von der COmmon.css*/
        .stat-card { background:#111; padding:20px; border-radius:8px; border:1px solid #222; text-align:center; }
        .stat-number { font-size:2em; font-weight:bold; color:#7aa2f7; }
        .stat-label { color:#bbb; margin-top:5px; }
        .wpm { font-weight:bold; color:#9ece6a; }
        .accuracy { color:#ffd43b; }
    </style>
</head>
<body>
    <nav class="nav">
        <div>
            <a href="../index.php">Home</a>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="test.php">Test</a>
            <a href="leaderboard.php">Leaderboard</a>
            <a href="features.php">Features</a>
        </div>
        <div>
            <span class="coins">💰 Coins: <?= $coins ?? 0 ?></span>
            <span style="margin:0 10px;">Hallo, <?= htmlspecialchars($username) ?></span>
            <a href="logout.php" class="btn">Logout</a>
        </div>
    </nav>

    <div class="content">
        <h1>Mein Dashboard</h1>
        <p>Hier siehst du deine persönlichen Statistiken und Testergebnisse.</p>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= $totalTests ?></div>
                <div class="stat-label">Tests absolviert</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $bestWpm ?></div>
                <div class="stat-label">Beste WPM</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $avgWpm ?></div>
                <div class="stat-label">Durchschnitt WPM</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $avgAccuracy ?>%</div>
                <div class="stat-label">Durchschnitt Genauigkeit</div>
            </div>
        </div>
        
        <h2>Gekaufte Features</h2>
        <?php if(empty($userFeatures)): ?>
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
        
        <div style="margin:30px 0;">
            <a href="test.php" class="btn">Neuen Test starten</a>
            <a href="leaderboard.php" class="btn">Leaderboard anzeigen</a>
        </div>
        
        <h2>Meine letzten Ergebnisse</h2>
        <?php if(empty($userResults)): ?>
            <p style="color:#9ad; text-align:center; margin:40px 0;">Noch keine Tests absolviert</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>WPM</th>
                        <th>Genauigkeit</th>
                        <th>Datum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(array_slice($userResults, 0, 10) as $result): ?>
                        <tr>
                            <td class="wpm"><?= $result['wpm'] ?></td>
                            <td class="accuracy"><?= $result['accuracy'] ?>%</td>
                            <td><?= date('d.m.Y H:i', strtotime($result['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
