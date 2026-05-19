<?php
/**
 * Logout – Hủy session và chuyển hướng về trang đăng nhập.
 */

session_start();

// Xóa toàn bộ session variables
$_SESSION = [];

// Xóa session cookie (nếu có)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Hủy session
session_destroy();

// Chuyển hướng về trang login
header('Location: login.php');
exit;
