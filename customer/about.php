<?php
// customer/about.php
require_once '../config/db.php';

$currentPage = 'about';
$navItems = [
    ['id' => 'home',    'label' => 'Trang Chủ',    'href' => 'index.php',   'icon' => 'home'],
    ['id' => 'cars',    'label' => 'Khám Phá Xe',  'href' => 'cars.php',    'icon' => 'directions_car'],
    ['id' => 'compare', 'label' => 'So Sánh',      'href' => 'compare.php', 'icon' => 'compare_arrows'],
    ['id' => 'about',   'label' => 'Giới Thiệu',  'href' => 'about.php',   'icon' => 'info'],
    ['id' => 'booking', 'label' => 'Lái Thử',     'href' => 'booking.php', 'icon' => 'event_available'],
];
?>
<!DOCTYPE html>
<html class="light" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>AutoSuperCar | Về Chúng Tôi</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-variant": "#353534",
                        "surface-tint": "#e9c176",
                        "on-primary-fixed": "#261900",
                        "on-secondary-fixed": "#1c1b1b",
                        "secondary": "#c8c6c5",
                        "inverse-surface": "#e5e2e1",
                        "tertiary-fixed-dim": "#c6c6c7",
                        "surface-container-high": "#e2e8f0",
                        "on-tertiary": "#2f3131",
                        "on-background": "#0f172a",
                        "tertiary": "#c6c6c7",
                        "on-tertiary-container": "#393b3b",
                        "background": "#ffffff",
                        "secondary-container": "#474746",
                        "primary-fixed": "#ffdea5",
                        "surface-container-lowest": "#ffffff",
                        "secondary-fixed": "#e5e2e1",
                        "primary-fixed-dim": "#e9c176",
                        "tertiary-fixed": "#e2e2e2",
                        "outline": "#9a8f80",
                        "tertiary-container": "#a4a5a5",
                        "surface-container": "#f1f5f9",
                        "on-surface": "#0f172a",
                        "on-secondary-container": "#334155",
                        "on-tertiary-fixed-variant": "#454747",
                        "on-secondary-fixed-variant": "#474746",
                        "error": "#ffb4ab",
                        "on-error": "#690005",
                        "surface-dim": "#f8fafc",
                        "on-primary": "#412d00",
                        "inverse-on-surface": "#313030",
                        "on-primary-fixed-variant": "#5d4201",
                        "surface": "#ffffff",
                        "on-primary-container": "#4e3700",
                        "surface-container-highest": "#cbd5e1",
                        "on-tertiary-fixed": "#1a1c1c",
                        "primary-container": "#c5a059",
                        "primary": "#e9c176",
                        "outline-variant": "#cbd5e1",
                        "error-container": "#93000a",
                        "on-error-container": "#ffdad6",
                        "on-secondary": "#313030",
                        "secondary-fixed-dim": "#c8c6c5",
                        "on-surface-variant": "#475569",
                        "surface-container-low": "#f8fafc",
                        "inverse-primary": "#775a19",
                        "surface-bright": "#3a3939"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                    "spacing": {
                        "gutter": "32px",
                        "container-max": "1440px",
                        "margin-mobile": "24px",
                        "unit": "8px",
                        "margin-desktop": "80px"
                    },
                    "fontFamily": {
                        "display-lg": ["Orbitron"],
                        "label-caps": ["Inter"],
                        "headline-md": ["Orbitron"],
                        "headline-sm": ["Orbitron"],
                        "body-md": ["Inter"],
                        "body-lg": ["Inter"],
                        "signature": ["Mrs Saint Delafield", "cursive"]
                    },
                    "fontSize": {
                        "label-caps": ["12px", {"lineHeight": "16px", "letterSpacing": "0.15em", "fontWeight": "600"}],
                        "headline-md": ["42px", {"lineHeight": "52px", "fontWeight": "600"}],
                        "display-lg": ["80px", {"lineHeight": "90px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "headline-sm": ["32px", {"lineHeight": "40px", "fontWeight": "500"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}]
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&family=Mrs+Saint+Delafield&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <style>
        :root {
            --nav-h: 72px;
            --subnav-h: 52px;
            --scroll-offset: calc(var(--nav-h) + var(--subnav-h) + 16px);
        }

        body { background-color: #ffffff; color: #0f172a; overflow-x: hidden; }
        body.menu-open { overflow: hidden; }

        /* Material Symbols — bắt buộc; uppercase trên parent sẽ làm icon hiện chữ "south" */
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 1.25em;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none !important;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-smoothing: antialiased;
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            user-select: none;
        }
        .material-symbols-outlined[data-weight="fill"] {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .bg-hero-pattern {
            position: fixed; top: 0; left: 0; width: 100%; height: 100vh; z-index: 0; pointer-events: none;
            background: radial-gradient(circle at 70% 30%, rgba(233, 193, 118, 0.06) 0%, rgba(255, 255, 255, 1) 65%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(233, 193, 118, 0.12);
            box-shadow: 0 24px 48px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(0, 0, 0, 0.04);
            transition: border-color 0.35s ease, transform 0.35s ease, background 0.35s ease;
        }
        .glass-card:hover {
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(233, 193, 118, 0.28);
            transform: translateY(-4px);
        }

        .nav-link {
            position: relative;
            padding: 0.5rem 0.75rem;
            border-radius: 9999px;
            transition: color 0.25s ease, background 0.25s ease;
        }
        .nav-link:hover { color: #e9c176; background: rgba(233, 193, 118, 0.08); }
        .nav-link.is-active {
            color: #e9c176;
            background: rgba(233, 193, 118, 0.12);
            box-shadow: inset 0 0 0 1px rgba(233, 193, 118, 0.2);
        }

        .btn-glow {
            background: linear-gradient(135deg, #c5a059 0%, #775a19 100%);
            position: relative; z-index: 1; overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .btn-glow:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(233, 193, 118, 0.25); }
        .btn-outline-gold {
            border: 1px solid rgba(233, 193, 118, 0.45);
            transition: all 0.3s ease;
        }
        .btn-outline-gold:hover {
            background: rgba(233, 193, 118, 0.1);
            border-color: #e9c176;
        }

        .reveal-up {
            opacity: 0; transform: translateY(32px);
            transition: opacity 0.9s cubic-bezier(0.16, 1, 0.3, 1), transform 0.9s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal-up.active { opacity: 1; transform: translateY(0); }
        .delay-100 { transition-delay: 100ms; }
        .delay-200 { transition-delay: 200ms; }
        .delay-300 { transition-delay: 300ms; }

        .hero-section {
            min-height: 100svh;
            min-height: 100vh;
            background:
                linear-gradient(180deg, rgba(255,255,255,0.8) 0%, rgba(255,255,255,0.9) 60%, #ffffff 100%),
                url('../assets/images/cars/mercedes-s-class.jpg') center center / cover no-repeat;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: calc(var(--nav-h) + 2rem) 1.5rem 0;
        }
        @media (min-width: 1024px) {
            .hero-section { background-attachment: fixed; }
        }

        .hero-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            max-width: 56rem;
        }

        .hero-scroll-hint {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            padding: 2rem 0 2.5rem;
            color: #d1c5b4;
            text-decoration: none;
            transition: color 0.25s ease;
        }
        .hero-scroll-hint:hover { color: #e9c176; }
        .hero-scroll-hint__label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }
        .hero-scroll-hint__icon {
            width: 1.5rem;
            height: 1.5rem;
            color: #e9c176;
            animation: bounce 2s infinite;
        }

        .section-pill {
            scroll-margin-top: var(--scroll-offset);
        }

        .subnav-bar {
            top: var(--nav-h);
        }

        .subnav-link {
            white-space: nowrap;
            padding: 0.625rem 1.25rem;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #d1c5b4;
            border: 1px solid transparent;
            transition: all 0.25s ease;
        }
        .subnav-link:hover, .subnav-link.is-active {
            color: #e9c176;
            border-color: rgba(233, 193, 118, 0.35);
            background: rgba(233, 193, 118, 0.08);
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        .subnav-scroll {
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .subnav-scroll::-webkit-scrollbar { display: none; }

        .story-image-wrap { padding-bottom: 3.5rem; }
        @media (min-width: 1024px) {
            .story-image-wrap { padding-bottom: 0; }
        }

        .watermark-text {
            font-family: 'Orbitron', serif;
            font-size: clamp(3rem, 12vw, 10rem);
            font-weight: 700;
            color: rgba(0, 0, 0, 0.05);
            position: absolute; top: 8%; left: 0;
            z-index: 0; user-select: none; pointer-events: none;
            line-height: 1;
            max-width: 100%;
            overflow: hidden;
        }

        .step-card::before {
            content: attr(data-step);
            position: absolute; top: -0.75rem; left: 1.5rem;
            font-size: 10px; font-weight: 700; letter-spacing: 0.2em;
            color: #775a19; background: #e9c176;
            padding: 0.25rem 0.625rem; border-radius: 9999px;
        }

        #mobileMenu {
            transform: translateX(100%);
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        #mobileMenu.is-open { transform: translateX(0); }
        #menuOverlay { opacity: 0; pointer-events: none; transition: opacity 0.3s ease; }
        #menuOverlay.is-open { opacity: 1; pointer-events: auto; }

        @media (prefers-reduced-motion: reduce) {
            .reveal-up { opacity: 1; transform: none; transition: none; }
            .hero-scroll-hint__icon { animation: none; }
            .glass-card:hover { transform: none; }
        }
    </style>
</head>
<body class="font-body-md antialiased relative">
    <div class="bg-hero-pattern" aria-hidden="true"></div>

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 w-full z-50 px-4 sm:px-6 lg:px-margin-desktop py-4 lg:py-5 bg-surface/70 backdrop-blur-xl border-b border-outline-variant/10 transition-all duration-300" id="navbar" role="navigation" aria-label="Điều hướng chính">
        <div class="max-w-container-max mx-auto flex justify-between items-center gap-4">
            <a href="index.php" class="text-lg sm:text-headline-sm font-headline-sm font-bold text-primary flex items-center gap-2 hover:opacity-85 transition-opacity shrink-0">
                <span class="material-symbols-outlined text-3xl sm:text-4xl" data-weight="fill">sports_motorsports</span>
                <span class="hidden sm:inline">AutoSuperCar</span>
            </a>

            <div class="hidden xl:flex items-center gap-1">
                <?php foreach ($navItems as $item): ?>
                    <a
                        href="<?php echo htmlspecialchars($item['href']); ?>"
                        class="nav-link text-label-caps font-label-caps <?php echo $currentPage === $item['id'] ? 'is-active' : 'text-on-surface-variant'; ?>"
                        <?php echo $currentPage === $item['id'] ? 'aria-current="page"' : ''; ?>
                    ><?php echo htmlspecialchars($item['label']); ?></a>
                <?php endforeach; ?>
            </div>

            <div class="flex items-center gap-3 sm:gap-5">
                <div class="hidden md:flex items-center gap-2 text-sm text-on-surface-variant border-r border-outline-variant/30 pr-4 lg:pr-6">
                    <span class="material-symbols-outlined text-primary text-xl" aria-hidden="true">partly_cloudy_day</span>
                    <span id="weatherTemp">--°C</span>
                </div>

                <div class="hidden sm:flex items-center gap-3 text-sm font-medium">
                    <?php if (isset($_SESSION['admin_id']) || isset($_SESSION['user_id'])): ?>
                        <div class="flex items-center gap-2 text-primary max-w-[140px] truncate">
                            <span class="material-symbols-outlined shrink-0" aria-hidden="true">account_circle</span>
                            <span class="truncate"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['user_name'] ?? 'Thành viên'); ?></span>
                        </div>
                        <a href="../logout.php" title="Đăng xuất" class="text-error hover:text-red-400 transition-colors flex items-center" aria-label="Đăng xuất">
                            <span class="material-symbols-outlined text-lg">logout</span>
                        </a>
                    <?php else: ?>
                        <a href="../login.php" class="text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-lg" aria-hidden="true">login</span>
                            <span class="hidden lg:inline">Đăng nhập</span>
                        </a>
                    <?php endif; ?>
                </div>

                <a href="booking.php" class="hidden sm:inline-flex items-center gap-2 btn-glow text-on-primary-fixed px-5 lg:px-7 py-2.5 lg:py-3 text-label-caps font-label-caps rounded-full shadow-[0_0_20px_rgba(233,193,118,0.15)]">
                    <span class="material-symbols-outlined text-lg" aria-hidden="true">steering_wheel_heat</span>
                    Đặt Lịch
                </a>

                <button type="button" id="menuToggle" class="xl:hidden flex items-center justify-center w-11 h-11 rounded-full border border-outline-variant/30 text-on-surface hover:border-primary/40 hover:text-primary transition-colors" aria-expanded="false" aria-controls="mobileMenu" aria-label="Mở menu">
                    <span class="material-symbols-outlined text-2xl" id="menuIcon">menu</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile menu overlay -->
    <div id="menuOverlay" class="fixed inset-0 z-[60] bg-black/70 backdrop-blur-sm xl:hidden" aria-hidden="true"></div>
    <aside id="mobileMenu" class="fixed top-0 right-0 z-[70] h-full w-[min(100%,320px)] bg-surface-container-low border-l border-outline-variant/20 shadow-2xl xl:hidden flex flex-col" aria-label="Menu di động" aria-hidden="true">
        <div class="flex items-center justify-between p-6 border-b border-outline-variant/15">
            <span class="text-label-caps font-label-caps text-primary tracking-widest">Menu</span>
            <button type="button" id="menuClose" class="w-10 h-10 rounded-full border border-outline-variant/30 flex items-center justify-center text-on-surface-variant hover:text-primary transition-colors" aria-label="Đóng menu">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <nav class="flex-1 overflow-y-auto p-4 space-y-1">
            <?php foreach ($navItems as $item): ?>
                <a
                    href="<?php echo htmlspecialchars($item['href']); ?>"
                    class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-medium transition-colors <?php echo $currentPage === $item['id'] ? 'bg-primary/15 text-primary' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface'; ?>"
                >
                    <span class="material-symbols-outlined text-xl" aria-hidden="true"><?php echo $item['icon']; ?></span>
                    <?php echo htmlspecialchars($item['label']); ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="p-6 border-t border-outline-variant/15 space-y-4">
            <a href="booking.php" class="btn-glow w-full flex items-center justify-center gap-2 py-3.5 rounded-full text-label-caps font-label-caps text-on-primary-fixed">
                <span class="material-symbols-outlined" aria-hidden="true">event_available</span>
                Đặt Lịch Lái Thử
            </a>
            <?php if (!isset($_SESSION['admin_id']) && !isset($_SESSION['user_id'])): ?>
                <a href="../login.php" class="w-full flex items-center justify-center gap-2 py-3 rounded-full btn-outline-gold text-sm text-on-surface-variant">
                    <span class="material-symbols-outlined text-lg" aria-hidden="true">login</span>
                    Đăng nhập
                </a>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Hero -->
    <header class="hero-section relative z-10 w-full" id="top">
        <div class="hero-content reveal-up active">
            <p class="text-label-caps font-label-caps text-primary tracking-[0.2em] uppercase mb-4 sm:mb-5">Tuyệt Tác Vượt Thời Gian</p>
            <h1 class="text-4xl sm:text-5xl md:text-6xl xl:text-7xl font-display-lg font-bold leading-[1.12] uppercase tracking-wide text-on-surface mb-5 sm:mb-6">
                Định Hình<br class="hidden sm:block"/>
                <span class="bg-gradient-to-r from-primary via-[#f5e6c8] to-on-surface-variant bg-clip-text text-transparent">Đẳng Cấp</span>
            </h1>
            <p class="text-base sm:text-lg text-on-surface-variant max-w-2xl mx-auto font-light leading-relaxed mb-8 sm:mb-10 px-2">
                Hơn cả một showroom ô tô — AutoSuperCar là nơi giao thoa giữa nghệ thuật chế tác cơ khí đỉnh cao và phong cách sống thượng lưu độc bản.
            </p>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3 sm:gap-4 w-full max-w-md sm:max-w-none">
                <a href="#story" class="btn-outline-gold inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-full text-sm font-semibold tracking-wide text-on-surface">
                    <span class="material-symbols-outlined text-xl" aria-hidden="true">auto_stories</span>
                    Câu Chuyện
                </a>
                <a href="booking.php" class="btn-glow inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-full text-sm font-semibold tracking-wide text-on-primary-fixed">
                    <span class="material-symbols-outlined text-xl" aria-hidden="true">event_available</span>
                    Đặt Lịch Lái Thử
                </a>
            </div>
        </div>

        <a href="#story" class="hero-scroll-hint" aria-label="Cuộn xuống nội dung">
            <span class="hero-scroll-hint__label">Khám phá</span>
            <svg class="hero-scroll-hint__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 5v14M5 13l7 7 7-7"/>
            </svg>
        </a>
    </header>

    <!-- Sub navigation -->
    <div id="subnavBar" class="subnav-bar sticky z-40 bg-[#ffffff]/95 backdrop-blur-md border-b border-outline-variant/10">
        <div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-margin-desktop py-2.5 subnav-scroll overflow-x-auto">
            <div class="flex items-center gap-2 min-w-max" role="tablist" aria-label="Mục trang Giới thiệu">
                <a href="#story" class="subnav-link is-active" data-subnav="story">Câu chuyện</a>
                <a href="#values" class="subnav-link" data-subnav="values">Giá trị</a>
                <a href="#experience" class="subnav-link" data-subnav="experience">Trải nghiệm</a>
                <a href="#visit" class="subnav-link" data-subnav="visit">Showroom</a>
                <a href="booking.php" class="subnav-link !text-primary !border-primary/40 !bg-primary/10 inline-flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base leading-none" aria-hidden="true">event_available</span>
                    Lái thử
                </a>
            </div>
        </div>
    </div>

    <!-- Story -->
    <section id="story" class="section-pill relative py-20 sm:py-28 lg:py-32 px-4 sm:px-6 lg:px-margin-desktop bg-[#ffffff] overflow-hidden">
        <div class="watermark-text" aria-hidden="true">HISTORY</div>

        <div class="max-w-container-max mx-auto relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div class="relative reveal-up order-2 lg:order-1 story-image-wrap">
                <div class="absolute -inset-4 bg-primary/5 rounded-3xl blur-2xl pointer-events-none" aria-hidden="true"></div>
                <img
                    src="../assets/images/cars/Porche%20911%20turbo.jpg"
                    alt="Showroom AutoSuperCar"
                    class="relative w-full lg:w-[92%] rounded-2xl shadow-[0_30px_60px_rgba(0,0,0,0.5)] object-cover aspect-[4/3]"
                    loading="lazy"
                    onerror="this.src='https://images.unsplash.com/photo-1603584173870-7f23fdae1b7a?auto=format&fit=crop&w=1200&q=80'"
                />
                <div class="absolute bottom-0 right-0 sm:-bottom-4 lg:right-6 glass-card p-5 sm:p-8 rounded-2xl text-center min-w-[160px] sm:min-w-[180px] z-10">
                    <div class="text-5xl sm:text-6xl font-display-lg font-bold text-primary leading-none counter" data-target="5">0</div>
                    <div class="text-label-caps font-label-caps tracking-widest text-on-surface-variant mt-2 text-[10px] sm:text-xs">NĂM KIẾN TẠO ĐỈNH CAO</div>
                </div>
            </div>

            <div class="lg:pl-6 mt-8 lg:mt-0 reveal-up delay-200 order-1 lg:order-2">
                <span class="text-label-caps font-label-caps text-primary tracking-[0.2em] uppercase border-b border-primary/30 pb-2 inline-block mb-5">Câu Chuyện Thương Hiệu</span>
                <h2 class="text-3xl sm:text-headline-md font-headline-md text-on-surface leading-[1.2] mb-6">
                    Hành Trình Tới<br/>
                    <span class="text-primary italic">Biểu Tượng</span>
                </h2>
                <div class="space-y-5 text-on-surface-variant font-light text-base sm:text-lg leading-relaxed">
                    <p>Khởi nguồn từ niềm đam mê mãnh liệt với những cỗ máy tốc độ và sự hoàn mỹ trong thiết kế, AutoSuperCar được thành lập với tầm nhìn trở thành điểm đến tối thượng cho giới tinh hoa yêu xe tại Việt Nam.</p>
                    <p>Chúng tôi tự hào sở hữu bộ sưu tập độc bản từ những thương hiệu danh giá bậc nhất thế giới — mỗi chiếc xe là một tác phẩm nghệ thuật và bản tuyên ngôn về vị thế của chủ nhân.</p>
                </div>
                <div class="flex flex-wrap gap-3 mt-8">
                    <a href="cars.php" class="btn-outline-gold inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-sm text-on-surface-variant">
                        <span class="material-symbols-outlined text-lg" aria-hidden="true">directions_car</span>
                        Xem bộ sưu tập
                    </a>
                    <a href="booking.php" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-sm text-primary hover:underline">
                        Trải nghiệm lái thử
                        <span class="material-symbols-outlined text-lg" aria-hidden="true">arrow_forward</span>
                    </a>
                </div>
                <p class="font-signature text-4xl sm:text-5xl lg:text-6xl text-black/10 mt-8 select-none overflow-hidden" aria-hidden="true">AutoSuperCar</p>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="py-16 sm:py-20 px-4 sm:px-6 lg:px-margin-desktop bg-surface-container-lowest border-y border-outline-variant/10 relative z-10" aria-label="Thống kê">
        <div class="max-w-container-max mx-auto grid grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
            <?php
            $stats = [
                ['target' => 500, 'suffix' => '+', 'label' => 'SIÊU XE ĐÃ GIAO', 'delay' => ''],
                ['target' => 100, 'suffix' => '%', 'label' => 'HÀI LÒNG TUYỆT ĐỐI', 'delay' => 'delay-100'],
                ['target' => 24,  'suffix' => '/7', 'label' => 'DỊCH VỤ ĐẶC QUYỀN', 'delay' => 'delay-200'],
                ['target' => 15,  'suffix' => '+', 'label' => 'THƯƠNG HIỆU ĐỈNH CAO', 'delay' => 'delay-300'],
            ];
            foreach ($stats as $stat):
            ?>
            <div class="reveal-up <?php echo $stat['delay']; ?> text-center">
                <div class="flex justify-center items-end gap-0.5 mb-3 text-primary">
                    <span class="text-4xl sm:text-5xl lg:text-6xl font-display-lg font-bold counter" data-target="<?php echo $stat['target']; ?>">0</span>
                    <span class="text-3xl sm:text-4xl font-display-lg font-bold pb-0.5"><?php echo $stat['suffix']; ?></span>
                </div>
                <p class="text-[10px] sm:text-label-caps font-label-caps tracking-widest text-on-surface-variant"><?php echo $stat['label']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Values -->
    <section id="values" class="section-pill py-20 sm:py-28 lg:py-32 px-4 sm:px-6 lg:px-margin-desktop bg-[#ffffff] relative z-10 overflow-hidden">
        <div class="absolute top-0 right-0 w-2/3 h-full bg-[radial-gradient(circle_at_center,rgba(212,175,55,0.06)_0%,transparent_70%)] pointer-events-none" aria-hidden="true"></div>

        <div class="max-w-3xl mx-auto text-center mb-14 sm:mb-16 reveal-up">
            <span class="text-label-caps font-label-caps text-primary tracking-[0.2em] uppercase border-b border-primary/30 pb-2 inline-block mb-5">Triết Lý Kinh Doanh</span>
            <h2 class="text-3xl sm:text-headline-md font-headline-md text-on-surface leading-[1.2] mb-4">Giá Trị Vượt Thời Gian</h2>
            <p class="text-body-md sm:text-body-lg text-on-surface-variant font-light max-w-2xl mx-auto">Chuẩn mực dịch vụ và cam kết dành cho giới tinh hoa — trải nghiệm không thể sao chép.</p>
        </div>

        <div class="max-w-container-max mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 relative z-10">
            <?php
            $values = [
                ['icon' => 'diamond', 'title' => 'Độc Bản & Đẳng Cấp', 'desc' => 'Chỉ tuyển chọn những mẫu xe xuất sắc nhất, mang tính biểu tượng và khẳng định vị thế độc tôn trên mọi hành trình.', 'delay' => ''],
                ['icon' => 'verified_user', 'title' => 'Đặc Quyền Tối Thượng', 'desc' => 'Dịch vụ cá nhân hóa, không gian riêng tư tuyệt đối và đặc quyền chỉ dành cho thành viên AutoSuperCar.', 'delay' => 'delay-100'],
                ['icon' => 'globe', 'title' => 'Uy Tín Toàn Cầu', 'desc' => 'Minh bạch trong giao dịch, bảo hành và bảo dưỡng theo tiêu chuẩn khắt khe nhất của từng hãng.', 'delay' => 'delay-200'],
            ];
            foreach ($values as $v):
            ?>
            <article class="glass-card p-8 sm:p-10 rounded-2xl group reveal-up <?php echo $v['delay']; ?> relative overflow-hidden h-full flex flex-col">
                <div class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-primary to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700" aria-hidden="true"></div>
                <span class="material-symbols-outlined text-4xl sm:text-5xl text-primary mb-6" data-weight="fill" aria-hidden="true"><?php echo $v['icon']; ?></span>
                <h3 class="text-xl sm:text-2xl font-display-lg font-semibold text-on-surface mb-3"><?php echo $v['title']; ?></h3>
                <p class="text-on-surface-variant font-light leading-relaxed text-sm sm:text-base flex-1"><?php echo $v['desc']; ?></p>
            </article>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Experience steps -->
    <section id="experience" class="section-pill py-20 sm:py-28 px-4 sm:px-6 lg:px-margin-desktop bg-surface-container-lowest border-y border-outline-variant/10 relative z-10">
        <div class="max-w-container-max mx-auto">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-12 reveal-up">
                <div>
                    <span class="text-label-caps font-label-caps text-primary tracking-[0.2em] uppercase border-b border-primary/30 pb-2 inline-block mb-4">Trải Nghiệm</span>
                    <h2 class="text-3xl sm:text-headline-md font-headline-md text-on-surface">Hành Trình Lái Thử Đặc Quyền</h2>
                </div>
                <a href="booking.php" class="btn-glow inline-flex items-center justify-center gap-2 self-start lg:self-auto px-8 py-3.5 rounded-full text-label-caps font-label-caps text-on-primary-fixed">
                    Bắt đầu ngay
                    <span class="material-symbols-outlined text-lg" aria-hidden="true">arrow_forward</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                <?php
                $steps = [
                    ['step' => '01', 'icon' => 'edit_calendar', 'title' => 'Đặt lịch online', 'desc' => 'Chọn thời gian và mẫu xe quan tâm — xác nhận trong vài phút.'],
                    ['step' => '02', 'icon' => 'support_agent', 'title' => 'Tư vấn 1:1', 'desc' => 'Chuyên gia riêng giải đáp chi tiết kỹ thuật và cấu hình.'],
                    ['step' => '03', 'icon' => 'speed', 'title' => 'Lái thử độc quyền', 'desc' => 'Lộ trình tùy chọn trên cung đường riêng — cảm nhận uy lực thực tế.'],
                ];
                foreach ($steps as $i => $s):
                ?>
                <div class="glass-card step-card relative pt-10 p-8 rounded-2xl reveal-up <?php echo $i > 0 ? 'delay-' . ($i * 100) : ''; ?>" data-step="<?php echo $s['step']; ?>">
                    <span class="material-symbols-outlined text-3xl text-primary mb-4" data-weight="fill" aria-hidden="true"><?php echo $s['icon']; ?></span>
                    <h3 class="text-lg font-semibold text-on-surface mb-2"><?php echo $s['title']; ?></h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed"><?php echo $s['desc']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Visit / Showroom -->
    <section id="visit" class="section-pill py-20 sm:py-28 px-4 sm:px-6 lg:px-margin-desktop bg-[#ffffff] relative z-10">
        <div class="max-w-container-max mx-auto grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-stretch reveal-up">
            <div class="glass-card rounded-2xl p-8 sm:p-10 flex flex-col justify-center">
                <span class="text-label-caps font-label-caps text-primary tracking-[0.2em] uppercase mb-4">Showroom</span>
                <h2 class="text-2xl sm:text-headline-sm font-headline-sm text-on-surface mb-6">Ghé Thăm Không Gian Đẳng Cấp</h2>
                <address class="not-italic space-y-4 text-on-surface-variant">
                    <p class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary shrink-0 mt-0.5" aria-hidden="true">location_on</span>
                        178 Đại Mỗ, Nam Từ Liêm<br/>Hà Nội, Việt Nam
                    </p>
                    <p class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary shrink-0" aria-hidden="true">call</span>
                        <a href="tel:+84356827852" class="hover:text-primary transition-colors font-medium text-on-surface">+84 (0) 356 827 852</a>
                    </p>
                    <p class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary shrink-0" aria-hidden="true">schedule</span>
                        Thứ 2 – Chủ nhật: 9:00 – 20:00
                    </p>
                </address>
                <div class="flex flex-wrap gap-3 mt-8">
                    <a href="booking.php" class="btn-glow inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm text-on-primary-fixed font-semibold tracking-wide">
                        <span class="material-symbols-outlined text-lg" aria-hidden="true">event_available</span>
                        Đặt lịch thăm quan
                    </a>
                    <a href="https://maps.google.com/?q=178+Đại+Mỗ+Nam+Từ+Liêm+Hà+Nội" target="_blank" rel="noopener noreferrer" class="btn-outline-gold inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm text-on-surface-variant">
                        <span class="material-symbols-outlined text-lg" aria-hidden="true">map</span>
                        Chỉ đường
                    </a>
                </div>
            </div>
            <div class="rounded-2xl overflow-hidden border border-outline-variant/20 min-h-[280px] lg:min-h-0 bg-surface-container relative">
                <img
                    src="../assets/images/cars/mercedes-s-class.jpg"
                    alt="Không gian showroom"
                    class="w-full h-full object-cover min-h-[280px] lg:min-h-[360px]"
                    loading="lazy"
                    onerror="this.style.display='none'"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-[#ffffff] via-transparent to-transparent pointer-events-none" aria-hidden="true"></div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="relative z-10 px-4 sm:px-6 lg:px-margin-desktop py-16 sm:py-20" aria-label="Đặt lịch lái thử">
        <div class="max-w-container-max mx-auto reveal-up">
            <div class="relative overflow-hidden rounded-3xl border border-primary/25 bg-gradient-to-br from-surface-container via-[#1a1814] to-surface-container-lowest p-10 sm:p-14 text-center">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(233,193,118,0.12)_0%,transparent_55%)] pointer-events-none" aria-hidden="true"></div>
                <div class="relative z-10 max-w-2xl mx-auto">
                    <span class="material-symbols-outlined text-5xl text-primary mb-6" data-weight="fill" aria-hidden="true">steering_wheel_heat</span>
                    <h2 class="text-2xl sm:text-headline-sm font-headline-sm text-on-surface mb-4">Sẵn Sàng Cảm Nhận Sức Mạnh?</h2>
                    <p class="text-on-surface-variant font-light mb-8 text-sm sm:text-base">Đặt lịch lái thử độc quyền — tư vấn riêng, lộ trình tùy chọn và không gian VIP dành cho bạn.</p>
                    <a href="booking.php" class="btn-glow inline-flex items-center gap-2 px-10 py-4 rounded-full text-label-caps font-label-caps text-on-primary-fixed">
                        Đến Trang Đặt Lịch
                        <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="w-full py-14 sm:py-16 px-4 sm:px-6 lg:px-margin-desktop border-t border-outline-variant/10 bg-surface-container-lowest relative z-20">
        <div class="max-w-container-max mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-10 lg:gap-gutter">
            <div class="sm:col-span-2 lg:col-span-2 space-y-5">
                <div class="text-headline-sm font-headline-sm font-bold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-3xl" data-weight="fill" aria-hidden="true">sports_motorsports</span>
                    AutoSuperCar
                </div>
                <p class="text-on-surface-variant text-body-md opacity-80 max-w-sm">Định hình tiêu chuẩn mới cho dịch vụ siêu xe đẳng cấp tại Việt Nam.</p>
            </div>

            <div>
                <h4 class="text-label-caps font-label-caps text-on-surface mb-5 uppercase tracking-widest">Điều hướng</h4>
                <nav class="flex flex-col gap-2.5" aria-label="Liên kết chân trang">
                    <?php foreach ($navItems as $item): ?>
                        <a class="text-on-surface-variant hover:text-primary transition-colors text-sm" href="<?php echo htmlspecialchars($item['href']); ?>"><?php echo htmlspecialchars($item['label']); ?></a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <div>
                <h4 class="text-label-caps font-label-caps text-on-surface mb-5 uppercase tracking-widest">Showroom</h4>
                <p class="text-on-surface-variant text-sm leading-relaxed">
                    178 Đại Mỗ<br/>Nam Từ Liêm, Hà Nội
                </p>
                <a href="tel:+84356827852" class="text-primary font-medium text-sm mt-3 inline-flex items-center gap-2 hover:opacity-80 transition-opacity">
                    <span class="material-symbols-outlined text-sm" aria-hidden="true">call</span> +84 356 827 852
                </a>
            </div>

            <div>
                <h4 class="text-label-caps font-label-caps text-on-surface mb-5 uppercase tracking-widest">Kết nối</h4>
                <div class="flex flex-col gap-2.5">
                    <a class="text-on-surface-variant hover:text-primary transition-colors text-sm" href="#">Facebook</a>
                    <a class="text-on-surface-variant hover:text-primary transition-colors text-sm" href="#">Instagram</a>
                    <a class="text-on-surface-variant hover:text-primary transition-colors text-sm" href="#">YouTube</a>
                </div>
            </div>
        </div>
        <div class="max-w-container-max mx-auto mt-12 pt-8 border-t border-outline-variant/10 flex flex-col sm:flex-row justify-between items-center gap-2 text-sm text-on-surface-variant opacity-70">
            <p>&copy; <?php echo date('Y'); ?> AutoSuperCar. All rights reserved.</p>
            <p>Designed for Excellence</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            updateLayoutVars();
            window.addEventListener('resize', updateLayoutVars);
            fetchWeather();
            initNavbar();
            initMobileMenu();
            initScrollReveal();
            initSubnav();
            initSmoothScroll();
            requestAnimationFrame(updateLayoutVars);
        });

        function updateLayoutVars() {
            const nav = document.getElementById('navbar');
            const subnav = document.getElementById('subnavBar');
            if (nav) {
                document.documentElement.style.setProperty('--nav-h', nav.offsetHeight + 'px');
            }
            if (subnav) {
                document.documentElement.style.setProperty('--subnav-h', subnav.offsetHeight + 'px');
            }
        }

        function initSmoothScroll() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const id = this.getAttribute('href');
                    if (!id || id === '#') return;
                    const target = document.querySelector(id);
                    if (!target) return;
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            });
        }

        function fetchWeather() {
            fetch('../api/get_weather.php')
                .then(r => r.json())
                .then(data => {
                    if (data?.current_weather) {
                        const temp = Math.round(data.current_weather.temperature);
                        const el = document.getElementById('weatherTemp');
                        if (el) el.textContent = `${temp}°C Hà Nội`;
                    }
                })
                .catch(() => {});
        }

        function initNavbar() {
            const navbar = document.getElementById('navbar');
            if (!navbar) return;
            const onScroll = () => {
                if (window.scrollY > 40) {
                    navbar.classList.add('bg-surface/95', 'shadow-lg', 'py-3');
                    navbar.classList.remove('bg-surface/70', 'py-4', 'lg:py-5');
                } else {
                    navbar.classList.remove('bg-surface/95', 'shadow-lg', 'py-3');
                    navbar.classList.add('bg-surface/70', 'py-4', 'lg:py-5');
                }
                updateLayoutVars();
            };
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        }

        function initMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const overlay = document.getElementById('menuOverlay');
            const toggle = document.getElementById('menuToggle');
            const closeBtn = document.getElementById('menuClose');
            const icon = document.getElementById('menuIcon');
            if (!menu || !toggle) return;

            const setOpen = (open) => {
                menu.classList.toggle('is-open', open);
                overlay?.classList.toggle('is-open', open);
                document.body.classList.toggle('menu-open', open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                menu.setAttribute('aria-hidden', open ? 'false' : 'true');
                if (icon) icon.textContent = open ? 'close' : 'menu';
            };

            toggle.addEventListener('click', () => setOpen(!menu.classList.contains('is-open')));
            closeBtn?.addEventListener('click', () => setOpen(false));
            overlay?.addEventListener('click', () => setOpen(false));
            menu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setOpen(false)));
            document.addEventListener('keydown', e => { if (e.key === 'Escape') setOpen(false); });
        }

        function initScrollReveal() {
            const revealElements = document.querySelectorAll('.reveal-up');
            const observer = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('active');
                    const counters = entry.target.querySelectorAll('.counter');
                    if (counters.length) startCounters(counters);
                    obs.unobserve(entry.target);
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

            revealElements.forEach(el => {
                if (!el.classList.contains('active')) observer.observe(el);
            });
        }

        function initSubnav() {
            const links = document.querySelectorAll('[data-subnav]');
            const sections = [...links].map(l => document.getElementById(l.dataset.subnav)).filter(Boolean);
            if (!sections.length) return;

            const update = () => {
                let current = sections[0].id;
                const root = getComputedStyle(document.documentElement);
                const offset = parseFloat(root.getPropertyValue('--scroll-offset')) || 140;
                sections.forEach(sec => {
                    if (sec.getBoundingClientRect().top <= offset) current = sec.id;
                });
                links.forEach(l => l.classList.toggle('is-active', l.dataset.subnav === current));
            };
            window.addEventListener('scroll', update, { passive: true });
            update();
        }

        function startCounters(counters) {
            counters.forEach(counter => {
                if (counter.dataset.animated) return;
                counter.dataset.animated = '1';
                const target = +counter.getAttribute('data-target') || 0;
                const duration = 1800;
                const start = performance.now();
                const tick = (now) => {
                    const p = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - p, 3);
                    counter.textContent = Math.round(target * eased);
                    if (p < 1) requestAnimationFrame(tick);
                    else counter.textContent = target;
                };
                requestAnimationFrame(tick);
            });
        }
    </script>
</body>
</html>
