<?php
// customer/about.php
require_once '../config/db.php';

$currentPage = 'about';
$navItems = [
    ['id' => 'home',    'label' => 'Home',    'href' => 'index.php',   'icon' => 'home'],
    ['id' => 'cars',    'label' => 'Explore Cars',  'href' => 'cars.php',    'icon' => 'directions_car'],
    ['id' => 'compare', 'label' => 'Compare',      'href' => 'compare.php', 'icon' => 'compare_arrows'],
    ['id' => 'about',   'label' => 'About Us',  'href' => 'about.php',   'icon' => 'info'],
    ['id' => 'booking', 'label' => 'Test Drive',     'href' => 'booking.php', 'icon' => 'event_available'],
    ['id' => 'contact', 'label' => 'Contact',    'href' => 'contact.php', 'icon' => 'mail'],
];
?>
<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Auto DreamCars | About Us</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                                        "colors": {
                        "primary": "#d4a843",
                        "primary-fixed": "#f0c96a",
                        "primary-fixed-dim": "#a67c2e",
                        "on-primary": "#ffffff",
                        "on-primary-fixed": "#111111",
                        "surface": "#0a0a0a",
                        "surface-dim": "#111111",
                        "surface-container": "#171717",
                        "surface-container-low": "#111111",
                        "surface-container-lowest": "#050505",
                        "surface-container-high": "#262626",
                        "surface-container-highest": "#333333",
                        "on-surface": "#f5f5f5",
                        "on-surface-variant": "#a3a3a3",
                        "outline": "#404040",
                        "outline-variant": "#262626",
                        "error": "#ffb4ab",
                        "on-error": "#690005",
                        "secondary": "#262626",
                        "on-secondary": "#a3a3a3",
                        "secondary-container": "#333333",
                        "on-secondary-container": "#d4d4d4"
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
                        "display-lg": ["Outfit", "sans-serif"],
                        "label-caps": ["Outfit", "sans-serif"],
                        "headline-md": ["Outfit", "sans-serif"],
                        "headline-sm": ["Outfit", "sans-serif"],
                        "body-md": ["Outfit", "sans-serif"],
                        "body-lg": ["Outfit", "sans-serif"],
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

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Mrs+Saint+Delafield&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <style>
        :root {
            --nav-h: 72px;
            --subnav-h: 52px;
            --scroll-offset: calc(var(--nav-h) + var(--subnav-h) + 16px);
        }

        body { background-color: #0a0a0a; color: #f5f5f5; overflow-x: hidden; }
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
            background: radial-gradient(circle at 70% 30%, rgba(233, 193, 118, 0.08) 0%, rgba(10, 10, 10, 1) 65%);
        }

        .glass-card {
            background: rgba(23, 23, 23, 0.7);
            backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(233, 193, 118, 0.12);
            box-shadow: 0 24px 48px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(0, 0, 0, 0.04);
            transition: border-color 0.35s ease, transform 0.35s ease, background 0.35s ease;
        }
        .glass-card:hover {
            background: rgba(38, 38, 38, 0.8);
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
                linear-gradient(180deg, rgba(10,10,10,0.8) 0%, rgba(10,10,10,0.95) 60%, #0a0a0a 100%),
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
            font-family: 'Outfit', sans-serif;
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
    <nav class="fixed top-0 left-0 w-full z-50 px-4 sm:px-6 lg:px-margin-desktop py-4 lg:py-5 bg-surface/70 backdrop-blur-xl border-b border-outline-variant/10 transition-all duration-300" id="navbar" role="navigation" aria-label="Main navigation">
        <div class="max-w-container-max mx-auto flex justify-between items-center gap-4">
            <a href="index.php" class="font-display-lg font-[900] text-[24px] text-on-surface hover:opacity-80 transition-opacity shrink-0 whitespace-nowrap">
                AUTO <span class="text-primary">DREAMCARS</span>
            </a>

            <div class="hidden xl:flex items-center gap-1">
                <?php foreach ($navItems as $item): ?>
                    <a
                        href="<?php echo htmlspecialchars($item['href']); ?>"
                        class="nav-link text-[13px] font-semibold uppercase tracking-[1px] <?php echo $currentPage === $item['id'] ? 'is-active' : 'text-on-surface-variant'; ?>"
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
                            <span class="truncate"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['user_name'] ?? 'Member'); ?></span>
                        </div>
                        <a href="../logout.php" title="Logout" class="text-error hover:text-red-400 transition-colors flex items-center" aria-label="Logout">
                            <span class="material-symbols-outlined text-lg">logout</span>
                        </a>
                    <?php else: ?>
                        <a href="../login.php" class="text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-lg" aria-hidden="true">login</span>
                            <span class="hidden lg:inline">Login</span>
                        </a>
                    <?php endif; ?>
                </div>



                <button type="button" id="menuToggle" class="xl:hidden flex items-center justify-center w-11 h-11 rounded-full border border-outline-variant/30 text-on-surface hover:border-primary/40 hover:text-primary transition-colors" aria-expanded="false" aria-controls="mobileMenu" aria-label="Open menu">
                    <span class="material-symbols-outlined text-2xl" id="menuIcon">menu</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile menu overlay -->
    <div id="menuOverlay" class="fixed inset-0 z-[60] bg-black/70 backdrop-blur-sm xl:hidden" aria-hidden="true"></div>
    <aside id="mobileMenu" class="fixed top-0 right-0 z-[70] h-full w-[min(100%,320px)] bg-surface-container-low border-l border-outline-variant/20 shadow-2xl xl:hidden flex flex-col" aria-label="Mobile menu" aria-hidden="true">
        <div class="flex items-center justify-between p-6 border-b border-outline-variant/15">
            <span class="text-label-caps font-label-caps text-primary tracking-widest">Menu</span>
            <button type="button" id="menuClose" class="w-10 h-10 rounded-full border border-outline-variant/30 flex items-center justify-center text-on-surface-variant hover:text-primary transition-colors" aria-label="Close menu">
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

            <?php if (!isset($_SESSION['admin_id']) && !isset($_SESSION['user_id'])): ?>
                <a href="../login.php" class="w-full flex items-center justify-center gap-2 py-3 rounded-full btn-outline-gold text-sm text-on-surface-variant">
                    <span class="material-symbols-outlined text-lg" aria-hidden="true">login</span>
                    Login
                </a>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Hero -->
    <header class="hero-section relative z-10 w-full" id="top">
        <div class="hero-content reveal-up active">
            <p class="text-label-caps font-label-caps text-primary tracking-[0.2em] uppercase mb-4 sm:mb-5">Timeless Masterpiece</p>
            <h1 class="text-4xl sm:text-5xl md:text-6xl xl:text-7xl font-display-lg font-bold leading-[1.12] uppercase tracking-wide text-on-surface mb-5 sm:mb-6">
                Shaping<br class="hidden sm:block"/>
                <span class="bg-gradient-to-r from-primary via-[#f5e6c8] to-on-surface-variant bg-clip-text text-transparent">Class</span>
            </h1>
            <p class="text-base sm:text-lg text-on-surface-variant max-w-2xl mx-auto font-light leading-relaxed mb-8 sm:mb-10 px-2">
                More than a car showroom — Auto DreamCars is the intersection of top-notch mechanical engineering and unique luxury lifestyle.
            </p>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3 sm:gap-4 w-full max-w-md sm:max-w-none">
                <a href="#story" class="btn-outline-gold inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-full text-sm font-semibold tracking-wide text-on-surface">
                    <span class="material-symbols-outlined text-xl" aria-hidden="true">auto_stories</span>
                    Story
                </a>
                <a href="booking.php" class="btn-glow inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-full text-sm font-semibold tracking-wide text-on-primary-fixed">
                    <span class="material-symbols-outlined text-xl" aria-hidden="true">event_available</span>
                    Test Drive
                </a>
            </div>
        </div>

        <a href="#story" class="hero-scroll-hint" aria-label="Explore">
            <span class="hero-scroll-hint__label">Explore</span>
            <svg class="hero-scroll-hint__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 5v14M5 13l7 7 7-7"/>
            </svg>
        </a>
    </header>

    <!-- Sub navigation -->
    <div id="subnavBar" class="subnav-bar sticky z-40 bg-surface/95 backdrop-blur-md border-b border-outline-variant/10">
        <div class="max-w-container-max mx-auto px-4 sm:px-6 lg:px-margin-desktop py-2.5 subnav-scroll overflow-x-auto">
            <div class="flex items-center gap-2 min-w-max" role="tablist" aria-label="About page sections">
                <a href="#story" class="subnav-link is-active" data-subnav="story">Story</a>
                <a href="#values" class="subnav-link" data-subnav="values">Values</a>
                <a href="#experience" class="subnav-link" data-subnav="experience">Experience</a>
                <a href="#visit" class="subnav-link" data-subnav="visit">Showroom</a>

            </div>
        </div>
    </div>

    <!-- Story -->
    <section id="story" class="section-pill relative py-20 sm:py-28 lg:py-32 px-4 sm:px-6 lg:px-margin-desktop bg-surface overflow-hidden">
        <div class="watermark-text" aria-hidden="true">HISTORY</div>

        <div class="max-w-container-max mx-auto relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div class="relative reveal-up order-2 lg:order-1 story-image-wrap">
                <div class="absolute -inset-4 bg-primary/5 rounded-3xl blur-2xl pointer-events-none" aria-hidden="true"></div>
                <img
                    src="../assets/images/cars/Porche%20911%20turbo.jpg"
                    alt="Showroom Auto DreamCars"
                    class="relative w-full lg:w-[92%] rounded-2xl shadow-[0_30px_60px_rgba(0,0,0,0.5)] object-cover aspect-[4/3]"
                    loading="lazy"
                    onerror="this.src='https://images.unsplash.com/photo-1603584173870-7f23fdae1b7a?auto=format&fit=crop&w=1200&q=80'"
                />
                <div class="absolute bottom-0 right-0 sm:-bottom-4 lg:right-6 glass-card p-5 sm:p-8 rounded-2xl text-center min-w-[160px] sm:min-w-[180px] z-10">
                    <div class="text-5xl sm:text-6xl font-display-lg font-bold text-primary leading-none counter" data-target="5">0</div>
                    <div class="text-label-caps font-label-caps tracking-widest text-on-surface-variant mt-2 text-[10px] sm:text-xs">YEARS OF CREATING THE PINNACLE</div>
                </div>
            </div>

            <div class="lg:pl-6 mt-8 lg:mt-0 reveal-up delay-200 order-1 lg:order-2">
                <span class="text-label-caps font-label-caps text-primary tracking-[0.2em] uppercase border-b border-primary/30 pb-2 inline-block mb-5">Brand Story</span>
                <h2 class="text-3xl sm:text-headline-md font-headline-md text-on-surface leading-[1.2] mb-6">
                    Journey to<br/>
                    <span class="text-primary italic">Icon</span>
                </h2>
                <div class="space-y-5 text-on-surface-variant font-light text-base sm:text-lg leading-relaxed">
                    <p>Originating from a burning passion for speed machines and perfection in design, Auto DreamCars was established with the vision of becoming the ultimate destination for car-loving elites in Vietnam.</p>
                    <p>We proudly own an exclusive collection from the world's most prestigious brands — each car is a work of art and a manifesto of its owner's status.</p>
                </div>
                <div class="flex flex-wrap gap-3 mt-8">
                    <a href="cars.php" class="btn-outline-gold inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-sm text-on-surface-variant">
                        <span class="material-symbols-outlined text-lg" aria-hidden="true">directions_car</span>
                        View collection
                    </a>
                    <a href="booking.php" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-sm text-primary hover:underline">
                        Test drive experience
                        <span class="material-symbols-outlined text-lg" aria-hidden="true">arrow_forward</span>
                    </a>
                </div>
                <p class="font-signature text-4xl sm:text-5xl lg:text-6xl text-black/10 mt-8 select-none overflow-hidden" aria-hidden="true">Auto DreamCars</p>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="py-16 sm:py-20 px-4 sm:px-6 lg:px-margin-desktop bg-surface-container-lowest border-y border-outline-variant/10 relative z-10" aria-label="Statistics">
        <div class="max-w-container-max mx-auto grid grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
            <?php
            $stats = [
                ['target' => 500, 'suffix' => '+', 'label' => 'SUPER CARS DELIVERED', 'delay' => ''],
                ['target' => 100, 'suffix' => '%', 'label' => 'ABSOLUTE SATISFACTION', 'delay' => 'delay-100'],
                ['target' => 24,  'suffix' => '/7', 'label' => 'EXCLUSIVE SERVICES', 'delay' => 'delay-200'],
                ['target' => 15,  'suffix' => '+', 'label' => 'PREMIUM BRANDS', 'delay' => 'delay-300'],
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
    <section id="values" class="section-pill py-20 sm:py-28 lg:py-32 px-4 sm:px-6 lg:px-margin-desktop bg-surface relative z-10 overflow-hidden">
        <div class="absolute top-0 right-0 w-2/3 h-full bg-[radial-gradient(circle_at_center,rgba(212,175,55,0.06)_0%,transparent_70%)] pointer-events-none" aria-hidden="true"></div>

        <div class="max-w-3xl mx-auto text-center mb-14 sm:mb-16 reveal-up">
            <span class="text-label-caps font-label-caps text-primary tracking-[0.2em] uppercase border-b border-primary/30 pb-2 inline-block mb-5">Business Philosophy</span>
            <h2 class="text-3xl sm:text-headline-md font-headline-md text-on-surface leading-[1.2] mb-4">Timeless Values</h2>
            <p class="text-body-md sm:text-body-lg text-on-surface-variant font-light max-w-2xl mx-auto">Service standards and commitment to the elite — an experience that cannot be copied.</p>
        </div>

        <div class="max-w-container-max mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 relative z-10">
            <?php
            $values = [
                ['icon' => 'diamond', 'title' => 'Unique & Class', 'desc' => 'Only select the most outstanding car models, which are iconic and assert their unique position on every journey.', 'delay' => ''],
                ['icon' => 'verified_user', 'title' => 'Ultimate Privilege', 'desc' => 'Personalized service, absolute private space and privileges exclusively for Auto DreamCars members.', 'delay' => 'delay-100'],
                ['icon' => 'globe', 'title' => 'Global Prestige', 'desc' => 'Transparency in transactions, warranty and maintenance according to the strictest standards of each brand.', 'delay' => 'delay-200'],
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
                    <span class="text-label-caps font-label-caps text-primary tracking-[0.2em] uppercase border-b border-primary/30 pb-2 inline-block mb-4">Experience</span>
                    <h2 class="text-3xl sm:text-headline-md font-headline-md text-on-surface">Exclusive Test Drive Journey</h2>
                </div>
                <a href="booking.php" class="btn-glow inline-flex items-center justify-center gap-2 self-start lg:self-auto px-8 py-3.5 rounded-full text-label-caps font-label-caps text-on-primary-fixed">
                    Start now
                    <span class="material-symbols-outlined text-lg" aria-hidden="true">arrow_forward</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                <?php
                $steps = [
                    ['step' => '01', 'icon' => 'edit_calendar', 'title' => 'Book online', 'desc' => 'Choose time and car model of interest — confirm in minutes.'],
                    ['step' => '02', 'icon' => 'support_agent', 'title' => '1:1 Consulting', 'desc' => 'Private expert explains technical details and configuration.'],
                    ['step' => '03', 'icon' => 'speed', 'title' => 'Exclusive test drive', 'desc' => 'Custom route on private road — feel the real power.'],
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
    <section id="visit" class="section-pill py-20 sm:py-28 px-4 sm:px-6 lg:px-margin-desktop bg-surface relative z-10">
        <div class="max-w-container-max mx-auto grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-stretch reveal-up">
            <div class="glass-card rounded-2xl p-8 sm:p-10 flex flex-col justify-center">
                <span class="text-label-caps font-label-caps text-primary tracking-[0.2em] uppercase mb-4">Showroom</span>
                <h2 class="text-2xl sm:text-headline-sm font-headline-sm text-on-surface mb-6">Visit Our Premium Space</h2>
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
                        Book a visit
                    </a>
                    <a href="https://maps.google.com/?q=178+Đại+Mỗ+Nam+Từ+Liêm+Hà+Nội" target="_blank" rel="noopener noreferrer" class="btn-outline-gold inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm text-on-surface-variant">
                        <span class="material-symbols-outlined text-lg" aria-hidden="true">map</span>
                        Get directions
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
                <div class="absolute inset-0 bg-gradient-to-t from-surface via-transparent to-transparent pointer-events-none" aria-hidden="true"></div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="relative z-10 px-4 sm:px-6 lg:px-margin-desktop py-16 sm:py-20" aria-label="Test Drive Booking">
        <div class="max-w-container-max mx-auto reveal-up">
            <div class="relative overflow-hidden rounded-3xl border border-primary/25 bg-gradient-to-br from-surface-container via-[#1a1814] to-surface-container-lowest p-10 sm:p-14 text-center">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(233,193,118,0.12)_0%,transparent_55%)] pointer-events-none" aria-hidden="true"></div>
                <div class="relative z-10 max-w-2xl mx-auto">
                    <span class="material-symbols-outlined text-5xl text-primary mb-6" data-weight="fill" aria-hidden="true">steering_wheel_heat</span>
                    <h2 class="text-2xl sm:text-headline-sm font-headline-sm text-on-surface mb-4">Ready to Feel the Power?</h2>
                    <p class="text-on-surface-variant font-light mb-8 text-sm sm:text-base">Book an exclusive test drive — private consultation, custom route, and VIP space just for you.</p>
                    <a href="booking.php" class="btn-glow inline-flex items-center gap-2 px-10 py-4 rounded-full text-label-caps font-label-caps text-on-primary-fixed">
                        Go to Booking Page
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
                <div class="font-display-lg font-[900] text-[24px] text-on-surface whitespace-nowrap">
                    AUTO <span class="text-primary">DREAMCARS</span>
                </div>
                <p class="text-on-surface-variant text-body-md opacity-80 max-w-sm">Shaping new standards for luxury supercar services in Vietnam.</p>
            </div>

            <div>
                <h4 class="text-label-caps font-label-caps text-on-surface mb-5 uppercase tracking-widest">Navigation</h4>
                <nav class="flex flex-col gap-2.5" aria-label="Footer links">
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
                <h4 class="text-label-caps font-label-caps text-on-surface mb-5 uppercase tracking-widest">Connect</h4>
                <div class="flex flex-col gap-2.5">
                    <a class="text-on-surface-variant hover:text-primary transition-colors text-sm" href="#">Facebook</a>
                    <a class="text-on-surface-variant hover:text-primary transition-colors text-sm" href="#">Instagram</a>
                    <a class="text-on-surface-variant hover:text-primary transition-colors text-sm" href="#">YouTube</a>
                </div>
            </div>
        </div>
        <div class="max-w-container-max mx-auto mt-12 pt-8 border-t border-outline-variant/10 flex flex-col sm:flex-row justify-between items-center gap-2 text-sm text-on-surface-variant opacity-70">
            <p>&copy; <?php echo date('Y'); ?> Auto DreamCars. All rights reserved.</p>
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
                        if (el) el.textContent = `${temp}°C Hanoi`;
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
    <!-- Floating Test Drive Button -->
    <a href="booking.php" class="fixed bottom-6 right-6 sm:bottom-10 sm:right-10 z-[100] btn-glow flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 rounded-full shadow-[0_10px_30px_rgba(212,168,67,0.4)] hover:scale-110 transition-transform duration-300 group" aria-label="Đặt lịch lái thử">
        <span class="material-symbols-outlined text-on-primary-fixed text-2xl sm:text-3xl">steering_wheel_heat</span>
        <span class="absolute right-full mr-4 top-1/2 -translate-y-1/2 bg-surface-container-highest text-on-surface px-4 py-2 rounded-lg text-sm font-semibold opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap shadow-xl border border-outline-variant/20 hidden sm:block">
            Đặt Lái Thử
        </span>
    </a>

</body>
</html>
