<?php
require_once __DIR__ . '/db.php';

function check_admin_login() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

function check_superadmin() {
    if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'superadmin') {
        die("Bạn không có quyền truy cập trang này.");
    }
}
?>
