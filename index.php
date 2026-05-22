<?php
require_once __DIR__ . '/config/db.php';

if (isset($_SESSION['admin_id']) && $_SESSION['admin_role'] === 'superadmin') {
    header('Location: admin/dashboard.php');
    exit;
}
header('Location: login.php');
exit;
?>
