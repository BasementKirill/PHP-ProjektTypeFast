<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$userId = $_SESSION['user_id'];

// Benutzer-Ergebnisse laden
require_once '../backend/core/Database.php';
require_once '../backend/models/Results.php';

$db = (new Database())->connect();
$results = new Results($db);
$userResults = $results->getByUser($userId);

// Statistiken berechnen
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
    <style>
        body { margin:0; font-family:Arial; background:#0f0f0f; color:#ddd; }
        .nav { display:flex; justify-content:space-between; align-items:center; padding:10px 20px; background:#111; border-bottom:1px solid #222; }
        .nav a { color:#ddd; text-decoration:none; padding:8px 12px; border-radius:6px; margin:0 4px; }
        .nav a:hover { background:#222; }
        .nav a.active { background:#2b7cff; color:#fff; }
        .content { max-width:1000px; margin:20px auto; padding:20px; }
        .btn { padding:8px 16px; background:#2b7cff; color:#fff; border:none; border-radius:6px; cursor:pointer; text-decoration:none; display:inline-block; margin:4px; }
        .btn:hover { background:#1e5bb8; }
        .stats { display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:20px; margin:20px 0; }
        .stat-card { background:#111; padding:20px; border-radius:8px; border:1px solid #222; text-align:center; }
        .stat-number { font-size:2em; font-weight:bold; color:#2b7cff; }
        .stat-label { color:#bbb; margin-top:5px; }
        table { width:100%; border-collapse: collapse; margin-top:20px; background:#111; border-radius:8px; overflow:hidden; }
        th, td { padding:12px; text-align:left; border-bottom:1px solid #222; }
        th { background:#222; font-weight:bold; }
        tr:hover { background:#1a1a1a; }
        .wpm { font-weight:bold; color:#51cf66; }
        .accuracy { color:#ffd43b; }
    </style>
</head>
<body>
    <nav class="nav">
        <div>
            <a href="index.php">Home</a>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="test.php">Test</a>
            <a href="leaderboard.php">Leaderboard</a>
        </div>
        <div>
            <span>Hallo, <?= htmlspecialchars($username) ?></span>
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
