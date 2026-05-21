<?php
// customer/about.php
require_once '../config/db.php';
session_start();
?>
<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>AutoSuperCar | Về Chúng Tôi</title>
    
    <!-- TailwindCSS & Config (Cùng hệ thống với booking.php) -->
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
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&family=Mrs+Saint+Delafield&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

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
            transition: all 0.4s ease;
        }

        .glass-card:hover {
            background: rgba(26, 26, 26, 0.6);
            border-color: rgba(233, 193, 118, 0.3);
            transform: translateY(-5px);
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

        /* Scroll Reveal Animation */
        .reveal-up {
            opacity: 0; transform: translateY(40px);
            transition: all 1s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal-up.active { opacity: 1; transform: translateY(0); }
        
        .delay-100 { transition-delay: 100ms; }
        .delay-200 { transition-delay: 200ms; }
        .delay-300 { transition-delay: 300ms; }

        /* Hero Parallax */
        .hero-section {
            height: 80vh; min-height: 600px;
            background: linear-gradient(to bottom, rgba(5,5,5,0.4) 0%, rgba(5,5,5,1) 100%),
                        url('../assets/images/cars/mercedes-s-class.jpg') no-repeat center center/cover;
            background-attachment: fixed;
            position: relative;
            display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;
        }

        .scroll-indicator {
            animation: bounce 2s infinite;
        }
        @keyframes bounce { 
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); } 
            40% { transform: translateY(-15px); } 
            60% { transform: translateY(-7px); } 
        }

        /* Watermark Text */
        .watermark-text {
            font-family: 'Playfair Display', serif;
            font-size: 15vw; font-weight: 700;
            color: rgba(255, 255, 255, 0.02);
            position: absolute; top: 10%; left: -2%;
            z-index: 0; user-select: none; pointer-events: none;
        }
    </style>
