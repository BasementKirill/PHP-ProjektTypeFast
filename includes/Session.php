<?php
class Session {
    public static function start() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function loginUser($user) {
        self::start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
    }

    public static function logout() {
        self::start();
        $_SESSION = [];
        session_destroy();
    }

    public static function currentUserId() {
        self::start();
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    public static function isLoggedIn() {
        self::start();
        return isset($_SESSION['user_id']);
    }
}
?>