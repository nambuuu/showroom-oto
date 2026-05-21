<?php
// customer/booking.php
require_once '../config/db.php';
// session_start(); // Đã được gọi trong db.php

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
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoSuperCar | Đặt Lịch Lái Thử</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* ==========================================
           PREMIUM DESIGN SYSTEM - BOOKING PAGE
           ========================================== */
        :root {
            /* Colors */
            --bg-primary: #0a0a0a;
            --bg-secondary: #121212;
            --bg-surface: #1a1a1a;
            --border-color: rgba(255, 255, 255, 0.08);
            --border-focus: rgba(212, 175, 55, 0.5);
            
            --gold-primary: #d4af37;
            --gold-light: #f3e5ab;
            --gold-dark: #aa8623;
            --gold-glow: rgba(212, 175, 55, 0.25);

            --text-primary: #ffffff;
            --text-secondary: #a1a1aa;
            --text-muted: #71717a;
            
            /* Typography */
            --font-sans: 'Inter', sans-serif;
            --font-display: 'Orbitron', sans-serif;
            
            /* Effects */
            --transition-fast: 0.2s ease;
            --transition-normal: 0.4s cubic-bezier(0.16, 1, 0.3, 1);
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
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        a { text-decoration: none; color: inherit; transition: color var(--transition-fast); }
        h1, h2, h3, h4, h5, h6 { font-family: var(--font-display); font-weight: 700; }

        /* NAVBAR (Mượn style từ About) */
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 5%; background-color: rgba(10, 10, 10, 0.8);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            position: fixed; top: 0; left: 0; width: 100%;
            z-index: 1000; border-bottom: 1px solid var(--border-color);
            transition: all var(--transition-normal);
        }
        .navbar.scrolled { padding: 10px 5%; background-color: rgba(10, 10, 10, 0.95); }
        .navbar-brand { font-family: var(--font-display); font-size: 24px; font-weight: 900; letter-spacing: 1px; }
        .navbar-brand span { color: var(--gold-primary); }
        .nav-links { display: flex; gap: 35px; list-style: none; }
        .nav-links li a { font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; position: relative; padding: 5px 0; color: var(--text-secondary); }
        .nav-links li a::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 0; height: 2px; background-color: var(--gold-primary); transition: width var(--transition-normal); }
        .nav-links li a:hover, .nav-links li a.active { color: var(--text-primary); }
        .nav-links li a:hover::after, .nav-links li a.active::after { width: 100%; }
        .nav-actions { display: flex; align-items: center; gap: 20px; }
        .weather-widget { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 500; color: var(--text-secondary); padding-right: 20px; border-right: 1px solid var(--border-color); }
        .user-menu a { font-size: 14px; font-weight: 500; color: var(--text-secondary); }
        .user-menu a:hover { color: var(--text-primary); }
        .user-menu .logout { color: #f87171; }
        .user-menu .logout:hover { color: #ef4444; }
        .btn-booking { background: linear-gradient(135deg, var(--gold-primary), var(--gold-dark)); color: #000; padding: 12px 28px; border-radius: 30px; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; border: none; cursor: pointer; box-shadow: 0 4px 20px var(--gold-glow); transition: all var(--transition-normal); }
        .btn-booking:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(212, 175, 55, 0.5); color: #000; }

        /* ==========================================
           PAGE HEADER
           ========================================== */
        .page-header {
            padding: 160px 5% 60px;
            background: linear-gradient(to bottom, rgba(10, 10, 10, 0.7) 0%, var(--bg-primary) 100%),
                        url('../assets/images/cars/Porche%20911%20turbo.jpg') no-repeat center 30%/cover;
            text-align: center; border-bottom: 1px solid var(--border-color);
        }
        .page-header h1 { font-size: 3.5rem; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 2px; }
        .page-header p { font-size: 1.1rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto; font-weight: 300; }

        /* ==========================================
           BOOKING SECTION (GLASSMORPHISM)
           ========================================== */
        .booking-section { padding: 80px 5%; display: flex; justify-content: center; align-items: center; position: relative; }
        .booking-section::before {
            content: ''; position: absolute; top: -10%; left: -10%; width: 40%; height: 60%;
            background: radial-gradient(circle, var(--gold-glow) 0%, transparent 60%); filter: blur(80px); z-index: 0;
        }

        .booking-container {
            display: flex; background: rgba(26, 26, 26, 0.6); backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px);
            border-radius: var(--radius-lg); box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            overflow: hidden; width: 100%; max-width: 1200px; border: 1px solid var(--border-color); position: relative; z-index: 1;
        }

        /* Left Info */
        .booking-info {
            flex: 1; background: url('../assets/images/cars/maybach.jpg') no-repeat center center/cover;
            position: relative; padding: 60px 50px; display: flex; flex-direction: column; justify-content: center;
        }
        .booking-info::before { content: ''; position: absolute; top: 0; right: 0; bottom: 0; left: 0; background: rgba(10, 10, 10, 0.85); z-index: 0; }
        .booking-info > * { position: relative; z-index: 1; }
        
        .booking-info h2 { color: var(--gold-primary); font-size: 2.5rem; margin-bottom: 20px; line-height: 1.2; }
        .booking-info p { color: var(--text-secondary); margin-bottom: 40px; font-size: 1.1rem; line-height: 1.8; font-weight: 300; }
        .features-list { list-style: none; }
        .features-list li { display: flex; align-items: center; gap: 15px; margin-bottom: 25px; font-size: 1.05rem; font-weight: 500; }
        .features-list i { 
            color: var(--gold-primary); font-size: 1.1rem; width: 32px; height: 32px; 
            background: rgba(212, 175, 55, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; 
        }

        /* Right Form */
        .booking-form { flex: 1.3; padding: 60px 50px; background: rgba(20, 20, 20, 0.5); }
        .form-title { font-size: 2rem; margin-bottom: 30px; color: var(--text-primary); letter-spacing: 1px; }

        .form-group { margin-bottom: 25px; }
        .form-row { display: flex; gap: 20px; }
        .form-row .form-group { flex: 1; }
        .form-label { display: block; margin-bottom: 10px; font-weight: 600; color: var(--text-secondary); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }
        
        .form-control {
            width: 100%; padding: 15px 20px; border: 1px solid var(--border-color);
            border-radius: var(--radius-sm); font-family: var(--font-sans); font-size: 1rem;
            background-color: rgba(255, 255, 255, 0.03); color: var(--text-primary);
            transition: all var(--transition-fast);
        }
        .form-control::placeholder { color: var(--text-muted); }
        .form-control:focus { outline: none; border-color: var(--gold-primary); background-color: rgba(255, 255, 255, 0.05); box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1); }
        
        /* Chỉnh style cho thẻ Date & Time & Select trên Dark mode */
        select.form-control, input[type="date"].form-control, input[type="time"].form-control {
            color: var(--text-primary);
            color-scheme: dark;
        }
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/200.svg' width='12' height='12' fill='%23d4af37' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 20px center;
        }
        select.form-control option { background-color: var(--bg-surface); color: var(--text-primary); }

        .btn-submit {
            background: linear-gradient(135deg, var(--gold-primary), var(--gold-dark)); color: #000;
            padding: 16px 30px; border: none; border-radius: var(--radius-sm); width: 100%;
            font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 2px;
            cursor: pointer; transition: all var(--transition-normal); box-shadow: 0 4px 15px var(--gold-glow);
            font-family: var(--font-sans); display: flex; justify-content: center; align-items: center; gap: 10px;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4); }
        .btn-submit:disabled { opacity: 0.7; cursor: not-allowed; transform: none; box-shadow: none; }

        /* ALERTS */
        .alert { padding: 15px 20px; border-radius: var(--radius-sm); margin-bottom: 25px; display: none; font-weight: 500; align-items: center; gap: 12px; animation: fadeIn 0.3s ease-out; }
        .alert-success { background-color: rgba(22, 101, 52, 0.2); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.2); }
        .alert-error { background-color: rgba(153, 27, 27, 0.2); color: #f87171; border: 1px solid rgba(248, 113, 113, 0.2); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

        /* ==========================================
           FOOTER (PREMIUM DARK)
           ========================================== */
        footer { background: var(--bg-surface); padding: 80px 5% 30px; border-top: 1px solid var(--border-color); }
        .footer-content { display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 50px; margin-bottom: 60px; max-width: 1400px; margin-left: auto; margin-right: auto; }
        .footer-logo { font-family: var(--font-display); font-size: 28px; font-weight: 900; margin-bottom: 25px; display: inline-block; }
        .footer-logo span { color: var(--gold-primary); }
        .footer-about { color: var(--text-secondary); font-size: 1rem; margin-bottom: 30px; line-height: 1.8; padding-right: 20px; }
        .social-links { display: flex; gap: 15px; }
        .social-links a { width: 45px; height: 45px; border-radius: 50%; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; color: var(--text-primary); transition: all var(--transition-fast); border: 1px solid transparent; }
        .social-links a:hover { background: transparent; border-color: var(--gold-primary); color: var(--gold-primary); transform: translateY(-5px); }
        .footer-title { font-size: 1.2rem; margin-bottom: 30px; color: var(--text-primary); text-transform: uppercase; letter-spacing: 2px; }
        .footer-links { list-style: none; }
        .footer-links li { margin-bottom: 15px; }
        .footer-links a { color: var(--text-secondary); transition: all var(--transition-fast); position: relative; }
        .footer-links a:hover { color: var(--gold-primary); padding-left: 8px; }
        .contact-info { list-style: none; }
        .contact-info li { display: flex; gap: 15px; margin-bottom: 25px; color: var(--text-secondary); line-height: 1.5; align-items: flex-start; }
        .contact-info i { color: var(--gold-primary); font-size: 1.2rem; margin-top: 2px; }
        .footer-bottom { text-align: center; padding-top: 30px; border-top: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.9rem; letter-spacing: 1px; }

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .booking-container { flex-direction: column; }
            .booking-info, .booking-form { padding: 40px; }
            .footer-content { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .navbar { padding: 15px; }
            .nav-links, .weather-widget { display: none; }
            .form-row { flex-direction: column; gap: 0; }
            .footer-content { grid-template-columns: 1fr; }
            .page-header h1 { font-size: 2.5rem; }
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
            <li><a href="about.php">Giới Thiệu</a></li>
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
            
            <a href="booking.php" class="btn-booking active">Lái Thử</a>
        </div>
    </nav>

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1>Đặt Lịch Lái Thử</h1>
        <p>Cảm nhận sức mạnh thực sự và trải nghiệm không gian sang trọng trên từng cung đường cùng AutoSuperCar.</p>
    </div>

    <!-- BOOKING SECTION -->
    <section class="booking-section">
        <div class="booking-container">
            <!-- Left Info -->
            <div class="booking-info">
                <h2>Trải Nghiệm<br>Hoàn Mỹ</h2>
                <p>Chúng tôi luôn sẵn sàng mang đến cho bạn cơ hội được trực tiếp cầm lái những mẫu xe sang trọng và mạnh mẽ nhất thế giới.</p>
                
                <ul class="features-list">
                    <li><i class="fa-solid fa-car-side"></i> Lựa chọn linh hoạt mẫu xe yêu thích.</li>
                    <li><i class="fa-solid fa-user-tie"></i> Chuyên viên tư vấn đồng hành 1:1.</li>
                    <li><i class="fa-solid fa-map-location-dot"></i> Trải nghiệm lộ trình thiết kế riêng biệt.</li>
                    <li><i class="fa-solid fa-gift"></i> Đặc quyền ưu đãi khi quyết định mua.</li>
                </ul>
            </div>

            <!-- Right Form -->
            <div class="booking-form">
                <h3 class="form-title">Thông Tin Đăng Ký</h3>
                
                <div id="bookingAlert" class="alert"></div>

                <form id="formBooking">
                    <div class="form-group">
                        <label class="form-label">Chọn Mẫu Xe <span style="color:#ef4444">*</span></label>
                        <select name="car_id" id="car_id" class="form-control" required>
                            <option value="">-- Đang tải danh sách xe --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Họ & Tên <span style="color:#ef4444">*</span></label>
                        <!-- Auto-fill for logged-in user -->
                        <input type="text" name="full_name" class="form-control" placeholder="Nhập họ và tên đầy đủ" value="<?php echo htmlspecialchars($user_name); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Số Điện Thoại <span style="color:#ef4444">*</span></label>
                            <input type="tel" name="phone" class="form-control" placeholder="09xxxx..." value="<?php echo htmlspecialchars($user_phone); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="example@email.com" value="<?php echo htmlspecialchars($user_email); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Ngày Lái Thử <span style="color:#ef4444">*</span></label>
                            <input type="date" name="preferred_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Giờ Lái Thử <span style="color:#ef4444">*</span></label>
                            <input type="time" name="preferred_time" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Yêu Cầu Bổ Sung</label>
                        <textarea name="message" class="form-control" rows="3" placeholder="Nhập ghi chú hoặc yêu cầu đặc biệt của bạn..."></textarea>
                    </div>

                    <button type="submit" class="btn-submit" id="btnSubmitBooking">
                        Xác Nhận Đặt Lịch <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>
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
            fetchWeather();
            loadCars();

            // Navbar Scroll Effect
            const navbar = document.getElementById('navbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) navbar.classList.add('scrolled');
                else navbar.classList.remove('scrolled');
            });

            // Date validation (Must be future date)
            const dateInput = document.querySelector('input[name="preferred_date"]');
            const today = new Date().toISOString().split('T')[0];
            dateInput.setAttribute('min', today);

            // Form Submit
            const formBooking = document.getElementById('formBooking');
            formBooking.addEventListener('submit', function(e) {
                e.preventDefault();
                submitBooking();
            });
        });

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

        function loadCars() {
            const selectCar = document.getElementById('car_id');
            // Fetch top cars for selection
            fetch('../api/get_cars.php?limit=100')
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success' && res.data) {
                        selectCar.innerHTML = '<option value="">-- Vui lòng chọn mẫu xe --</option>';
                        res.data.forEach(car => {
                            selectCar.innerHTML += `<option value="${car.id}">${car.brand_name ? car.brand_name + ' ' : ''}${car.model_name}</option>`;
                        });
                    } else {
                        selectCar.innerHTML = '<option value="">Không tải được danh sách xe</option>';
                    }
                })
                .catch(err => {
                    selectCar.innerHTML = '<option value="">Lỗi kết nối</option>';
                    console.error(err);
                });
        }

        function submitBooking() {
            const form = document.getElementById('formBooking');
            const btn = document.getElementById('btnSubmitBooking');
            const alertBox = document.getElementById('bookingAlert');
            const formData = new FormData(form);
            
            const data = {};
            formData.forEach((value, key) => { data[key] = value; });

            // Loading state
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Đang Xử Lý...';
            alertBox.style.display = 'none';
            alertBox.className = 'alert';

            fetch('../api/post_booking.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(res => {
                btn.disabled = false;
                btn.innerHTML = 'Xác Nhận Đặt Lịch <i class="fa-solid fa-arrow-right"></i>';
                
                alertBox.style.display = 'flex';
                if (res.status === 'success') {
                    alertBox.classList.add('alert-success');
                    alertBox.innerHTML = '<i class="fa-solid fa-circle-check" style="font-size:1.2rem;"></i> ' + res.message;
                    form.reset();
                    // Nếu là user đã đăng nhập, không nên xóa sạch tên/sđt, nhưng hàm form.reset() sẽ đưa về giá trị mặc định (value attribute).
                } else {
                    alertBox.classList.add('alert-error');
                    alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="font-size:1.2rem;"></i> ' + (res.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerHTML = 'Xác Nhận Đặt Lịch <i class="fa-solid fa-arrow-right"></i>';
                alertBox.style.display = 'flex';
                alertBox.classList.add('alert-error');
                alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="font-size:1.2rem;"></i> Lỗi kết nối tới máy chủ. Vui lòng thử lại sau.';
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
