<?php
// includes/topbar.php
$pageTitle = $pageTitle ?? 'Dashboard';
$adminName = $_SESSION['admin_name'] ?? 'Admin';
$initials  = strtoupper(substr($adminName, 0, 1));
?>
<header class="topbar" id="topbar">
    <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle" title="Toggle menu">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-breadcrumb">
            <a href="dashboard.php" style="color:var(--text-muted)">Admin</a>
            &nbsp;/&nbsp;
            <span><?= htmlspecialchars($pageTitle) ?></span>
        </div>
    </div>
    <div class="topbar-right">
        <div class="topbar-time" id="topbarClock"></div>
        <div class="topbar-user" title="<?= htmlspecialchars($adminName) ?>">
            <div class="t-avatar"><?= $initials ?></div>
            <span class="t-name"><?= htmlspecialchars($adminName) ?></span>
            <i class="bi bi-chevron-down" style="font-size:10px;color:var(--text-muted)"></i>
        </div>
    </div>
</header>

<script>
(function(){
    function updateClock(){
        var now = new Date();
        var h = String(now.getHours()).padStart(2,'0');
        var m = String(now.getMinutes()).padStart(2,'0');
        var s = String(now.getSeconds()).padStart(2,'0');
        var el = document.getElementById('topbarClock');
        if(el) el.textContent = h+':'+m+':'+s;
    }
    updateClock();
    setInterval(updateClock, 1000);
})();
</script>
