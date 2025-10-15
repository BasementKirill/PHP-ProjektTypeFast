<?php
// backend/index.php

// Optional: Session starten
session_start();

// Einfaches HTML-Layout
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TypeFast Backend</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; padding: 50px; }
        h1 { color: #4CAF50; }
        a { display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Welcome to TypeFast Backend!</h1>
    <p>The backend is working correctly.</p>

    <h2>Available API Endpoints:</h2>
    <ul>
        <li><a href="api/auth.php?action=login">Login API</a></li>
        <li><a href="api/auth.php?action=register">Register API</a></li>
        <li><a href="api/results.php">Results API</a></li>
        <li><a href="api/leaderboard.php">Leaderboard API</a></li>
    </ul>

    <p>Open your frontend in Vite to start the app.</p>
</body>
</html>
