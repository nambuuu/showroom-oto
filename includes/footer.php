<?php
// includes/footer.php
?>
<footer style="padding:16px 28px;border-top:1px solid var(--border);margin-top:40px;text-align:center">
    <span style="font-size:12px;color:var(--text-muted)">
        &copy; <?= date('Y') ?> Showroom Ô Tô &mdash; Admin Panel &nbsp;|&nbsp;
        <span style="color:var(--gold)">v2.0</span>
    </span>
</footer>

<!-- Sidebar toggle script (global) -->
<script>
(function(){
    var sidebar  = document.getElementById('sidebar');
    var overlay  = document.getElementById('sidebarOverlay');
    var toggle   = document.getElementById('sidebarToggle');

    if(!sidebar||!toggle) return;

    function isMobile(){ return window.innerWidth < 992; }

    toggle.addEventListener('click', function(){
        if(isMobile()){
            sidebar.classList.toggle('open');
            overlay && overlay.classList.toggle('show');
        } else {
            sidebar.classList.toggle('collapsed');
            document.querySelector('.main-content') && document.querySelector('.main-content').classList.toggle('collapsed');
            document.getElementById('topbar') && document.getElementById('topbar').classList.toggle('collapsed');
        }
    });

    overlay && overlay.addEventListener('click', function(){
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    });

    /* Stat counter animation */
    document.querySelectorAll('[data-count]').forEach(function(el){
        var target = parseInt(el.getAttribute('data-count'),10);
        var duration = 1200;
        var step = target / (duration / 16);
        var current = 0;
        var timer = setInterval(function(){
            current += step;
            if(current >= target){ current = target; clearInterval(timer); }
            el.textContent = Math.floor(current).toLocaleString('vi-VN');
        }, 16);
    });
})();
</script>