</head>
<body class="font-body-md antialiased relative">
    <div class="bg-hero-pattern"></div>
    
    <!-- Top Navigation (Shared with booking.php) -->
    <nav class="fixed top-0 left-0 w-full z-50 flex justify-between items-center px-6 lg:px-margin-desktop py-6 bg-surface/60 backdrop-blur-xl border-b border-outline-variant/10 transition-all duration-300" id="navbar">
        <a href="index.php" class="text-headline-sm font-headline-sm font-bold text-primary flex items-center gap-2 hover:opacity-80 transition-opacity">
            <span class="material-symbols-outlined text-4xl" data-weight="fill">sports_motorsports</span>
            AutoSuperCar
        </a>
        
        <div class="hidden lg:flex items-center gap-8">
            <a class="text-label-caps font-label-caps text-on-surface-variant hover:text-primary transition-colors duration-300" href="index.php">Trang Chủ</a>
            <a class="text-label-caps font-label-caps text-on-surface-variant hover:text-primary transition-colors duration-300" href="cars.php">Khám Phá Xe</a>
            <a class="text-label-caps font-label-caps text-on-surface-variant hover:text-primary transition-colors duration-300" href="compare.php">So Sánh</a>
            <a class="text-label-caps font-label-caps text-primary border-b border-primary pb-1 transition-colors duration-300" href="about.php">Giới Thiệu</a>
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

    <!-- HERO SECTION -->
    <header class="hero-section relative z-10 w-full pt-20">
        <div class="reveal-up active">
            <div class="text-label-caps font-label-caps text-primary tracking-[0.3em] uppercase mb-6">Tuyệt Tác Vượt Thời Gian</div>
            <h1 class="text-[5rem] lg:text-[7rem] font-display-lg font-bold leading-[1.1] uppercase tracking-wide bg-gradient-to-r from-white to-gray-500 bg-clip-text text-transparent mb-8">
                Định Hình<br/>Đẳng Cấp
            </h1>
            <p class="text-body-lg font-body-lg text-on-surface-variant max-w-2xl mx-auto font-light leading-relaxed">
                Hơn cả một showroom ô tô, AutoSuperCar là nơi giao thoa giữa nghệ thuật chế tác cơ khí đỉnh cao và phong cách sống thượng lưu độc bản.
            </p>
        </div>
        
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex flex-col items-center gap-3 text-on-surface-variant text-label-caps font-label-caps tracking-[0.2em] uppercase reveal-up delay-300 active">
            Khám Phá
            <span class="material-symbols-outlined text-primary scroll-indicator">south</span>
        </div>
    </header>

    <!-- STORY SECTION -->
    <section class="relative py-32 px-6 lg:px-margin-desktop bg-[#050505] overflow-hidden">
        <div class="watermark-text">HISTORY</div>
        
        <div class="max-w-container-max mx-auto relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            
            <div class="relative reveal-up">
                <!-- Encode URL path safely -->
                <img src="../assets/images/cars/Porche%20911%20turbo.jpg" alt="Showroom" class="w-full lg:w-[90%] rounded-2xl shadow-[0_30px_60px_rgba(0,0,0,0.5)]" onerror="this.src='https://images.unsplash.com/photo-1603584173870-7f23fdae1b7a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80'">
                
                <!-- Glass Badge -->
                <div class="absolute -bottom-10 right-0 lg:right-5 glass-card p-8 rounded-2xl text-center min-w-[200px]">
                    <div class="text-6xl font-display-lg font-bold text-primary leading-none counter" data-target="5">0</div>
                    <div class="text-label-caps font-label-caps tracking-widest text-on-surface-variant mt-3">NĂM KIẾN TẠO<br/>ĐỈNH CAO</div>
                </div>
            </div>
            
            <div class="lg:pl-10 mt-16 lg:mt-0 reveal-up delay-200">
                <span class="text-label-caps font-label-caps text-primary tracking-[0.2em] uppercase border-b border-primary/30 pb-2 inline-block mb-6">Câu Chuyện Thương Hiệu</span>
                <h2 class="text-headline-md font-headline-md text-on-surface leading-[1.2] mb-8">
                    Hành Trình Tới<br/>
                    <span class="text-primary italic">Biểu Tượng</span>
                </h2>
                <div class="space-y-6 text-on-surface-variant font-light text-lg leading-[1.9]">
                    <p>Khởi nguồn từ niềm đam mê mãnh liệt với những cỗ máy tốc độ và sự hoàn mỹ trong thiết kế, AutoSuperCar được thành lập với một tầm nhìn duy nhất: Trở thành điểm đến tối thượng cho giới tinh hoa yêu xe tại Việt Nam.</p>
                    <p>Chúng tôi tự hào sở hữu bộ sưu tập độc bản từ những thương hiệu danh giá bậc nhất thế giới. Mỗi chiếc xe tại showroom không chỉ là một phương tiện di chuyển, mà là một tác phẩm nghệ thuật, một bản tuyên ngôn mạnh mẽ về vị thế của chủ nhân.</p>
                </div>
                <div class="font-signature text-6xl text-white/80 mt-10">AutoSuperCar</div>
            </div>
            
        </div>
    </section>

    <!-- STATS SECTION -->
    <section class="py-24 px-6 lg:px-margin-desktop bg-surface-container-lowest border-y border-outline-variant/10 relative z-10">
        <div class="max-w-container-max mx-auto grid grid-cols-2 md:grid-cols-4 gap-12 text-center">
            <div class="reveal-up">
                <div class="flex justify-center items-end gap-1 mb-4 text-primary">
                    <span class="text-6xl font-display-lg font-bold counter" data-target="500">0</span>
                    <span class="text-5xl font-display-lg font-bold pb-1">+</span>
                </div>
                <div class="text-label-caps font-label-caps tracking-widest text-on-surface-variant">SIÊU XE ĐÃ GIAO</div>
            </div>
            <div class="reveal-up delay-100">
                <div class="flex justify-center items-end gap-1 mb-4 text-primary">
                    <span class="text-6xl font-display-lg font-bold counter" data-target="100">0</span>
                    <span class="text-5xl font-display-lg font-bold pb-1">%</span>
                </div>
                <div class="text-label-caps font-label-caps tracking-widest text-on-surface-variant">HÀI LÒNG TUYỆT ĐỐI</div>
            </div>
            <div class="reveal-up delay-200">
                <div class="flex justify-center items-end gap-1 mb-4 text-primary">
                    <span class="text-6xl font-display-lg font-bold counter" data-target="24">0</span>
                    <span class="text-5xl font-display-lg font-bold pb-1">/7</span>
                </div>
                <div class="text-label-caps font-label-caps tracking-widest text-on-surface-variant">DỊCH VỤ ĐẶC QUYỀN</div>
            </div>
            <div class="reveal-up delay-300">
                <div class="flex justify-center items-end gap-1 mb-4 text-primary">
                    <span class="text-6xl font-display-lg font-bold counter" data-target="15">0</span>
                    <span class="text-5xl font-display-lg font-bold pb-1">+</span>
                </div>
                <div class="text-label-caps font-label-caps tracking-widest text-on-surface-variant">THƯƠNG HIỆU ĐỈNH CAO</div>
            </div>
        </div>
    </section>

    <!-- CORE VALUES -->
    <section class="py-32 px-6 lg:px-margin-desktop bg-[#050505] relative z-10 overflow-hidden">
        <!-- Radial Gradient Background -->
        <div class="absolute top-0 right-0 w-1/2 h-full bg-[radial-gradient(circle_at_center,rgba(212,175,55,0.05)_0%,transparent_70%)] pointer-events-none"></div>
        
        <div class="max-w-3xl mx-auto text-center mb-20 reveal-up">
            <span class="text-label-caps font-label-caps text-primary tracking-[0.2em] uppercase border-b border-primary/30 pb-2 inline-block mb-6">Triết Lý Kinh Doanh</span>
            <h2 class="text-headline-md font-headline-md text-on-surface leading-[1.2] mb-6">Giá Trị Vượt Thời Gian</h2>
            <p class="text-body-lg text-on-surface-variant font-light">Những nguyên tắc tối thượng định hình chuẩn mực dịch vụ và cam kết của chúng tôi đối với giới tinh hoa, mang đến trải nghiệm không thể sao chép.</p>
        </div>
        
        <div class="max-w-container-max mx-auto grid grid-cols-1 md:grid-cols-3 gap-10 relative z-10">
            <!-- Card 1 -->
            <div class="glass-card p-10 rounded-2xl group reveal-up relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                <span class="material-symbols-outlined text-5xl text-primary mb-8 block" data-weight="fill">diamond</span>
                <h3 class="text-2xl font-display-lg font-semibold text-on-surface mb-4">Độc Bản & Đẳng Cấp</h3>
                <p class="text-on-surface-variant font-light leading-relaxed">Chỉ tuyển chọn và phân phối những mẫu xe xuất sắc nhất, mang tính biểu tượng và khẳng định vị thế độc tôn của chủ nhân trên mọi hành trình.</p>
            </div>
            
            <!-- Card 2 -->
            <div class="glass-card p-10 rounded-2xl group reveal-up delay-100 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                <span class="material-symbols-outlined text-5xl text-primary mb-8 block" data-weight="fill">verified_user</span>
                <h3 class="text-2xl font-display-lg font-semibold text-on-surface mb-4">Đặc Quyền Tối Thượng</h3>
                <p class="text-on-surface-variant font-light leading-relaxed">Dịch vụ chăm sóc khách hàng cá nhân hóa, không gian riêng tư tuyệt đối và những đặc quyền chỉ dành riêng cho thành viên của AutoSuperCar.</p>
            </div>
            
            <!-- Card 3 -->
            <div class="glass-card p-10 rounded-2xl group reveal-up delay-200 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                <span class="material-symbols-outlined text-5xl text-primary mb-8 block" data-weight="fill">globe</span>
                <h3 class="text-2xl font-display-lg font-semibold text-on-surface mb-4">Uy Tín Toàn Cầu</h3>
                <p class="text-on-surface-variant font-light leading-relaxed">Minh bạch trong mọi giao dịch, cam kết chất lượng tuyệt đối với chế độ bảo hành và bảo dưỡng theo đúng tiêu chuẩn khắt khe nhất của hãng.</p>
            </div>
        </div>
    </section>

    <!-- FOOTER (Shared with booking.php) -->
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
                <p class="text-on-surface-variant mt-4 font-bold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">call</span> +84 (0) 356 827 852
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
            // 1. Fetch Weather
            fetchWeather();
            
            // 2. Navbar Background Transition
            const navbar = document.getElementById('navbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('bg-surface/95', 'shadow-lg');
                    navbar.classList.remove('bg-surface/60');
                } else {
                    navbar.classList.remove('bg-surface/95', 'shadow-lg');
                    navbar.classList.add('bg-surface/60');
                }
            });

            // 3. Scroll Reveal Animation & Counters
            const revealElements = document.querySelectorAll('.reveal-up');
            
            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        
                        // Kích hoạt counter nếu element chứa counter
                        const counters = entry.target.querySelectorAll('.counter');
                        if (counters.length > 0) {
                            startCounters(counters);
                        }
                        
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15, rootMargin: "0px 0px -50px 0px" });

            revealElements.forEach(el => revealObserver.observe(el));
        });

        // Function: Fetch Weather
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

        // Function: Counter Animation
        function startCounters(counters) {
            counters.forEach(counter => {
                const target = +counter.getAttribute('data-target') || 0; 
                const duration = 2000; // 2 seconds
                const increment = target / (duration / 16); // ~60fps
                let current = 0;

                const updateCounter = () => {
                    current += increment;
                    if (current < target) {
                        counter.innerText = Math.ceil(current);
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCounter();
            });
        }
    </script>
</body>
</html>
