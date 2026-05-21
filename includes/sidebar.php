<?php
// includes/sidebar.php
$current = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon"><i class="bi bi-car-front-fill"></i></div>
        <div class="logo-text">
            SHOWROOM
            <span>OTO ADMIN</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Tổng quan</div>
        <a href="dashboard.php" class="<?= $current==='dashboard.php'?'active':'' ?>">
            <i class="bi bi-grid-1x2-fill"></i>
            Dashboard
        </a>

        <div class="nav-section-label">Quản lý</div>
        <a href="cars.php" class="<?= $current==='cars.php'||$current==='cars_add.php'||$current==='cars_edit.php'?'active':'' ?>">
            <i class="bi bi-car-front-fill"></i>
            Quản lý xe
        </a>
        <a href="brands.php" class="<?= $current==='brands.php'?'active':'' ?>">
            <i class="bi bi-award-fill"></i>
            Hãng xe
        </a>
        <a href="bookings.php" class="<?= $current==='bookings.php'||$current==='booking_detail.php'?'active':'' ?>">
            <i class="bi bi-calendar2-check-fill"></i>
            Lịch lái thử
            <?php
            // Show pending badge
            if (isset($pdo)) {
                try {
                    $cnt = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();
                    if ($cnt > 0) echo '<span class="nav-badge">'.$cnt.'</span>';
                } catch(Exception $e) {}
            }
            ?>
        </a>
        <a href="contacts.php" class="<?= $current==='contacts.php'||$current==='contact_view.php'?'active':'' ?>">
            <i class="bi bi-envelope-fill"></i>
            Liên hệ
            <?php
            if (isset($pdo)) {
                try {
                    $cnt2 = $pdo->query("SELECT COUNT(*) FROM contacts WHERE is_read=0")->fetchColumn();
                    if ($cnt2 > 0) echo '<span class="nav-badge">'.$cnt2.'</span>';
                } catch(Exception $e) {}
            }
            ?>
        </a>

        <div class="nav-section-label">Hệ thống</div>
        <a href="users.php" class="<?= $current==='users.php'?'active':'' ?>">
            <i class="bi bi-shield-lock-fill"></i>
            Quản trị viên
        </a>
    </nav>

    <div class="sidebar-bottom">
        <div class="admin-info">
            <div class="admin-avatar">
                <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?>
            </div>
            <div>
                <div class="admin-name"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></div>
                <div class="admin-role"><?= ucfirst($_SESSION['admin_role'] ?? 'admin') ?></div>
            </div>
        </div>
        <a href="../logout.php" class="btn-logout">
            <i class="bi bi-box-arrow-left"></i>
            Đăng xuất
        </a>
    </div>
</aside>
