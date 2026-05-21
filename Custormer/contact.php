<?php
// customer/contact.php
require_once '../config/db.php';
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoSuperCar | Liên Hệ</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS VARIABLES — Customer Light Theme */
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-card: #ffffff;
            --border: #e2e8f0;
            --border-gold: rgba(212, 168, 67, 0.4);

            --gold: #d4a843;
            --gold-light: #f0c96a;
            --gold-dark: #a67c2e;
            --gold-glow: rgba(212, 168, 67, 0.25);

            --text-dark: #0f172a;
            --text-muted: #64748b;
            --text-dim: #94a3b8;
            
            --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --radius-lg: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-secondary);
            color: var(--text-dark);
            min-height: 100vh;
            overflow-x: hidden;
            line-height: 1.6;
        }

        a {
            text-decoration: none;
            color: inherit;
            transition: color var(--transition);
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Orbitron', sans-serif;
            color: var(--text-dark);
        }

        /* NAVIGATION BAR */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 5%;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }

        .navbar-brand {
            font-family: 'Orbitron', sans-serif;
            font-size: 24px;
            font-weight: 900;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand span { color: var(--gold); }

        .nav-links {
            display: flex;
            gap: 30px;
            list-style: none;
        }

        .nav-links li a {
            font-size: 15px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-dark);
            position: relative;
            padding: 5px 0;
        }

        .nav-links li a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--gold);
            transition: width var(--transition);
        }

        .nav-links li a:hover::after,
        .nav-links li a.active::after { width: 100%; }

        .nav-links li a:hover,
        .nav-links li a.active { color: var(--gold); }

        .btn-nav {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: #fff;
            padding: 10px 24px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px var(--gold-glow);
            transition: all var(--transition);
            margin-left: 20px;
        }

        .btn-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 168, 67, 0.4);
            color: #fff;
        }

        /* PAGE HEADER */
        .page-header {
            padding: 150px 5% 60px;
            background: linear-gradient(to right, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.4) 100%),
                        url('../assets/images/cars/mercedes-s-class.jpg') no-repeat center 40%/cover;
            text-align: center;
            border-bottom: 1px solid var(--border);
        }

        .page-header h1 {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .page-header p {
            font-size: 1.1rem;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
        }

        /* CONTACT SECTION */
        .contact-section {
            padding: 80px 5%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .contact-container {
            display: flex;
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            box-shadow: 0 15px 40px rgba(0,0,0,0.08);
            overflow: hidden;
            width: 100%;
            max-width: 1100px;
            border: 1px solid var(--border);
        }

        .contact-info {
            flex: 1;
            background: var(--text-dark);
            color: #fff;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
        }

        .contact-info h2 {
            color: var(--gold);
            font-size: 2.2rem;
            margin-bottom: 20px;
        }

        .contact-info > p {
            color: var(--text-dim);
            margin-bottom: 40px;
            font-size: 1.05rem;
            line-height: 1.8;
        }

        .info-list {
            list-style: none;
            margin-bottom: 40px;
        }

        .info-list li {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 30px;
            font-size: 1.05rem;
        }

        .info-list i {
            color: var(--text-dark);
            background: var(--gold);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
            margin-top: 3px;
        }

        .info-content h4 {
            color: #fff;
            font-size: 1.1rem;
            margin-bottom: 5px;
            font-family: 'Inter', sans-serif;
        }

        .info-content p {
            color: var(--text-dim);
            font-size: 0.95rem;
        }

        .contact-form {
            flex: 1.2;
            padding: 60px 50px;
        }

        .form-title {
            font-size: 1.8rem;
            margin-bottom: 30px;
            color: var(--text-dark);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            background-color: var(--bg-secondary);
            transition: all var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 4px var(--gold-glow);
            background-color: #fff;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: #fff;
            padding: 16px 30px;
            border: none;
            border-radius: 8px;
            width: 100%;
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all var(--transition);
            box-shadow: 0 4px 15px var(--gold-glow);
            font-family: 'Inter', sans-serif;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 168, 67, 0.4);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* ALERTS */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* FOOTER */
        footer {
            background: #0f172a;
            color: #fff;
            padding: 80px 5% 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 40px;
            margin-bottom: 50px;
        }

        .footer-logo {
            font-family: 'Orbitron', sans-serif;
            font-size: 24px;
            font-weight: 900;
            margin-bottom: 20px;
        }

        .footer-logo span { color: var(--gold); }

        .footer-about {
            color: var(--text-dim);
            font-size: 0.95rem;
            margin-bottom: 20px;
            line-height: 1.8;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            transition: all var(--transition);
        }

        .social-links a:hover {
            background: var(--gold);
            transform: translateY(-3px);
        }

        .footer-title {
            font-size: 1.2rem;
            margin-bottom: 25px;
            color: #fff;
        }

        .footer-links { list-style: none; }
        .footer-links li { margin-bottom: 15px; }
        .footer-links a {
            color: var(--text-dim);
            transition: color var(--transition);
        }

        .footer-links a:hover {
            color: var(--gold);
            padding-left: 5px;
        }

        .contact-info li {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            color: var(--text-dim);
        }

        .contact-info i {
            color: var(--gold);
            font-size: 1.2rem;
            margin-top: 3px;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: var(--text-dim);
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .contact-container { flex-direction: column; }
            .contact-info, .contact-form { padding: 40px 30px; }
            .footer-content { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .form-row { flex-direction: column; gap: 0; }
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
            <li><a href="about.php">Giới Thiệu</a></li>
            <li><a href="contact.php" class="active">Liên Hệ</a></li>
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
        <h1>Liên Hệ & Tư Vấn</h1>
        <p>Đội ngũ chuyên gia của AutoSuperCar luôn sẵn sàng hỗ trợ, giải đáp mọi thắc mắc và mang đến cho bạn trải nghiệm dịch vụ đẳng cấp nhất.</p>
    </div>

    <!-- CONTACT SECTION -->
    <section class="contact-section">
        <div class="contact-container">
            <!-- Left Info -->
            <div class="contact-info">
                <h2>Thông Tin Showroom</h2>
                <p>Khám phá bộ sưu tập những siêu phẩm xe hơi và nhận sự tư vấn chuyên sâu từ đội ngũ AutoSuperCar.</p>
                
                <ul class="info-list">
                    <li>
                        <i class="fa-solid fa-location-dot"></i>
                        <div class="info-content">
                            <h4>Địa chỉ Showroom</h4>
                            <p>178 Đại Mỗ, Quận Nam Từ Liêm, TP. Hà Nội</p>
                        </div>
                    </li>
                    <li>
                        <i class="fa-solid fa-phone"></i>
                        <div class="info-content">
                            <h4>Hotline CSKH</h4>
                            <p>+84 (0) 356 827 852</p>
                            <p style="font-size: 0.85rem; margin-top: 5px;">Hỗ trợ 24/7</p>
                        </div>
                    </li>
                    <li>
                        <i class="fa-solid fa-envelope"></i>
                        <div class="info-content">
                            <h4>Email Hỗ Trợ</h4>
                            <p>nnnam12341@gmail.com</p>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Right Form -->
            <div class="contact-form">
                <h3 class="form-title">Gửi Yêu Cầu Cho Chúng Tôi</h3>
                
                <div id="contactAlert" class="alert" style="display: none;"></div>

                <form id="formContact">
                    <div class="form-group">
                        <label class="form-label">Họ & Tên <span style="color:red">*</span></label>
                        <input type="text" name="full_name" class="form-control" placeholder="Nhập họ và tên đầy đủ" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Email <span style="color:red">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Số Điện Thoại</label>
                            <input type="tel" name="phone" class="form-control" placeholder="09xxxx...">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Chủ Đề <span style="color:red">*</span></label>
                        <input type="text" name="subject" class="form-control" placeholder="Vấn đề bạn cần hỗ trợ là gì?" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nội Dung Yêu Cầu <span style="color:red">*</span></label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Chi tiết yêu cầu của bạn..." required></textarea>
                    </div>

                    <button type="submit" class="btn-submit" id="btnSubmitContact">
                        Gửi Yêu Cầu <i class="fa-solid fa-paper-plane" style="margin-left: 8px;"></i>
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

            const formContact = document.getElementById('formContact');
            formContact.addEventListener('submit', function(e) {
                e.preventDefault();
                submitContact();
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

        function submitContact() {
            const form = document.getElementById('formContact');
            const btn = document.getElementById('btnSubmitContact');
            const alertBox = document.getElementById('contactAlert');
            const formData = new FormData(form);
            
            // Chuyển FormData sang dạng object
            const data = {};
            formData.forEach((value, key) => { data[key] = value; });

            // Trạng thái Loading
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Đang gửi...';
            alertBox.style.display = 'none';
            alertBox.className = 'alert';

            fetch('../api/post_contact.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(res => {
                btn.disabled = false;
                btn.innerHTML = 'Gửi Yêu Cầu <i class="fa-solid fa-paper-plane" style="margin-left: 8px;"></i>';
                
                alertBox.style.display = 'flex';
                if (res.status === 'success') {
                    alertBox.classList.add('alert-success');
                    alertBox.innerHTML = '<i class="fa-solid fa-circle-check" style="font-size:1.2rem;"></i> ' + res.message;
                    form.reset();
                } else {
                    alertBox.classList.add('alert-error');
                    alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="font-size:1.2rem;"></i> ' + (res.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerHTML = 'Gửi Yêu Cầu <i class="fa-solid fa-paper-plane" style="margin-left: 8px;"></i>';
                alertBox.style.display = 'flex';
                alertBox.className = 'alert alert-error';
                alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="font-size:1.2rem;"></i> Lỗi kết nối tới máy chủ.';
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
