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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS VARIABLES */
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-card: #ffffff;
            --border: #e2e8f0;

            --gold: #d4a843;
            --gold-light: #f0c96a;
            --gold-dark: #a67c2e;
            --gold-glow: rgba(212, 168, 67, 0.25);

            --text-dark: #0f172a;
            --text-muted: #475569;
            --text-dim: #94a3b8;
            
            --transition: 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            --radius-lg: 16px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-dark);
            min-height: 100vh;
            overflow-x: hidden;
            line-height: 1.7;
        }

        a { text-decoration: none; color: inherit; transition: color var(--transition); }
        h1, h2, h3, h4, h5, h6 { font-family: 'Orbitron', sans-serif; color: var(--text-dark); }

        /* NAVBAR (Preserved) */
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 5%; background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px); position: fixed; top: 0; left: 0; width: 100%;
            z-index: 1000; box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }
        .navbar-brand { font-family: 'Orbitron', sans-serif; font-size: 24px; font-weight: 900; display: flex; align-items: center; gap: 10px; }
        .navbar-brand span { color: var(--gold); }
        .nav-links { display: flex; gap: 30px; list-style: none; }
        .nav-links li a { font-size: 15px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; position: relative; padding: 5px 0; }
        .nav-links li a::after { content: ''; position: absolute; bottom: 0; left: 0; width: 0; height: 2px; background-color: var(--gold); transition: width var(--transition); }
        .nav-links li a:hover::after, .nav-links li a.active::after { width: 100%; }
        .nav-links li a:hover, .nav-links li a.active { color: var(--gold); }
        .btn-nav { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: #fff; padding: 10px 24px; border-radius: 30px; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; border: none; cursor: pointer; box-shadow: 0 4px 15px var(--gold-glow); transition: all var(--transition); margin-left: 20px; }
        .btn-nav:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(212, 168, 67, 0.4); color: #fff; }

        /* SCROLL ANIMATIONS */
        .animate-on-scroll {
            opacity: 0; transform: translateY(40px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }
        .animate-on-scroll.visible { opacity: 1; transform: translateY(0); }

        /* PAGE HEADER (PREMIUM) */
        .page-header {
            height: 75vh;
            min-height: 500px;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            padding: 100px 5% 0;
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.6) 0%, rgba(15, 23, 42, 0.8) 100%),
                        url('../assets/images/cars/mercedes-s-class.jpg') no-repeat center center/cover;
            background-attachment: fixed;
            text-align: center; color: #fff; position: relative;
        }
        .page-header h1 {
            font-size: 4.5rem; margin-bottom: 20px; color: #fff; text-transform: uppercase;
            letter-spacing: 4px; text-shadow: 0 4px 15px rgba(0,0,0,0.3);
            animation: fadeInUp 1s ease-out forwards;
        }
        .page-header p {
            font-size: 1.3rem; color: rgba(255,255,255,0.9); max-width: 800px;
            margin: 0 auto; font-weight: 300; line-height: 1.8;
            animation: fadeInUp 1s ease-out 0.3s forwards; opacity: 0;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .scroll-indicator {
            position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%);
            color: rgba(255,255,255,0.6); font-size: 24px; animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0) translateX(-50%); }
            40% { transform: translateY(-20px) translateX(-50%); }
            60% { transform: translateY(-10px) translateX(-50%); }
        }

        /* ABOUT SECTION */
        .about-section { padding: 120px 5%; background-color: var(--bg-primary); }
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; max-width: 1200px; margin: 0 auto; }
        
        .about-content h2 { font-size: 3rem; margin-bottom: 30px; line-height: 1.2; color: var(--text-dark); }
        .about-content h2 span { color: var(--gold); }
        .about-content p { color: var(--text-muted); font-size: 1.15rem; line-height: 1.9; margin-bottom: 25px; font-weight: 300; }
        
        .signature { font-family: 'Orbitron', sans-serif; font-size: 1.5rem; color: var(--gold); margin-top: 40px; font-weight: 700; letter-spacing: 2px; }

        .about-image { position: relative; border-radius: 4px; overflow: hidden; box-shadow: 0 30px 60px rgba(0,0,0,0.15); }
        .about-image img { width: 100%; height: auto; display: block; transition: transform 0.8s ease; }
        .about-image:hover img { transform: scale(1.05); }
        
        .experience-badge {
            position: absolute; bottom: -20px; right: -20px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: #fff; padding: 30px; width: 160px; height: 160px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            box-shadow: 0 15px 40px rgba(212, 168, 67, 0.4); border: 8px solid var(--bg-primary);
        }
        .experience-badge h3 { font-size: 2.5rem; margin: 0; color: #fff; line-height: 1; font-family: 'Inter', sans-serif; font-weight: 900; }
        .experience-badge span { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; text-align: center; margin-top: 5px; line-height: 1.4; }

        /* STATS SECTION (DARK) */
        .stats-section { background: #0f172a; color: #fff; padding: 100px 5%; position: relative; overflow: hidden; }
        .stats-section::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: url('../assets/images/cars/maybach.jpg') no-repeat center center/cover;
            opacity: 0.1; z-index: 0; filter: grayscale(100%);
        }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; max-width: 1200px; margin: 0 auto; position: relative; z-index: 1; text-align: center; }
        .stat-item h3 { font-size: 4rem; color: var(--gold); margin-bottom: 10px; font-family: 'Inter', sans-serif; font-weight: 800; }
        .stat-item p { font-size: 1.1rem; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 2px; font-weight: 600; }

        /* CORE VALUES */
        .core-values { padding: 120px 5%; background-color: var(--bg-secondary); }
        .section-title { text-align: center; margin-bottom: 80px; }
        .section-title h2 { font-size: 3rem; margin-bottom: 20px; position: relative; display: inline-block; }
        .values-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px; max-width: 1200px; margin: 0 auto; }
        .value-card {
            background: var(--bg-primary); padding: 60px 40px; border-top: 4px solid transparent;
            box-shadow: 0 15px 40px rgba(0,0,0,0.03); transition: all var(--transition);
            display: flex; flex-direction: column; align-items: center; text-align: center;
        }
        .value-card:hover { border-top-color: var(--gold); transform: translateY(-10px); box-shadow: 0 25px 50px rgba(0,0,0,0.08); }
        .value-icon {
            width: 90px; height: 90px; border-radius: 50%;
            background: rgba(212, 168, 67, 0.1); color: var(--gold);
            display: flex; align-items: center; justify-content: center;
            font-size: 36px; margin-bottom: 30px; transition: all var(--transition);
        }
        .value-card:hover .value-icon { background: var(--gold); color: #fff; transform: scale(1.1); }
        .value-card h3 { font-size: 1.5rem; margin-bottom: 20px; font-family: 'Inter', sans-serif; font-weight: 700; letter-spacing: -0.5px; }
        .value-card p { color: var(--text-muted); font-size: 1.05rem; line-height: 1.8; }

        /* FOOTER */
        footer { background: #0f172a; color: #fff; padding: 80px 5% 30px; border-top: 1px solid rgba(255,255,255,0.05); }
        .footer-content { display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 40px; margin-bottom: 50px; }
        .footer-logo { font-family: 'Orbitron', sans-serif; font-size: 24px; font-weight: 900; margin-bottom: 20px; }
        .footer-logo span { color: var(--gold); }
        .footer-about { color: rgba(255,255,255,0.6); font-size: 0.95rem; margin-bottom: 20px; line-height: 1.8; }
        .social-links { display: flex; gap: 15px; }
        .social-links a { width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; color: #fff; transition: all var(--transition); }
        .social-links a:hover { background: var(--gold); transform: translateY(-3px); }
        .footer-title { font-size: 1.2rem; margin-bottom: 25px; color: #fff; font-family: 'Inter', sans-serif; font-weight: 600; letter-spacing: 1px; }
        .footer-links { list-style: none; }
        .footer-links li { margin-bottom: 15px; }
        .footer-links a { color: rgba(255,255,255,0.6); transition: color var(--transition); }
        .footer-links a:hover { color: var(--gold); padding-left: 5px; }
        .contact-info li { display: flex; gap: 15px; margin-bottom: 20px; color: rgba(255,255,255,0.6); }
        .contact-info i { color: var(--gold); font-size: 1.2rem; margin-top: 3px; }
        .footer-bottom { text-align: center; padding-top: 30px; border-top: 1px solid rgba(255,255,255,0.05); color: rgba(255,255,255,0.5); font-size: 0.9rem; }

        /* Responsive */
        @media (max-width: 992px) {
            .page-header h1 { font-size: 3.5rem; }
            .about-grid { grid-template-columns: 1fr; gap: 60px; text-align: center; }
            .experience-badge { right: 50%; transform: translateX(50%); bottom: -30px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 50px; }
            .values-grid { grid-template-columns: 1fr; }
            .footer-content { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .page-header { padding: 150px 5% 80px; }
            .page-header h1 { font-size: 2.5rem; }
            .stats-grid { grid-template-columns: 1fr; }
            .footer-content { grid-template-columns: 1fr; }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">
            AUTO<span>SUPERCAR</span>
        </a>
        
        <ul class="nav-links">
            <li><a href="index.php">Trang Chủ</a></li>
            <li><a href="cars.php">Khám Phá Xe</a></li>
            <li><a href="compare.php">So Sánh</a></li>
            <li><a href="about.php" class="active">Giới Thiệu</a></li>
            <li><a href="contact.php">Liên Hệ</a></li>
        </ul>

        <div style="display: flex; align-items: center; gap: 15px;">
            <div id="weatherWidget" style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--text-dark); margin-right: 10px; padding-right: 15px; border-right: 1px solid var(--border);">
                <i class="fa-solid fa-cloud-sun" style="color: var(--gold); font-size: 16px;"></i> 
                <span id="weatherTemp">--°C</span>
            </div>

            <?php if (isset($_SESSION['admin_id']) || isset($_SESSION['user_id'])): ?>
                <span style="font-weight: 600; color: var(--text-dark); font-size: 14px;">
                    <i class="fa-solid fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['user_name'] ?? 'Thành viên'); ?>
                </span>
                <a href="../logout.php" style="color: #ef4444; font-weight: 600; text-decoration: none; font-size: 14px;"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
            <?php else: ?>
                <a href="../login.php" style="color: var(--text-dark); font-weight: 600; text-decoration: none; font-size: 14px;"><i class="fa-solid fa-user"></i> Đăng nhập</a>
            <?php endif; ?>
            <a href="booking.php" class="btn-nav"><i class="fa-solid fa-calendar-check" style="margin-right: 8px;"></i> Lái Thử</a>
        </div>
    </nav>

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1>Định Hình<br><span style="color: var(--gold);">Đẳng Cấp</span></h1>
        <p>Hơn cả một showroom ô tô, AutoSuperCar là nơi giao thoa giữa nghệ thuật chế tác đỉnh cao và phong cách sống thượng lưu. Chúng tôi không chỉ bán xe, chúng tôi trao gửi những giá trị vượt thời gian.</p>
        <div class="scroll-indicator"><i class="fa-solid fa-angle-down"></i></div>
    </div>

    <!-- ABOUT CONTENT -->
    <section class="about-section">
        <div class="about-grid">
            <div class="about-content animate-on-scroll">
                <h2 style="font-size: 1rem; text-transform: uppercase; color: var(--gold); font-family: 'Inter', sans-serif; font-weight: 700; letter-spacing: 3px; margin-bottom: 10px;">Câu Chuyện Thương Hiệu</h2>
                <h2>Hành Trình Tới<br><span>Biểu Tượng</span></h2>
                <p>Khởi nguồn từ niềm đam mê mãnh liệt với những cỗ máy tốc độ và sự hoàn mỹ trong thiết kế, AutoSuperCar Showroom được thành lập vào năm 2010 với một tầm nhìn duy nhất: Trở thành điểm đến tối thượng cho giới tinh hoa yêu xe tại Việt Nam.</p>
                <p>Chúng tôi tự hào sở hữu bộ sưu tập độc bản từ những thương hiệu danh giá bậc nhất thế giới như Rolls-Royce, Bentley, Mercedes-Maybach, Porsche và Lamborghini. Mỗi chiếc xe tại showroom không chỉ là một phương tiện di chuyển, mà là một tác phẩm nghệ thuật, một bản tuyên ngôn mạnh mẽ về vị thế của chủ nhân.</p>
                <p>Với không gian trưng bày sang trọng bậc nhất cùng đội ngũ chuyên gia am hiểu sâu sắc, AutoSuperCar mang đến trải nghiệm mua sắm cá nhân hóa, tinh tế và hoàn hảo đến từng chi tiết.</p>
                <div class="signature">AUTOSUPERCAR</div>
            </div>
            
            <div class="about-image animate-on-scroll">
                <img src="../assets/images/cars/Porche 911 turbo.jpg" alt="AutoSuperCar Showroom" onerror="this.src='https://images.unsplash.com/photo-1560958089-b8a1929cea89?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80'">
                <div class="experience-badge">
                    <h3>5</h3>
                    <span>Năm<br>Đỉnh Cao</span>
                </div>
            </div>
        </div>
    </section>

    <!-- STATS SECTION -->
    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-item animate-on-scroll">
                <h3>500+</h3>
                <p>Siêu Xe Đã Giao</p>
            </div>
            <div class="stat-item animate-on-scroll" style="transition-delay: 0.2s;">
                <h3>100%</h3>
                <p>Khách Hàng Hài Lòng</p>
            </div>
            <div class="stat-item animate-on-scroll" style="transition-delay: 0.4s;">
                <h3>5</h3>
                <p>Năm Kinh Nghiệm</p>
            </div>
            <div class="stat-item animate-on-scroll" style="transition-delay: 0.6s;">
                <h3>11+</h3>
                <p>Thương Hiệu Đỉnh Cao</p>
            </div>
        </div>
    </section>

    <!-- CORE VALUES -->
    <section class="core-values">
        <div class="section-title animate-on-scroll">
            <h2 style="font-size: 1rem; text-transform: uppercase; color: var(--gold); font-family: 'Inter', sans-serif; font-weight: 700; letter-spacing: 3px; margin-bottom: 15px; display: block;">Triết Lý Kinh Doanh</h2>
            <h2>Giá Trị Vượt Thời Gian</h2>
            <p style="margin-top: 20px; color: var(--text-muted); font-size: 1.15rem;">Những nguyên tắc tối thượng định hình chuẩn mực dịch vụ và cam kết của chúng tôi đối với giới tinh hoa.</p>
        </div>
        
        <div class="values-grid">
            <div class="value-card animate-on-scroll">
                <div class="value-icon"><i class="fa-regular fa-gem"></i></div>
                <h3>Độc Bản & Đẳng Cấp</h3>
                <p>Chỉ tuyển chọn và phân phối những mẫu xe xuất sắc nhất, mang tính biểu tượng và khẳng định vị thế độc tôn của chủ nhân trên mọi hành trình.</p>
            </div>
            
            <div class="value-card animate-on-scroll" style="transition-delay: 0.2s;">
                <div class="value-icon"><i class="fa-solid fa-crown"></i></div>
                <h3>Đặc Quyền Tối Thượng</h3>
                <p>Dịch vụ chăm sóc khách hàng cá nhân hóa, không gian riêng tư tuyệt đối và những đặc quyền chỉ dành riêng cho thành viên của AutoSuperCar.</p>
            </div>
            
            <div class="value-card animate-on-scroll" style="transition-delay: 0.4s;">
                <div class="value-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <h3>Uy Tín Toàn Cầu</h3>
                <p>Minh bạch trong mọi giao dịch, cam kết chất lượng tuyệt đối với chế độ bảo hành và bảo dưỡng theo đúng tiêu chuẩn khắt khe nhất toàn cầu.</p>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer-content">
            <div>
                <div class="footer-logo">AUTO<span>SUPERCAR</span></div>
                <p class="footer-about">
                    AutoSuperCar Showroom là điểm đến lý tưởng cho những người đam mê xe hơi siêu sang. Chúng tôi tự hào phân phối các dòng xe cao cấp nhất với chất lượng dịch vụ chuẩn quốc tế.
                </p>
                <div class="social-links">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                </div>
            </div>
            
            <div>
                <h4 class="footer-title">Khám Phá</h4>
                <ul class="footer-links">
                    <li><a href="index.php">Trang Chủ</a></li>
                    <li><a href="cars.php">Tất Cả Mẫu Xe</a></li>
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
                    <li><a href="contact.php">Liên Hệ Hỗ Trợ</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="footer-title">Liên Hệ</h4>
                <ul class="contact-info" style="list-style: none; padding: 0;">
                    <li>
                        <i class="fa-solid fa-location-dot"></i>
                        <span>178 Đại Mỗ, Quận Nam Từ Liêm, TP. Hà Nội</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-phone"></i>
                        <span>+84 (0) 356 827 852</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-envelope"></i>
                        <span>nnnam12341@gmail.com</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> AutoSuperCar Showroom. All Rights Reserved. Designed for Luxury.</p>
        </div>
    </footer>

    <!-- JAVASCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchWeather();
            
            // Intersection Observer for scroll animations
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });
        });

        function fetchWeather() {
            fetch('../api/get_weather.php')
                .then(response => response.json())
                .then(data => {
                    if (data && data.current_weather) {
                        const temp = Math.round(data.current_weather.temperature);
                        document.getElementById('weatherTemp').innerHTML = `${temp}°C <span style="color: var(--text-muted); font-weight: 500; font-size: 11px;">Hà Nội</span>`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching weather:', error);
                });
        }
    </script>
</body>
</html>
