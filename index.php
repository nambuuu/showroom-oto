<?php
require_once __DIR__ . '/config/db.php';

if (isset($_SESSION['admin_id'])) {
    if ($_SESSION['admin_role'] === 'superadmin') {
        header('Location: admin/dashboard.php');
    } else {
        echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>";
        echo "<h2>Giao diện khách hàng đang được xây dựng...</h2>";
        echo "<p>Bạn đang đăng nhập với tài khoản khách hàng.</p>";
        echo "<a href='logout.php' style='padding:10px 20px; background:#ef4444; color:#fff; text-decoration:none; border-radius:5px;'>Đăng xuất</a>";
        echo "</div>";
    }
    exit;
} else {
    header('Location: login.php');
    exit;
}
?>
