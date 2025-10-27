<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../includes/Database.php';
require_once '../includes/Coins.php';

$username = $_SESSION['username'];
$userId = $_SESSION['user_id'];

// Check if Challenge Mode
$challengeId = $_GET['challenge'] ?? null;
$targetWpm = $_GET['wpm'] ?? 0;

try {
    $db = (new Database())->connect();
    $coinsModel = new Coins($db);
    $coins = $coinsModel->get($userId);
} catch (Exception $e) {
    $coins = 0;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Typing Test - TypeFast</title>
    <link rel="stylesheet" href="../assets/common.css">
    <style>
        .test-area { font-size:28px; line-height:2; padding:20px; background:#16161e; border-radius:8px; }
        .char { display:inline-block; }
        .char.correct { color:#9ece6a; }
        .char.wrong { color:#f7768e; background:#2d2538; }
        .char.current { background:#394374; padding:0 2px; border-radius:2px; color:#fff; }
        .char.untouched { color:#565f89; }
        .stats { padding:10px 20px; display:flex; justify-content:space-between; font-size:14px; }
        .stat-label { color:#565f89; }
        .stat-value { color:#7aa2f7; font-weight:bold; }
        .actions { display:flex; gap:8px; justify-content:center; margin:20px 0; }
    </style>
</head>
<body>
    <nav class="nav">
        <div>
            <a href="../index.php">Home</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="test.php" class="active">Test</a>
            <a href="leaderboard.php">Leaderboard</a>
            <a href="features.php">Features</a>
        </div>
        <div>
            <span class="coins">💰 Coins: <span id="coin-display"><?= $coins ?></span></span>
            <span style="margin:0 10px;"><?= htmlspecialchars($username) ?></span>
            <a href="logout.php" class="btn">Logout</a>
        </div>
    </nav>

    <div id="typing-test"></div>

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        const { createApp } = Vue;
        
        createApp({
            data() {
                return {
                    text: '',
                    input: '',
                    timeLeft: 30,
                    running: false,
                    done: false,
                    timer: null,
                    msg: '',
                    saving: false,
                    wpm: 0,
                    accuracy: 0,
                    coins: <?= $coins ?>,
                    lastInputLength: 0,
                    challengeMode: <?= $challengeId ? 'true' : 'false' ?>,
                    targetWpm: <?= (int)$targetWpm ?>,
                    challengeId: <?= $challengeId ?? 'null' ?>
                }
            },
            computed: {
                chars() {
                    return this.text.split('').map((char, i) => {
                        if (i < this.input.length) {
                            return this.input[i] === char ? 'correct' : 'wrong';
                        }
                        if (i === this.input.length) return 'current';
                        return 'untouched';
                    });
                }
            },
            methods: {
                async loadText() {
                    try {
                        const res = await axios.get('/PHP-ProjektTypeFast/backend/api/sentence.php?words=50');
                        this.text = res.data.sentence;
                    } catch(e) {
                        this.text = 'thequickbrownfoxjumpsoverthelazydog';
                    }
                },
                async onInput(e) {
                    if (!this.running && !this.done) this.start();
                    
                    // Coins pro NEUEM Zeichen
                    const newChars = this.input.length - this.lastInputLength;
                    if (newChars > 0) {
                        this.coinsEarned += newChars;
                        this.coins += newChars;
                        document.getElementById('coin-display').textContent = this.coins;
                        
                        // Save to DB immediately
                        try {
                            await axios.post('/PHP-ProjektTypeFast/backend/api/results.php', {
                                action: 'saveCoins',
                                amount: newChars
                            });
                        } catch(e) { console.error('Coin save failed', e); }
                    }
                    this.lastInputLength = this.input.length;
                    
                    // Check ob fertig
                    if (this.input === this.text) {
                        this.finish();
                    }
                },
                start() {
                    if (this.running || this.done) return;
                    this.running = true;
                    this.msg = '';
                    this.timer = setInterval(() => {
                        this.timeLeft--;
                        if (this.timeLeft <= 0) this.finish();
                    }, 1000);
                    this.updateStats();
                },
                updateStats() {
                    if (!this.running || this.done) return;
                    const minutes = (30 - this.timeLeft) / 60;
                    const chars = this.input.length;
                    const spaces = 0; // Keine Spaces!
                    const words = chars / 5; // Schätzung
                    this.wpm = minutes > 0 ? Math.round(words / minutes) : 0;
                    
                    let correct = 0;
                    for (let i = 0; i < this.input.length; i++) {
                        if (this.input[i] === this.text[i]) correct++;
                    }
                    this.accuracy = this.input.length > 0 ? Math.round((correct / this.input.length) * 100) : 0;
                    
                    if (this.running) {
                        setTimeout(() => this.updateStats(), 100);
                    }
                },
                finish() {
                    if (this.done) return;
                    clearInterval(this.timer);
                    this.timer = null;
                    this.running = false;
                    this.done = true;
                },
                reset() {
                    clearInterval(this.timer);
                    this.timer = null;
                    this.input = '';
                    this.timeLeft = 30;
                    this.running = false;
                    this.done = false;
                    this.msg = '';
                    this.wpm = 0;
                    this.accuracy = 0;
                    this.lastInputLength = 0;
                    this.loadText();
                },
                async save() {
                    if (!this.done) return;
                    this.saving = true;
                    this.msg = '...';
                    try {
                        const completed = this.input === this.text;
                        const totalCoins = this.coinsEarned + (completed ? 10 : 0);
                        
                        const res = await axios.post('/PHP-ProjektTypeFast/backend/api/results.php', {
                            wpm: this.wpm,
                            accuracy: this.accuracy,
                            charsTyped: this.input.length,
                            completed: completed
                        });
                        
                        this.coins = res.data.totalCoins;
                        document.getElementById('coin-display').textContent = this.coins;
                        this.msg = `Gespeichert! +${totalCoins} Coins`;
                    } catch (e) {
                        this.msg = 'Speichern fehlgeschlagen';
                    } finally {
                        this.saving = false;
                    }
                }
            },
            mounted() {
                this.loadText();
            },
            template: `
                <div class="content">
                    <div v-if="challengeMode" style="background:#24283b; padding:15px; border-radius:8px; margin-bottom:20px;">
                        <h3 style="color:#9ece6a; margin:0;">⚔️ 1v1 Challenge</h3>
                        <p style="margin:5px 0; color:#a9b1d6;">Ziel: {{ targetWpm }} WPM schlagen!</p>
                    </div>

                    <div class="stats">
                        <div><span class="stat-label">Zeit:</span> <span class="stat-value">{{ timeLeft }}s</span></div>
                        <div><span class="stat-label">WPM:</span> <span class="stat-value">{{ wpm }}</span></div>
                        <div><span class="stat-label">Genauigkeit:</span> <span class="stat-value">{{ accuracy }}%</span></div>
                    </div>

                    <div class="test-area" @click="$refs.input.focus()">
                        <span v-for="(char, idx) in text" :key="idx" class="char" :class="chars[idx]">
                            {{ char }}
                        </span>
                    </div>

                    <input ref="input" v-model="input" :disabled="done" @input="onInput" 
                           style="position: absolute; opacity: 0; left: -9999px;" />

                    <div class="actions">
                        <button @click="start" :disabled="running" class="btn">Start</button>
                        <button @click="reset" class="btn">Reset</button>
                        <button @click="save" :disabled="!done || saving" class="btn">Speichern</button>
                    </div>
                    
                    <p v-if="msg" style="text-align:center; margin-top:10px; color:#9ad;">{{ msg }}</p>
                </div>
            `
        }).mount('#typing-test');
    </script>
</body>
</html>
