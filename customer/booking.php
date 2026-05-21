<?php
// customer/booking.php
require_once '../config/db.php';

$currentPage = 'booking';
$navItems = [
    ['id' => 'home',    'label' => 'Trang Chủ',    'href' => 'index.php'],
    ['id' => 'cars',    'label' => 'Khám Phá Xe',  'href' => 'cars.php'],
    ['id' => 'compare', 'label' => 'So Sánh',      'href' => 'compare.php'],
    ['id' => 'about',   'label' => 'Giới Thiệu',  'href' => 'about.php'],
    ['id' => 'booking', 'label' => 'Lái Thử',     'href' => 'booking.php'],
];

$user_name = '';
$user_email = '';
$user_phone = '';

if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT full_name, email, phone FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        if ($user) {
            $user_name = $user['full_name'] ?? '';
            $user_email = $user['email'] ?? '';
            $user_phone = $user['phone'] ?? '';
        }
    } catch (PDOException $e) {
        // Fallback or ignore if columns are different
    }
}
?>
<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>AutoSuperCar | Đặt Lịch Lái Thử</title>
    
    <!-- TailwindCSS & Config -->
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
                        "surface-container-high": "#2a2a2a",
                        "on-tertiary": "#2f3131",
                        "on-background": "#e5e2e1",
                        "tertiary": "#c6c6c7",
                        "on-tertiary-container": "#393b3b",
                        "background": "#131313",
                        "secondary-container": "#474746",
                        "primary-fixed": "#ffdea5",
                        "surface-container-lowest": "#0e0e0e",
                        "secondary-fixed": "#e5e2e1",
                        "primary-fixed-dim": "#e9c176",
                        "tertiary-fixed": "#e2e2e2",
                        "outline": "#9a8f80",
                        "tertiary-container": "#a4a5a5",
                        "surface-container": "#201f1f",
                        "on-surface": "#e5e2e1",
                        "on-secondary-container": "#b7b5b4",
                        "on-tertiary-fixed-variant": "#454747",
                        "on-secondary-fixed-variant": "#474746",
                        "error": "#ffb4ab",
                        "on-error": "#690005",
                        "surface-dim": "#131313",
                        "on-primary": "#412d00",
                        "inverse-on-surface": "#313030",
                        "on-primary-fixed-variant": "#5d4201",
                        "surface": "#131313",
                        "on-primary-container": "#4e3700",
                        "surface-container-highest": "#353534",
                        "on-tertiary-fixed": "#1a1c1c",
                        "primary-container": "#c5a059",
                        "primary": "#e9c176",
                        "outline-variant": "#4e4639",
                        "error-container": "#93000a",
                        "on-error-container": "#ffdad6",
                        "on-secondary": "#313030",
                        "secondary-fixed-dim": "#c8c6c5",
                        "on-surface-variant": "#d1c5b4",
                        "surface-container-low": "#1c1b1b",
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
                        "display-lg": ["Playfair Display"],
                        "label-caps": ["Inter"],
                        "headline-md": ["Playfair Display"],
                        "headline-sm": ["Playfair Display"],
                        "body-md": ["Inter"],
                        "body-lg": ["Inter"]
                    },
                    "fontSize": {
                        "label-caps": ["12px", {"lineHeight": "16px", "letterSpacing": "0.15em", "fontWeight": "600"}],
                        "headline-md": ["42px", {"lineHeight": "52px", "fontWeight": "600"}],
                        "display-lg": ["80px", {"lineHeight": "90px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "headline-sm": ["32px", {"lineHeight": "40px", "fontWeight": "500"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}]
                    }
                },
            },
        }
    </script>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { background-color: #050505; color: #e5e2e1; overflow-x: hidden; }
        
        .bg-hero-pattern {
            position: absolute; top: 0; left: 0; width: 100%; height: 100vh; z-index: 0;
            background: radial-gradient(circle at 70% 30%, rgba(233, 193, 118, 0.05) 0%, rgba(5, 5, 5, 1) 70%);
        }

        .glass-card {
            background: rgba(26, 26, 26, 0.4);
            backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px);
            border: 1px solid rgba(233, 193, 118, 0.15);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.05);
            overflow: visible;
        }

        .input-line {
            background: transparent; border: none; border-bottom: 1px solid rgba(154, 143, 128, 0.3);
            border-radius: 0; padding: 12px 0; transition: all 0.3s ease;
        }
        
        .input-line:focus {
            outline: none; box-shadow: none; border-bottom: 1px solid #e9c176;
            box-shadow: 0 4px 10px -6px rgba(233, 193, 118, 0.5);
        }

        /* Hide default calendar/time icons */
        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="time"]::-webkit-calendar-picker-indicator {
            opacity: 0; position: absolute; right: 0; top: 0; width: 100%; height: 100%; cursor: pointer;
        }

        .btn-glow {
            background: linear-gradient(135deg, #c5a059 0%, #775a19 100%);
            position: relative; z-index: 1; overflow: hidden;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .btn-glow::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, #e9c176 0%, #b38b40 100%);
            z-index: -1; opacity: 0; transition: opacity 0.4s ease;
        }
        
        .btn-glow:hover::before { opacity: 1; }

        .btn-glow::after {
            content: ''; position: absolute; top: -5px; left: -5px; right: -5px; bottom: -5px;
            z-index: -2; background: #e9c176; filter: blur(20px); opacity: 0.3;
            animation: pulse-glow 3s infinite alternate;
        }

        @keyframes pulse-glow {
            0% { opacity: 0.2; transform: scale(0.98); }
            100% { opacity: 0.5; transform: scale(1.02); }
        }

        .reveal-up {
            opacity: 0; transform: translateY(30px);
            animation: revealUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes revealUp { to { opacity: 1; transform: translateY(0); } }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }

        .parallax-bg {
            position: absolute; top: 0; right: 0; width: 70%; height: 100vh; z-index: 1;
            display: flex; align-items: center; justify-content: flex-end; pointer-events: none;
        }

        .parallax-bg img {
            width: 120%; max-width: none; height: auto; transform: translateX(10%);
            mask-image: linear-gradient(to left, black 50%, transparent 100%);
            -webkit-mask-image: linear-gradient(to left, black 50%, transparent 100%);
        }
        
        @media (max-width: 1024px) {
            .parallax-bg { position: relative; width: 100%; height: 50vh; margin-top: 80px; justify-content: center; }
            .parallax-bg img { width: 100%; transform: translateX(0); mask-image: none; -webkit-mask-image: none; }
        }

        /* Custom SweetAlert2 Theme */
        .swal-premium-popup { background: #131313 !important; border: 1px solid rgba(197, 160, 89, 0.5) !important; color: #e5e2e1 !important; border-radius: 16px !important; box-shadow: 0 20px 40px rgba(0,0,0,0.5) !important;}
        .swal-premium-title { color: #e9c176 !important; font-family: 'Playfair Display', serif !important; letter-spacing: 1px; }
        .swal-premium-confirm { background: linear-gradient(135deg, #c5a059 0%, #775a19 100%) !important; color: #261900 !important; font-weight: bold !important; font-family: 'Inter', sans-serif !important; border-radius: 8px !important; padding: 12px 30px !important; text-transform: uppercase; letter-spacing: 1px; }
        
        /* Multi-step Form & Interactions */
        .step { display: none; opacity: 0; transition: opacity 0.5s ease-in-out; }
        .step.active { display: block; opacity: 1; }
        .progress-bar { height: 4px; background: rgba(255,255,255,0.1); border-radius: 2px; overflow: hidden; position: relative; margin-bottom: 2rem; }
        .progress-fill { position: absolute; top: 0; left: 0; height: 100%; background: #e9c176; transition: width 0.4s ease; width: 33.33%; }
        
        /* Card (no 3D transform to avoid stacking context issues with dropdown) */
        .tilt-card { transition: box-shadow 0.3s ease; }
        .tilt-card:hover { box-shadow: 0 35px 70px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.08); }
        .tilt-content { /* no transform */ }
        
        /* Custom Select */
        .custom-select-wrapper { position: relative; user-select: none; width: 100%; }
        .custom-select { position: relative; display: flex; flex-direction: column; }
        .custom-select__trigger {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 0; font-size: 1.125rem; color: #e5e2e1;
            border-bottom: 1px solid rgba(154, 143, 128, 0.3); cursor: pointer; transition: all 0.3s;
        }
        .custom-select.open .custom-select__trigger { border-bottom-color: #e9c176; }
        .custom-options {
            position: absolute; display: block; top: 100%; left: 0; right: 0;
            background: rgba(20, 20, 20, 0.98); backdrop-filter: blur(10px);
            border: 1px solid rgba(233, 193, 118, 0.2); border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            opacity: 0; visibility: hidden; pointer-events: none;
            transition: opacity 0.3s, visibility 0.3s; z-index: 9999; max-height: 250px; overflow-y: auto; margin-top: 5px;
        }
        .custom-options::-webkit-scrollbar { width: 6px; }
        .custom-options::-webkit-scrollbar-thumb { background: #e9c176; border-radius: 3px; }
        .custom-select.open .custom-options { opacity: 1; visibility: visible; pointer-events: all; }
        .custom-option { padding: 12px 16px; cursor: pointer; transition: background 0.2s; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .custom-option:last-child { border-bottom: none; }
        .custom-option:hover, .custom-option.selected { background: rgba(233, 193, 118, 0.1); color: #e9c176; }
        
        /* Particles */
        #particles-js { position: absolute; width: 100%; height: 100%; top: 0; left: 0; z-index: 0; pointer-events: none; }
    </style>
</head>
<body class="font-body-md antialiased relative">
    <div class="bg-hero-pattern"></div>
    <div id="particles-js"></div>
    
    <!-- Top Navigation -->
    <nav class="fixed top-0 left-0 w-full z-50 flex justify-between items-center px-6 lg:px-margin-desktop py-6 bg-surface/60 backdrop-blur-xl border-b border-outline-variant/10">
        <a href="index.php" class="text-headline-sm font-headline-sm font-bold text-primary flex items-center gap-2 hover:opacity-80 transition-opacity">
            <span class="material-symbols-outlined text-4xl" data-weight="fill">sports_motorsports</span>
            AutoSuperCar
        </a>
        
        <div class="hidden lg:flex items-center gap-1">
            <?php foreach ($navItems as $item): ?>
                <a
                    href="<?php echo htmlspecialchars($item['href']); ?>"
                    class="px-3 py-2 rounded-full text-label-caps font-label-caps transition-colors duration-300 <?php echo $currentPage === $item['id'] ? 'text-primary bg-primary/12 ring-1 ring-primary/20' : 'text-on-surface-variant hover:text-primary hover:bg-primary/8'; ?>"
                    <?php echo $currentPage === $item['id'] ? 'aria-current="page"' : ''; ?>
                ><?php echo htmlspecialchars($item['label']); ?></a>
            <?php endforeach; ?>
        </div>
        
        <div class="flex items-center gap-6">
            <!-- Weather Widget -->
            <div class="hidden md:flex items-center gap-2 text-sm text-on-surface-variant border-r border-outline-variant/30 pr-6">
                <span class="material-symbols-outlined text-primary text-xl">partly_cloudy_day</span>
                <span id="weatherTemp">--°C</span>
            </div>

            <!-- User Menu -->
            <div class="hidden sm:flex items-center gap-4 text-sm font-medium">
                <?php if (isset($_SESSION['admin_id']) || isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center gap-2 text-primary">
                        <span class="material-symbols-outlined">account_circle</span>
                        <?php echo htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['user_name'] ?? 'Thành viên'); ?>
                    </div>
                    <a href="../logout.php" title="Đăng xuất" class="text-error hover:text-red-400 transition-colors flex items-center"><span class="material-symbols-outlined text-lg">logout</span></a>
                <?php else: ?>
                    <a href="../login.php" class="text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1"><span class="material-symbols-outlined text-lg">login</span> Đăng nhập</a>
                <?php endif; ?>
            </div>

            <a href="booking.php" class="hidden md:inline-block bg-primary text-on-primary-fixed border border-transparent px-8 py-3 text-label-caps font-label-caps rounded-full hover:bg-surface-container-high hover:text-primary hover:border-outline-variant/30 transition-all duration-300 shadow-[0_0_15px_rgba(233,193,118,0.2)]">
                Lái Thử
            </a>
            
            <!-- Mobile Menu Icon -->
            <button class="lg:hidden text-on-surface-variant">
                <span class="material-symbols-outlined text-3xl">menu</span>
            </button>
        </div>
    </nav>

    <!-- Parallax Car Background -->
    <div class="parallax-bg" id="parallaxContainer">
        <img alt="Supercar" id="parallaxImg" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCBeaC8cKzyY9mvAtq-adPrl_H3bj2iyZRQVRVMUit_BZ1naSPWfQIpUDkRwnW7vPHsYDvT2UwIa9C4nyVd3sMZrTHVcC2N0x7LAHn3KuNQMWHw0Ngx5uhQ7lc5dU0ymX8rfMJvG_YNWMlm2bBw5Cisfj46WQTyEJuaTFIVGHGzOzYaS9bKRp0KZzMlpnIyVlyFJsML4LFbgEH7MPZdbkwvD2WlWV5VgiwgS1mfqbjzO_TEXmAzIyZITdfVbrWCB7uBW5AbCRxysiXn"/>
    </div>

    <!-- Main Content -->
    <main class="relative z-10 min-h-screen pt-32 lg:pt-20 flex items-center px-6 lg:px-margin-desktop max-w-container-max mx-auto">
        <div class="mt-20 mb-20 w-full grid grid-cols-1 lg:grid-cols-12 gap-gutter items-center">
            
            <!-- Left Content: Title & Privileges -->
            <div class="lg:col-span-5 space-y-12 reveal-up pt-10 lg:pt-0">
                <div>
                    <h1 class="text-display-lg font-display-lg text-primary leading-tight mb-4">
                        Exclusive<br/><span class="text-on-surface">Experience</span>
                    </h1>
                    <p class="text-body-lg font-body-lg text-on-surface-variant max-w-md">
                        Đặt trước dịch vụ lái thử để cảm nhận trọn vẹn uy lực và đẳng cấp của AutoSuperCar.
                    </p>
                </div>
                
                <div class="space-y-6">
                    <h3 class="text-label-caps font-label-caps text-primary tracking-widest uppercase border-b border-outline-variant/30 pb-2 inline-block">Đặc Quyền Thượng Lưu</h3>
                    <ul class="space-y-6 text-on-surface-variant">
                        <li class="flex items-start gap-4 group">
                            <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center border border-primary/20 group-hover:border-primary/50 transition-colors shrink-0">
                                <span class="material-symbols-outlined text-primary text-xl" data-weight="fill">diamond</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-on-surface mb-1">Tư vấn chuyên sâu 1:1</h4>
                                <p class="text-sm opacity-70">Chuyên gia sản phẩm riêng biệt hỗ trợ giải đáp mọi thắc mắc.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4 group">
                            <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center border border-primary/20 group-hover:border-primary/50 transition-colors shrink-0">
                                <span class="material-symbols-outlined text-primary text-xl" data-weight="fill">route</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-on-surface mb-1">Lộ trình thử xe tùy chọn</h4>
                                <p class="text-sm opacity-70">Thiết kế cung đường riêng để thử nghiệm đa dạng chế độ lái.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4 group">
                            <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center border border-primary/20 group-hover:border-primary/50 transition-colors shrink-0">
                                <span class="material-symbols-outlined text-primary text-xl" data-weight="fill">wine_bar</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-on-surface mb-1">Dịch vụ VIP Lounge</h4>
                                <p class="text-sm opacity-70">Thưởng thức champagne và đồ uống cao cấp trước khi trải nghiệm.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Right Content: Booking Form Card -->
            <div class="lg:col-span-6 lg:col-start-7 reveal-up delay-200 mt-12 lg:mt-0 mb-20 lg:mb-0 perspective-1000">
                <div class="glass-card p-8 lg:p-14 rounded-3xl relative tilt-card" id="bookingCard">
                    <div class="tilt-content">
                        <!-- Decorative corner accent -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 blur-3xl rounded-full translate-x-1/2 -translate-y-1/2"></div>
                        
                        <div class="flex justify-between items-end mb-8 relative z-10">
                            <h2 class="text-headline-sm font-headline-sm text-on-surface">Đặt Lịch Lái Thử</h2>
                            <span class="text-primary font-label-caps tracking-widest text-sm" id="stepIndicator">BƯỚC 1/3</span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="progress-bar relative z-10">
                            <div class="progress-fill" id="progressFill"></div>
                        </div>
                        
                        <form id="formBooking" class="relative z-10 min-h-[350px]">
                            <!-- STEP 1: SELECT CAR -->
                            <div class="step active" id="step1">
                                <h3 class="text-xl text-on-surface-variant mb-6 font-display-lg">Lựa chọn Siêu xe</h3>
                                <div class="custom-select-wrapper" id="carSelectWrapper">
                                    <div class="custom-select">
                                        <div class="custom-select__trigger">
                                            <span id="carSelectDisplay">-- Đang tải danh sách xe --</span>
                                            <span class="material-symbols-outlined text-on-surface-variant transition-transform" id="carSelectIcon">expand_more</span>
                                        </div>
                                        <div class="custom-options" id="carOptions">
                                            <!-- Injected by JS -->
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="car_id" id="car_id" required>
                                
                                <div class="pt-10 flex justify-end">
                                    <button type="button" id="btnNext1" class="btn-glow px-8 py-3 text-label-caps font-label-caps text-on-primary-fixed rounded-xl font-bold tracking-widest">
                                        TIẾP TỤC <span class="material-symbols-outlined align-middle ml-1 text-sm">arrow_forward</span>
                                    </button>
                                </div>
                            </div>

                            <!-- STEP 2: DATE & TIME -->
                            <div class="step" id="step2">
                                <h3 class="text-xl text-on-surface-variant mb-6 font-display-lg">Thời gian Dự kiến</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="relative group">
                                        <input name="preferred_date" id="preferred_date" class="w-full bg-transparent text-on-surface input-line" type="date" required />
                                        <span class="absolute left-0 -top-6 text-xs text-on-surface-variant tracking-wider uppercase">Ngày dự kiến *</span>
                                        <span class="material-symbols-outlined absolute right-0 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none group-focus-within:text-primary transition-colors">calendar_today</span>
                                    </div>
                                    <div class="relative group">
                                        <input name="preferred_time" id="preferred_time" class="w-full bg-transparent text-on-surface input-line" type="time" required />
                                        <span class="absolute left-0 -top-6 text-xs text-on-surface-variant tracking-wider uppercase">Giờ dự kiến *</span>
                                        <span class="material-symbols-outlined absolute right-0 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none group-focus-within:text-primary transition-colors">schedule</span>
                                    </div>
                                </div>
                                
                                <div class="pt-10 flex justify-between items-center mt-4">
                                    <button type="button" id="btnPrev1" class="text-on-surface-variant hover:text-primary transition-colors flex items-center font-label-caps tracking-widest text-sm">
                                        <span class="material-symbols-outlined mr-1 text-sm">arrow_back</span> QUAY LẠI
                                    </button>
                                    <button type="button" id="btnNext2" class="btn-glow px-8 py-3 text-label-caps font-label-caps text-on-primary-fixed rounded-xl font-bold tracking-widest">
                                        TIẾP TỤC <span class="material-symbols-outlined align-middle ml-1 text-sm">arrow_forward</span>
                                    </button>
                                </div>
                            </div>

                            <!-- STEP 3: PERSONAL INFO -->
                            <div class="step" id="step3">
                                <h3 class="text-xl text-on-surface-variant mb-6 font-display-lg">Thông tin Cá nhân</h3>
                                <div class="space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        <div class="relative group">
                                            <input name="full_name" class="w-full bg-transparent text-on-surface input-line peer placeholder-transparent" id="fullname" placeholder="Họ & Tên" type="text" value="<?php echo htmlspecialchars($user_name); ?>" required />
                                            <label class="absolute left-0 -top-3.5 text-sm text-on-surface-variant transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-primary pointer-events-none" for="fullname">Họ & Tên *</label>
                                        </div>
                                        <div class="relative group">
                                            <input name="phone" class="w-full bg-transparent text-on-surface input-line peer placeholder-transparent" id="phone" placeholder="Số Điện Thoại" type="tel" value="<?php echo htmlspecialchars($user_phone); ?>" required />
                                            <label class="absolute left-0 -top-3.5 text-sm text-on-surface-variant transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-primary pointer-events-none" for="phone">Số Điện Thoại *</label>
                                        </div>
                                    </div>
                                    <div class="relative group">
                                        <input name="email" class="w-full bg-transparent text-on-surface input-line peer placeholder-transparent" id="email" placeholder="Email" type="email" value="<?php echo htmlspecialchars($user_email); ?>" />
                                        <label class="absolute left-0 -top-3.5 text-sm text-on-surface-variant transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-primary pointer-events-none" for="email">Email</label>
                                    </div>
                                    <div class="relative group mt-8">
                                        <textarea name="message" id="message" class="w-full bg-transparent text-on-surface input-line peer placeholder-transparent" rows="2" placeholder="Yêu cầu bổ sung (Tùy chọn)"></textarea>
                                        <label class="absolute left-0 -top-3.5 text-sm text-on-surface-variant transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-focus:-top-3.5 peer-focus:text-sm peer-focus:text-primary pointer-events-none" for="message">Yêu cầu bổ sung (Tùy chọn)</label>
                                    </div>
                                </div>
                                
                                <div class="pt-10 flex justify-between items-center mt-4">
                                    <button type="button" id="btnPrev2" class="text-on-surface-variant hover:text-primary transition-colors flex items-center font-label-caps tracking-widest text-sm">
                                        <span class="material-symbols-outlined mr-1 text-sm">arrow_back</span> QUAY LẠI
                                    </button>
                                    <button id="btnSubmitBooking" class="btn-glow px-8 py-3 text-label-caps font-label-caps text-on-primary-fixed rounded-xl font-bold tracking-widest shadow-2xl" type="submit">
                                        HOÀN TẤT
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full py-16 px-6 lg:px-margin-desktop border-t border-outline-variant/10 bg-surface-container-lowest relative z-20">
        <div class="max-w-container-max mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter">
            <div class="space-y-6">
                <div class="text-headline-sm font-headline-sm font-bold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-3xl" data-weight="fill">sports_motorsports</span>
                    AutoSuperCar
                </div>
                <p class="text-on-surface-variant text-body-md opacity-70 max-w-xs">Định hình tiêu chuẩn mới cho dịch vụ siêu xe đẳng cấp tại Việt Nam.</p>
            </div>
            
            <div>
                <h4 class="text-label-caps font-label-caps text-on-surface mb-6 uppercase tracking-widest">Showroom</h4>
                <p class="text-on-surface-variant hover:text-primary transition-colors cursor-pointer leading-relaxed">
                    178 Đại Mỗ<br/>
                    Nam Từ Liêm, Hà Nội<br/>
                    Việt Nam
                </p>
                <p class="text-on-surface-variant mt-4 font-bold text-primary">
                    <span class="material-symbols-outlined text-sm align-middle mr-1">call</span> +84 (0) 356 827 852
                </p>
            </div>
            
            <div>
                <h4 class="text-label-caps font-label-caps text-on-surface mb-6 uppercase tracking-widest">Kết Nối</h4>
                <div class="flex flex-col gap-3">
                    <a class="text-on-surface-variant hover:text-primary transition-colors flex items-center gap-2" href="#">
                        <span class="w-1 h-1 bg-primary rounded-full opacity-0 hover:opacity-100 transition-opacity"></span> Facebook
                    </a>
                    <a class="text-on-surface-variant hover:text-primary transition-colors flex items-center gap-2" href="#">
                        <span class="w-1 h-1 bg-primary rounded-full opacity-0 hover:opacity-100 transition-opacity"></span> Instagram
                    </a>
                    <a class="text-on-surface-variant hover:text-primary transition-colors flex items-center gap-2" href="#">
                        <span class="w-1 h-1 bg-primary rounded-full opacity-0 hover:opacity-100 transition-opacity"></span> YouTube
                    </a>
                </div>
            </div>
            
            <div>
                <h4 class="text-label-caps font-label-caps text-on-surface mb-6 uppercase tracking-widest">Pháp Lý</h4>
                <div class="flex flex-col gap-3">
                    <a class="text-on-surface-variant hover:text-primary transition-colors" href="#">Điều khoản sử dụng</a>
                    <a class="text-on-surface-variant hover:text-primary transition-colors" href="#">Chính sách bảo mật</a>
                </div>
            </div>
        </div>
        <div class="max-w-container-max mx-auto mt-16 pt-8 border-t border-outline-variant/10 flex flex-col md:flex-row justify-between items-center text-sm text-on-surface-variant opacity-60">
            <p>&copy; <?php echo date('Y'); ?> AutoSuperCar. All rights reserved.</p>
            <p class="mt-2 md:mt-0">Designed for Excellence</p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ============================
            // 1. THIẾT LẬP NGÀY TỐI THIỂU
            // ============================
            const dateInput = document.querySelector('input[name="preferred_date"]');
            if (dateInput) {
                const today = new Date().toISOString().split('T')[0];
                dateInput.setAttribute('min', today);
            }

            fetchWeather();
            loadCars();

            // ============================
            // 2. MULTI-STEP FORM NAVIGATION
            // ============================
            let currentStep = 1;
            const totalSteps = 3;
            const progressFill = document.getElementById('progressFill');
            const stepIndicator = document.getElementById('stepIndicator');

            function goToStep(step) {
                // Ẩn step hiện tại
                const current = document.getElementById('step' + currentStep);
                if (current) {
                    current.classList.remove('active');
                }
                // Hiện step mới
                currentStep = step;
                const next = document.getElementById('step' + currentStep);
                if (next) {
                    // Delay nhỏ để CSS transition hoạt động
                    setTimeout(() => next.classList.add('active'), 50);
                }
                // Cập nhật thanh tiến trình
                if (progressFill) {
                    progressFill.style.width = ((currentStep / totalSteps) * 100) + '%';
                }
                if (stepIndicator) {
                    stepIndicator.textContent = 'BƯỚC ' + currentStep + '/' + totalSteps;
                }
            }

            // Nút TIẾP TỤC Bước 1 → 2
            const btnNext1 = document.getElementById('btnNext1');
            if (btnNext1) {
                btnNext1.addEventListener('click', function() {
                    const carId = document.getElementById('car_id').value;
                    if (!carId) {
                        Swal.fire({
                            icon: 'warning', title: 'Chưa chọn xe',
                            text: 'Vui lòng chọn một dòng xe trước khi tiếp tục.',
                            customClass: { popup: 'swal-premium-popup', title: 'swal-premium-title', confirmButton: 'swal-premium-confirm' },
                            buttonsStyling: false
                        });
                        return;
                    }
                    goToStep(2);
                });
            }

            // Nút TIẾP TỤC Bước 2 → 3
            const btnNext2 = document.getElementById('btnNext2');
            if (btnNext2) {
                btnNext2.addEventListener('click', function() {
                    const date = document.getElementById('preferred_date').value;
                    const time = document.getElementById('preferred_time').value;
                    if (!date || !time) {
                        Swal.fire({
                            icon: 'warning', title: 'Thiếu thông tin',
                            text: 'Vui lòng chọn ngày và giờ dự kiến.',
                            customClass: { popup: 'swal-premium-popup', title: 'swal-premium-title', confirmButton: 'swal-premium-confirm' },
                            buttonsStyling: false
                        });
                        return;
                    }
                    goToStep(3);
                });
            }

            // Nút QUAY LẠI Bước 2 → 1
            const btnPrev1 = document.getElementById('btnPrev1');
            if (btnPrev1) {
                btnPrev1.addEventListener('click', function() { goToStep(1); });
            }

            // Nút QUAY LẠI Bước 3 → 2
            const btnPrev2 = document.getElementById('btnPrev2');
            if (btnPrev2) {
                btnPrev2.addEventListener('click', function() { goToStep(2); });
            }

            // ============================
            // 3. CUSTOM SELECT DROPDOWN
            // ============================
            const selectWrapper = document.getElementById('carSelectWrapper');
            const customSelect = selectWrapper ? selectWrapper.querySelector('.custom-select') : null;
            const selectTrigger = selectWrapper ? selectWrapper.querySelector('.custom-select__trigger') : null;
            const optionsContainer = document.getElementById('carOptions');
            const selectDisplay = document.getElementById('carSelectDisplay');
            const selectIcon = document.getElementById('carSelectIcon');
            const hiddenCarInput = document.getElementById('car_id');

            if (selectTrigger && customSelect) {
                // Mở/đóng dropdown khi click vào trigger
                selectTrigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    customSelect.classList.toggle('open');
                    if (selectIcon) {
                        selectIcon.style.transform = customSelect.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0)';
                    }
                });

                // Đóng dropdown khi click ra ngoài
                document.addEventListener('click', function(e) {
                    if (!selectWrapper.contains(e.target)) {
                        customSelect.classList.remove('open');
                        if (selectIcon) selectIcon.style.transform = 'rotate(0)';
                    }
                });
            }

            // ============================
            // 4. PARALLAX EFFECT
            // ============================
            const img = document.getElementById('parallaxImg');

            document.addEventListener('mousemove', (e) => {
                if (window.innerWidth > 1024 && img) {
                    const xAxis = (window.innerWidth / 2 - e.pageX) / 40;
                    const yAxis = (window.innerHeight / 2 - e.pageY) / 40;
                    img.style.transform = `translate(calc(10% + ${xAxis}px), ${yAxis}px) scale(1.05)`;
                }
            });

            document.addEventListener('mouseleave', () => {
                if (window.innerWidth > 1024 && img) {
                    img.style.transform = `translate(10%, 0) scale(1)`;
                    img.style.transition = 'transform 0.5s ease-out';
                }
            });

            document.addEventListener('mouseenter', () => {
                if (img) img.style.transition = 'none';
            });

            // ============================
            // 5. FORM SUBMIT
            // ============================
            const form = document.getElementById('formBooking');
            if (form) {
                form.addEventListener('submit', submitBooking);
            }
        });

        // ============================
        // FETCH WEATHER
        // ============================
        function fetchWeather() {
            fetch('../api/get_weather.php')
                .then(response => response.json())
                .then(data => {
                    if (data && data.current_weather) {
                        const temp = Math.round(data.current_weather.temperature);
                        document.getElementById('weatherTemp').innerHTML = `${temp}°C Hà Nội`;
                    }
                })
                .catch(error => console.error('Lỗi khi tải thời tiết:', error));
        }

        // ============================
        // LOAD CARS → CUSTOM SELECT
        // ============================
        function loadCars() {
            const optionsContainer = document.getElementById('carOptions');
            const selectDisplay = document.getElementById('carSelectDisplay');
            const hiddenCarInput = document.getElementById('car_id');
            const customSelect = document.querySelector('#carSelectWrapper .custom-select');
            const selectIcon = document.getElementById('carSelectIcon');

            fetch('../api/get_cars.php?limit=100')
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success' && res.data && res.data.length > 0) {
                        optionsContainer.innerHTML = '';
                        selectDisplay.textContent = '-- Lựa chọn dòng xe bạn muốn --';

                        res.data.forEach(car => {
                            const name = car.brand_name ? `${car.brand_name} ${car.model_name}` : car.model_name;
                            const option = document.createElement('div');
                            option.classList.add('custom-option');
                            option.dataset.value = car.id;
                            option.textContent = name;

                            option.addEventListener('click', function() {
                                // Cập nhật giá trị đã chọn
                                selectDisplay.textContent = name;
                                hiddenCarInput.value = car.id;

                                // Đánh dấu option được chọn
                                optionsContainer.querySelectorAll('.custom-option').forEach(o => o.classList.remove('selected'));
                                option.classList.add('selected');

                                // Đóng dropdown
                                if (customSelect) customSelect.classList.remove('open');
                                if (selectIcon) selectIcon.style.transform = 'rotate(0)';
                            });

                            optionsContainer.appendChild(option);
                        });
                    } else {
                        selectDisplay.textContent = 'Không tải được danh sách xe';
                    }
                })
                .catch(err => {
                    console.error('Lỗi khi tải danh sách xe:', err);
                    if (selectDisplay) selectDisplay.textContent = 'Lỗi kết nối';
                });
        }

        // ============================
        // SUBMIT BOOKING
        // ============================
        function submitBooking(e) {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('btnSubmitBooking');
            const originalText = btn.innerHTML;

            // Kiểm tra các trường bắt buộc
            const carId = document.getElementById('car_id').value;
            const fullName = document.getElementById('fullname').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const preferredDate = document.getElementById('preferred_date').value;
            const preferredTime = document.getElementById('preferred_time').value;

            if (!carId || !fullName || !phone || !preferredDate || !preferredTime) {
                Swal.fire({
                    icon: 'warning', title: 'Thiếu thông tin',
                    text: 'Vui lòng điền đầy đủ các trường bắt buộc (*) trước khi gửi.',
                    customClass: { popup: 'swal-premium-popup', title: 'swal-premium-title', confirmButton: 'swal-premium-confirm' },
                    buttonsStyling: false
                });
                return;
            }

            const data = {
                car_id: carId,
                full_name: fullName,
                phone: phone,
                email: document.getElementById('email').value.trim(),
                preferred_date: preferredDate,
                preferred_time: preferredTime,
                message: document.getElementById('message').value.trim()
            };

            // Trạng thái Loading
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin mr-2">autorenew</span> ĐANG XỬ LÝ...';
            btn.style.pointerEvents = 'none';
            btn.classList.add('opacity-80');

            fetch('../api/post_booking.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(res => {
                btn.innerHTML = originalText;
                btn.style.pointerEvents = 'auto';
                btn.classList.remove('opacity-80');

                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Đăng Ký Thành Công',
                        text: res.message || 'Chuyên viên của chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất.',
                        customClass: { popup: 'swal-premium-popup', title: 'swal-premium-title', confirmButton: 'swal-premium-confirm' },
                        buttonsStyling: false
                    });

                    // Reset form nhưng giữ thông tin cá nhân từ session
                    document.getElementById('preferred_date').value = '';
                    document.getElementById('preferred_time').value = '';
                    document.getElementById('message').value = '';
                    document.getElementById('car_id').value = '';
                    const selectDisplay = document.getElementById('carSelectDisplay');
                    if (selectDisplay) selectDisplay.textContent = '-- Lựa chọn dòng xe bạn muốn --';
                    document.querySelectorAll('.custom-option').forEach(o => o.classList.remove('selected'));

                } else {
                    Swal.fire({
                        icon: 'error', title: 'Rất tiếc...',
                        text: res.message || 'Có lỗi xảy ra, vui lòng thử lại.',
                        customClass: { popup: 'swal-premium-popup', title: 'swal-premium-title', confirmButton: 'swal-premium-confirm' },
                        buttonsStyling: false
                    });
                }
            })
            .catch(error => {
                btn.innerHTML = originalText;
                btn.style.pointerEvents = 'auto';
                btn.classList.remove('opacity-80');

                Swal.fire({
                    icon: 'error', title: 'Lỗi Kết Nối',
                    text: 'Không thể kết nối tới máy chủ. Vui lòng thử lại sau.',
                    customClass: { popup: 'swal-premium-popup', title: 'swal-premium-title', confirmButton: 'swal-premium-confirm' },
                    buttonsStyling: false
                });
                console.error('Lỗi khi gửi form:', error);
            });
        }
    </script>
</body>
</html>
