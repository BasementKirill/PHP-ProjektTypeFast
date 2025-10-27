<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';
$userId = $_SESSION['user_id'] ?? 0;

require_once '../backend/core/Database.php';
require_once '../backend/models/Coins.php';
require_once '../backend/models/Results.php';

try {
    $db = (new Database())->connect();
    $coinsModel = new Coins($db);
    $resultsModel = new Results($db);
    
    $coins = $coinsModel->get($userId);
    $leaderboard = $resultsModel->getLeaderboard(100);
} catch (Exception $e) {
    $coins = 0;
    $leaderboard = [];
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Features - TypeFast</title>
    <link rel="stylesheet" href="common.css">
    <style>
        body { font-family:'Courier New', monospace; background:#1a1b26; color:#a9b1d6; }
        .nav { background:#16161e; border-bottom:1px solid #24283b; }
        .nav a { color:#a9b1d6; }
        .nav a:hover { background:#24283b; }
        .nav a.active { background:#7aa2f7; color:#1a1b26; }
        .btn { background:#7aa2f7; color:#1a1b26; }
        .btn:hover { background:#6272a4; }
        .wpm { font-weight:bold; color:#9ece6a; }
    </style>
</head>
<body>
    <nav class="nav">
        <div>
            <a href="index.php">Home</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="test.php">Test</a>
            <a href="leaderboard.php">Leaderboard</a>
            <a href="features.php" class="active">Features</a>
        </div>
        <div>
            <span class="coins">💰 Coins: <?= $coins ?></span>
            <span style="margin:0 10px;"><?= htmlspecialchars($username) ?></span>
            <a href="logout.php" class="btn">Logout</a>
        </div>
    </nav>

    <div class="content">
        <h1>1v1 Challenge</h1>
        <p>Wähle ein Ergebnis aus dem Leaderboard und versuche, es zu schlagen!</p>
        <p><strong>Kosten:</strong> 100 Coins pro Challenge</p>
        
        <?php if(!$loggedIn): ?>
            <p style="color:#f7768e;">Melde dich an, um Challenges nutzen zu können.</p>
        <?php elseif($coins < 100): ?>
            <p style="color:#f7768e;">Du hast nicht genug Coins (100 benötigt). Aktuelle Coins: <?= $coins ?></p>
        <?php endif; ?>

        <div id="challenge-area"></div>
    </div>

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        const { createApp } = Vue;
        
        createApp({
            data() {
                return {
                    leaderboard: <?= json_encode($leaderboard) ?>,
                    coins: <?= $coins ?>,
                    msg: ''
                }
            },
            computed: {
                canChallenge() {
                    return <?= $loggedIn ? 'true' : 'false' ?> && this.coins >= 100;
                }
            },
            methods: {
                async buyChallenge(targetId) {
                    if (!this.canChallenge) return;
                    this.msg = 'Kauf...';
                    
                    try {
                        const res = await axios.get('/PHP-ProjektTypeFast/backend/api/challenge.php?id=' + targetId);
                        if (res.data.status === 'success') {
                            this.coins -= 100;
                            window.location = 'test.php?challenge=' + targetId + '&wpm=' + res.data.target.wpm;
                        } else {
                            this.msg = res.data.message;
                        }
                    } catch (e) {
                        this.msg = e.response?.data?.message || 'Fehler beim Kauf';
                    }
                }
            },
            template: `
                <div>
                    <p v-if="msg" style="color:#9ad; margin:10px 0;">{{ msg }}</p>
                    
                    <h2>Leaderboard (wähle einen Gegner)</h2>
                    <table v-if="leaderboard.length" style="width:100%; border-collapse: collapse; margin-top:20px; background:#111; border-radius:8px; overflow:hidden;">
                        <thead>
                            <tr><th style="padding:12px; background:#24283b;">Rang</th><th>User</th><th>WPM</th><th>Genauigkeit</th><th>Aktion</th></tr>
                        </thead>
                        <tbody>
                            <tr v-for="(entry, idx) in leaderboard" :key="entry.id" style="border-bottom:1px solid #222;">
                                <td style="padding:12px;">{{ idx + 1 }}</td>
                                <td>{{ entry.username }}</td>
                                <td class="wpm">{{ entry.wpm }}</td>
                                <td>{{ entry.accuracy }}%</td>
                                <td>
                                    <button v-if="canChallenge" @click="buyChallenge(entry.id)" class="btn">Herausfordern</button>
                                    <span v-else style="color:#565f89;">-</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else style="text-align:center; color:#9ad;">Keine Ergebnisse vorhanden</p>
                </div>
            `
        }).mount('#challenge-area');
    </script>
</body>
</html>
