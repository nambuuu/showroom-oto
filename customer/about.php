<?php
// customer/about.php
require_once '../config/db.php';
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoSuperCar | Về Chúng Tôi</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* ==========================================
           PREMIUM DESIGN SYSTEM - ABOUT PAGE
           ========================================== */
        :root {
            /* Colors */
            --bg-primary: #0a0a0a;
            --bg-secondary: #121212;
            --bg-surface: #1a1a1a;
            --border-color: rgba(255, 255, 255, 0.08);
            
            --gold-primary: #d4af37;
            --gold-light: #f3e5ab;
            --gold-dark: #aa8623;
            --gold-glow: rgba(212, 175, 55, 0.3);

            --text-primary: #ffffff;
            --text-secondary: #a1a1aa;
            --text-muted: #71717a;
            
            /* Typography */
            --font-sans: 'Inter', sans-serif;
            --font-display: 'Orbitron', sans-serif;
            
            /* Effects */
            --transition-fast: 0.2s ease;
            --transition-normal: 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            --transition-slow: 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 24px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font-sans);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
            line-height: 1.7;
            -webkit-font-smoothing: antialiased;
        }

        a { text-decoration: none; color: inherit; transition: color var(--transition-fast); }
        h1, h2, h3, h4, h5, h6 { font-family: var(--font-display); font-weight: 700; }

        /* ==========================================
           GLASSMORPHISM NAVBAR
           ========================================== */
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 5%; 
            background-color: rgba(10, 10, 10, 0.8);
            backdrop-filter: blur(20px); 
            -webkit-backdrop-filter: blur(20px);
            position: fixed; top: 0; left: 0; width: 100%;
            z-index: 1000; border-bottom: 1px solid var(--border-color);
            transition: all var(--transition-normal);
        }
        .navbar.scrolled { padding: 10px 5%; background-color: rgba(10, 10, 10, 0.95); }
        
        .navbar-brand { font-family: var(--font-display); font-size: 24px; font-weight: 900; letter-spacing: 1px; }
        .navbar-brand span { color: var(--gold-primary); }
        
        .nav-links { display: flex; gap: 35px; list-style: none; }
        .nav-links li a { 
            font-size: 14px; font-weight: 600; text-transform: uppercase; 
            letter-spacing: 1.5px; position: relative; padding: 5px 0; color: var(--text-secondary);
        }
        .nav-links li a::after { 
            content: ''; position: absolute; bottom: -2px; left: 0; 
            width: 0; height: 2px; background-color: var(--gold-primary); 
            transition: width var(--transition-normal); 
        }
        .nav-links li a:hover, .nav-links li a.active { color: var(--text-primary); }
        .nav-links li a:hover::after, .nav-links li a.active::after { width: 100%; }
        
        .nav-actions { display: flex; align-items: center; gap: 20px; }
        .weather-widget { 
            display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 500; 
            color: var(--text-secondary); padding-right: 20px; border-right: 1px solid var(--border-color);
        }
        
        .user-menu a { font-size: 14px; font-weight: 500; color: var(--text-secondary); }
        .user-menu a:hover { color: var(--text-primary); }
        .user-menu .logout { color: #f87171; }
        .user-menu .logout:hover { color: #ef4444; }

        .btn-booking { 
            background: linear-gradient(135deg, var(--gold-primary), var(--gold-dark)); 
            color: #000; padding: 12px 28px; border-radius: 30px; 
            font-weight: 700; font-size: 13px; text-transform: uppercase; 
            letter-spacing: 1px; border: none; cursor: pointer; 
            box-shadow: 0 4px 20px var(--gold-glow); transition: all var(--transition-normal); 
        }
        .btn-booking:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(212, 175, 55, 0.5); color: #000; }

        /* ==========================================
           HERO SECTION (PARALLAX)
           ========================================== */
        .hero {
            height: 85vh; min-height: 600px;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            padding: 100px 5% 0;
            background: linear-gradient(to bottom, rgba(10, 10, 10, 0.4) 0%, rgba(10, 10, 10, 1) 100%),
                        url('../assets/images/cars/mercedes-s-class.jpg') no-repeat center center/cover;
            background-attachment: fixed; /* Parallax effect */
            text-align: center; position: relative;
        }
        .hero-subtitle {
            font-family: var(--font-sans); color: var(--gold-primary);
            font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 4px;
            margin-bottom: 20px;
            animation: fadeInDown 1s ease-out forwards;
        }
        .hero h1 {
            font-size: 5rem; margin-bottom: 25px; line-height: 1.1;
            text-transform: uppercase; letter-spacing: 2px;
            background: linear-gradient(to right, #fff, #a1a1aa);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            animation: fadeInUp 1s ease-out forwards;
        }
        .hero p {
            font-size: 1.2rem; color: var(--text-secondary); max-width: 700px;
            margin: 0 auto; font-weight: 300; line-height: 1.8;
            animation: fadeInUp 1s ease-out 0.3s forwards; opacity: 0;
        }
        .scroll-down {
            position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%);
            display: flex; flex-direction: column; align-items: center; gap: 10px;
            color: var(--text-secondary); font-size: 12px; letter-spacing: 2px; text-transform: uppercase;
            animation: fadeIn 1s ease-out 1s forwards; opacity: 0;
        }
        .scroll-down i { font-size: 20px; animation: bounce 2s infinite; color: var(--gold-primary); }

        /* ==========================================
           STORY SECTION (ASYMMETRICAL LAYOUT)
           ========================================== */
        .story-section { padding: 120px 5%; background-color: var(--bg-primary); position: relative; }
        .story-section::before {
            content: 'HISTORY'; position: absolute; top: 10%; left: -2%;
            font-family: var(--font-display); font-size: 15vw; font-weight: 900;
            color: rgba(255, 255, 255, 0.02); z-index: 0; user-select: none;
        }
        .story-container {
            display: flex; gap: 80px; max-width: 1300px; margin: 0 auto; position: relative; z-index: 1;
            align-items: center;
        }
        .story-images { flex: 1; position: relative; }
        .img-main { width: 90%; border-radius: var(--radius-md); box-shadow: 0 30px 60px rgba(0,0,0,0.5); z-index: 2; position: relative; }
        .experience-badge {
            position: absolute; bottom: -30px; right: 0; z-index: 3;
            background: rgba(20, 20, 20, 0.8); backdrop-filter: blur(15px); border: 1px solid var(--border-color);
            padding: 30px; border-radius: var(--radius-md); text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        .experience-badge .num { font-size: 3.5rem; color: var(--gold-primary); font-family: var(--font-display); line-height: 1; }
        .experience-badge .text { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 2px; color: var(--text-secondary); margin-top: 10px; font-weight: 600; }
        
        .story-content { flex: 1; padding-left: 40px; }
        .section-tag { color: var(--gold-primary); font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 3px; display: block; margin-bottom: 15px; }
        .story-content h2 { font-size: 3.5rem; line-height: 1.1; margin-bottom: 30px; }
        .story-content p { color: var(--text-secondary); font-size: 1.1rem; line-height: 1.9; margin-bottom: 25px; font-weight: 300; }
        .signature { font-family: 'Mrs Saint Delafield', cursive, sans-serif; font-size: 3rem; color: #fff; margin-top: 30px; opacity: 0.8; }

        /* ==========================================
           STATS SECTION (COUNTER ANIMATION)
           ========================================== */
        .stats-section { padding: 100px 5%; background-color: var(--bg-surface); border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 40px; max-width: 1200px; margin: 0 auto; text-align: center; }
        .stat-item .stat-num { font-size: 4rem; color: var(--gold-primary); font-family: var(--font-display); font-weight: 700; margin-bottom: 15px; display: inline-block; }
        .stat-item .stat-label { font-size: 1rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 2px; font-weight: 600; }

        /* ==========================================
           CORE VALUES (GLASS CARDS)
           ========================================== */
        .values-section { padding: 120px 5%; background: var(--bg-primary); position: relative; overflow: hidden; }
        .values-section::after {
            content: ''; position: absolute; top: 0; right: 0; width: 50%; height: 100%;
            background: radial-gradient(circle at center, rgba(212, 175, 55, 0.05) 0%, transparent 70%); pointer-events: none;
        }
        .section-header { text-align: center; max-width: 700px; margin: 0 auto 80px; }
        .section-header h2 { font-size: 3rem; margin-bottom: 20px; }
        .section-header p { color: var(--text-secondary); font-size: 1.1rem; }
        
        .values-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; max-width: 1200px; margin: 0 auto; position: relative; z-index: 1; }
        .value-card {
            background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color);
            padding: 50px 40px; border-radius: var(--radius-lg); backdrop-filter: blur(10px);
            transition: all var(--transition-normal); position: relative; overflow: hidden;
        }
        .value-card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px;
            background: linear-gradient(90deg, transparent, var(--gold-primary), transparent);
            transform: translateX(-100%); transition: transform var(--transition-slow);
        }
        .value-card:hover { transform: translateY(-10px); background: rgba(255, 255, 255, 0.04); border-color: rgba(212, 175, 55, 0.3); }
        .value-card:hover::before { transform: translateX(100%); }
        .value-icon { font-size: 40px; color: var(--gold-primary); margin-bottom: 30px; }
        .value-card h3 { font-size: 1.5rem; margin-bottom: 15px; letter-spacing: 1px; }
        .value-card p { color: var(--text-secondary); line-height: 1.7; font-weight: 300; }

        /* ==========================================
           FOOTER (PREMIUM DARK)
           ========================================== */
        footer { background: var(--bg-surface); padding: 80px 5% 30px; border-top: 1px solid var(--border-color); }
        .footer-content { display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 50px; margin-bottom: 60px; max-width: 1400px; margin-left: auto; margin-right: auto; }
        .footer-logo { font-family: var(--font-display); font-size: 28px; font-weight: 900; margin-bottom: 25px; display: inline-block; }
        .footer-logo span { color: var(--gold-primary); }
        .footer-about { color: var(--text-secondary); font-size: 1rem; margin-bottom: 30px; line-height: 1.8; padding-right: 20px; }
        .social-links { display: flex; gap: 15px; }
        .social-links a { 
            width: 45px; height: 45px; border-radius: 50%; background: rgba(255,255,255,0.05); 
            display: flex; align-items: center; justify-content: center; color: var(--text-primary); 
            transition: all var(--transition-fast); border: 1px solid transparent;
        }
        .social-links a:hover { background: transparent; border-color: var(--gold-primary); color: var(--gold-primary); transform: translateY(-5px); }
        .footer-title { font-size: 1.2rem; margin-bottom: 30px; color: var(--text-primary); text-transform: uppercase; letter-spacing: 2px; }
        .footer-links { list-style: none; }
        .footer-links li { margin-bottom: 15px; }
        .footer-links a { color: var(--text-secondary); transition: all var(--transition-fast); position: relative; }
        .footer-links a:hover { color: var(--gold-primary); padding-left: 8px; }
        .contact-info { list-style: none; }
        .contact-info li { display: flex; gap: 15px; margin-bottom: 25px; color: var(--text-secondary); line-height: 1.5; align-items: flex-start; }
        .contact-info i { color: var(--gold-primary); font-size: 1.2rem; margin-top: 2px; }
        .footer-bottom { 
            text-align: center; padding-top: 30px; border-top: 1px solid var(--border-color); 
            color: var(--text-muted); font-size: 0.9rem; letter-spacing: 1px; 
        }

        /* ==========================================
           ANIMATIONS & UTILITIES
           ========================================== */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes bounce { 0%, 20%, 50%, 80%, 100% { transform: translateY(0); } 40% { transform: translateY(-15px); } 60% { transform: translateY(-7px); } }
        
        .reveal { opacity: 0; transform: translateY(40px); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        .reveal.active { opacity: 1; transform: translateY(0); }

        /* ==========================================
           RESPONSIVE DESIGN
           ========================================== */
        @media (max-width: 1200px) {
            .story-container { flex-direction: column; text-align: center; }
            .story-content { padding-left: 0; margin-top: 40px; }
            .experience-badge { right: 50%; transform: translateX(50%); }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 50px; }
            .values-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-content { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .navbar { padding: 15px; }
            .nav-links, .weather-widget { display: none; }
            .hero h1 { font-size: 3rem; }
            .values-grid { grid-template-columns: 1fr; }
            .footer-content { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar" id="navbar">
        <a href="index.php" class="navbar-brand">AUTO<span>SUPERCAR</span></a>
        
        <ul class="nav-links">
            <li><a href="index.php">Trang Chủ</a></li>
            <li><a href="cars.php">Khám Phá Xe</a></li>
            <li><a href="compare.php">So Sánh</a></li>
            <li><a href="about.php" class="active">Giới Thiệu</a></li>
            <li><a href="contact.php">Liên Hệ</a></li>
        </ul>

        <div class="nav-actions">
            <div class="weather-widget">
                <i class="fa-solid fa-cloud-sun" style="color: var(--gold-primary); font-size: 16px;"></i> 
                <span id="weatherTemp">--°C</span>
            </div>

            <div class="user-menu">
                <?php if (isset($_SESSION['admin_id']) || isset($_SESSION['user_id'])): ?>
                    <span style="font-weight: 600; color: var(--text-primary); font-size: 14px; margin-right: 15px;">
                        <i class="fa-solid fa-circle-user" style="color: var(--gold-primary);"></i> 
                        <?php echo htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['user_name'] ?? 'Thành viên'); ?>
                    </span>
                    <a href="../logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Thoát</a>
                <?php else: ?>
                    <a href="../login.php"><i class="fa-regular fa-user" style="margin-right: 5px;"></i> Đăng nhập</a>
                <?php endif; ?>
            </div>
            
            <a href="booking.php" class="btn-booking">Lái Thử</a>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <header class="hero">
        <div class="hero-subtitle">Tuyệt Tác Vượt Thời Gian</div>
        <h1>Định Hình<br>Đẳng Cấp</h1>
        <p>Hơn cả một showroom ô tô, AutoSuperCar là nơi giao thoa giữa nghệ thuật chế tác cơ khí đỉnh cao và phong cách sống thượng lưu độc bản. Chúng tôi trao gửi những giá trị tinh hoa nhất.</p>
        
        <div class="scroll-down">
            Khám Phá
            <i class="fa-solid fa-arrow-down-long"></i>
        </div>
    </header>

    <!-- STORY SECTION -->
    <section class="story-section">
        <div class="story-container">
            <div class="story-images reveal">
                <!-- Encode URL path safely -->
                <img src="../assets/images/cars/Porche%20911%20turbo.jpg" alt="Showroom" class="img-main" onerror="this.src='https://images.unsplash.com/photo-1603584173870-7f23fdae1b7a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80'">
                <div class="experience-badge">
                    <div class="num" id="expCounter" data-target="5">0</div>
                    <div class="text">Năm Kiến Tạo<br>Đỉnh Cao</div>
                </div>
            </div>
            
            <div class="story-content reveal">
                <span class="section-tag">Câu Chuyện Thương Hiệu</span>
                <h2>Hành Trình Tới<br><span style="color: var(--gold-primary);">Biểu Tượng</span></h2>
                <p>Khởi nguồn từ niềm đam mê mãnh liệt với những cỗ máy tốc độ và sự hoàn mỹ trong thiết kế, AutoSuperCar được thành lập với một tầm nhìn duy nhất: Trở thành điểm đến tối thượng cho giới tinh hoa yêu xe tại Việt Nam.</p>
                <p>Chúng tôi tự hào sở hữu bộ sưu tập độc bản từ những thương hiệu danh giá bậc nhất thế giới. Mỗi chiếc xe tại showroom không chỉ là một phương tiện di chuyển, mà là một tác phẩm nghệ thuật, một bản tuyên ngôn mạnh mẽ về vị thế của chủ nhân.</p>
                <div class="signature">AutoSuperCar</div>
            </div>
        </div>
    </section>

    <!-- STATS SECTION -->
    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-item reveal">
                <div class="stat-num counter" data-target="500">0</div><span style="color: var(--gold-primary); font-size: 3rem; font-family: var(--font-display); font-weight: 700;">+</span>
                <div class="stat-label">Siêu Xe Đã Giao</div>
            </div>
            <div class="stat-item reveal" style="transition-delay: 0.1s;">
                <div class="stat-num counter" data-target="100">0</div><span style="color: var(--gold-primary); font-size: 3rem; font-family: var(--font-display); font-weight: 700;">%</span>
                <div class="stat-label">Hài Lòng Tuyệt Đối</div>
            </div>
            <div class="stat-item reveal" style="transition-delay: 0.2s;">
                <div class="stat-num counter" data-target="24">0</div><span style="color: var(--gold-primary); font-size: 3rem; font-family: var(--font-display); font-weight: 700;">/7</span>
                <div class="stat-label">Dịch Vụ Đặc Quyền</div>
            </div>
            <div class="stat-item reveal" style="transition-delay: 0.3s;">
                <div class="stat-num counter" data-target="15">0</div><span style="color: var(--gold-primary); font-size: 3rem; font-family: var(--font-display); font-weight: 700;">+</span>
                <div class="stat-label">Thương Hiệu Đỉnh Cao</div>
            </div>
        </div>
    </section>

    <!-- CORE VALUES -->
    <section class="values-section">
        <div class="section-header reveal">
            <span class="section-tag">Triết Lý Kinh Doanh</span>
            <h2>Giá Trị Vượt Thời Gian</h2>
            <p>Những nguyên tắc tối thượng định hình chuẩn mực dịch vụ và cam kết của chúng tôi đối với giới tinh hoa, mang đến trải nghiệm không thể sao chép.</p>
        </div>
        
        <div class="values-grid">
            <div class="value-card reveal">
                <div class="value-icon"><i class="fa-regular fa-gem"></i></div>
                <h3>Độc Bản & Đẳng Cấp</h3>
                <p>Chỉ tuyển chọn và phân phối những mẫu xe xuất sắc nhất, mang tính biểu tượng và khẳng định vị thế độc tôn của chủ nhân trên mọi hành trình.</p>
            </div>
            
            <div class="value-card reveal" style="transition-delay: 0.1s;">
                <div class="value-icon"><i class="fa-solid fa-crown"></i></div>
                <h3>Đặc Quyền Tối Thượng</h3>
                <p>Dịch vụ chăm sóc khách hàng cá nhân hóa, không gian riêng tư tuyệt đối và những đặc quyền chỉ dành riêng cho thành viên của AutoSuperCar.</p>
            </div>
            
            <div class="value-card reveal" style="transition-delay: 0.2s;">
                <div class="value-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <h3>Uy Tín Toàn Cầu</h3>
                <p>Minh bạch trong mọi giao dịch, cam kết chất lượng tuyệt đối với chế độ bảo hành và bảo dưỡng theo đúng tiêu chuẩn khắt khe nhất của hãng.</p>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer-content">
            <div>
                <div class="footer-logo">AUTO<span>SUPERCAR</span></div>
                <p class="footer-about">
                    Điểm đến lý tưởng cho những người đam mê xe hơi siêu sang. Chúng tôi tự hào phân phối các dòng xe cao cấp nhất với chất lượng dịch vụ chuẩn quốc tế.
                </p>
                <div class="social-links">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <div>
                <h4 class="footer-title">Khám Phá</h4>
                <ul class="footer-links">
                    <li><a href="index.php">Trang Chủ</a></li>
                    <li><a href="cars.php">Bộ Sưu Tập Xe</a></li>
                    <li><a href="compare.php">So Sánh Xe</a></li>
                    <li><a href="about.php">Về Chúng Tôi</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="footer-title">Dịch Vụ</h4>
                <ul class="footer-links">
                    <li><a href="booking.php">Đặt Lịch Lái Thử</a></li>
                    <li><a href="#">Bảo Hành & Bảo Dưỡng</a></li>
                    <li><a href="#">Tư Vấn Tài Chính</a></li>
                    <li><a href="#">Chăm Sóc VIP</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="footer-title">Liên Hệ</h4>
                <ul class="contact-info">
                    <li>
                        <i class="fa-solid fa-location-dot"></i>
                        <span>178 Đại Mỗ, Quận Nam Từ Liêm,<br>TP. Hà Nội</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-phone"></i>
                        <span>+84 (0) 356 827 852</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-envelope"></i>
                        <span>contact@autosupercar.vn</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> AutoSuperCar. All Rights Reserved. Designed for Luxury.</p>
        </div>
    </footer>

    <!-- JAVASCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Weather API Fetch
            fetchWeather();
            
            // 2. Navbar Scroll Effect
            const navbar = document.getElementById('navbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            // 3. Scroll Reveal Animation
            const revealElements = document.querySelectorAll('.reveal');
            const revealOptions = { threshold: 0.15, rootMargin: "0px 0px -50px 0px" };
            
            const revealObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('active');
                    
                    // Nếu element là stats section, kích hoạt counter
                    if(entry.target.querySelector('.counter') || entry.target.id === 'expCounter') {
                        startCounters(entry.target);
                    }
                    
                    observer.unobserve(entry.target); // Chỉ chạy 1 lần
                });
            }, revealOptions);

            revealElements.forEach(el => revealObserver.observe(el));
            
            // Theo dõi riêng cho badge kinh nghiệm vì nó có thể nằm ngoài flow
            const expBadge = document.querySelector('.experience-badge');
            if(expBadge) revealObserver.observe(expBadge.parentElement);
        });

        // Function: Counter Animation
        function startCounters(container) {
            const counters = container.querySelectorAll('.counter, #expCounter');
            counters.forEach(counter => {
                const target = +counter.getAttribute('data-target') || 0; 
                const duration = 2000; // 2 seconds
                const increment = target / (duration / 16); // 60fps
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

        // Function: Fetch Weather
        function fetchWeather() {
            fetch('../api/get_weather.php')
                .then(response => response.json())
                .then(data => {
                    if (data && data.current_weather) {
                        const temp = Math.round(data.current_weather.temperature);
                        document.getElementById('weatherTemp').innerHTML = `${temp}°C <span style="font-size: 11px; font-weight:400; margin-left: 3px;">Hà Nội</span>`;
                    }
                })
                .catch(error => console.error('Error fetching weather:', error));
        }
    </script>
</body>
</html>
