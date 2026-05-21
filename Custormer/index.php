<?php
// customer/index.php
require_once '../config/db.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoSuperCar Showroom | Trang Chủ</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* 
           CSS VARIABLES — Customer Light Theme
       */
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-card: #ffffff;
            --bg-card-hover: #f1f5f9;
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

        /* 
           RESET & BASE
        */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-primary);
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

        /* 
           NAVIGATION BAR
        */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 5%;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            transition: all var(--transition);
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

        .navbar-brand span {
            color: var(--gold);
        }

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
        .nav-links li a.active::after {
            width: 100%;
        }

        .nav-links li a:hover,
        .nav-links li a.active {
            color: var(--gold);
        }

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

        /* 
           HERO SECTION
        */
        .hero {
            height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            padding: 0 5%;
            margin-top: 0;
            background: linear-gradient(to right, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.1) 100%),
                        url('../assets/image/cars/Porche\ 911\ turbo.jpg') no-repeat center center/cover;
        }

        .hero-content {
            max-width: 600px;
            z-index: 2;
            animation: fadeIn 1.5s ease-out;
        }

        .hero-content h1 {
            font-size: 4rem;
            line-height: 1.1;
            margin-bottom: 20px;
            color: var(--text-dark);
            text-shadow: 2px 2px 4px rgba(255,255,255,0.5);
        }

        .hero-content p {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 30px;
            font-weight: 500;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--text-dark);
            color: #fff;
            padding: 15px 35px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all var(--transition);
        }

        .btn-primary:hover {
            background: var(--gold);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            color: #fff;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* 
           FEATURED CARS SECTION
        */
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .section-title p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .featured-cars {
            padding: 100px 5%;
            background-color: var(--bg-secondary);
        }

        .cars-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
        }

        .car-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all var(--transition);
            border: 1px solid var(--border);
        }

        .car-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: var(--border-gold);
        }

        .car-img-wrapper {
            position: relative;
            height: 220px;
            overflow: hidden;
        }

        .car-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .car-card:hover .car-img-wrapper img {
            transform: scale(1.05);
        }

        .car-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--gold);
            color: #fff;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .car-info {
            padding: 25px;
        }

        .car-brand {
            color: var(--gold);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .car-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--text-dark);
            font-family: 'Orbitron', sans-serif;
        }

        .car-specs {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }

        .spec-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            color: var(--text-muted);
            font-size: 13px;
        }

        .spec-item i {
            color: var(--gold);
            font-size: 16px;
        }

        .car-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .car-price {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-dark);
        }

        .btn-details {
            background: transparent;
            border: 2px solid var(--text-dark);
            color: var(--text-dark);
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
            transition: all var(--transition);
        }

        .car-card:hover .btn-details {
            background: var(--text-dark);
            color: #fff;
        }

        /* ============================================================
           SERVICES SECTION
        ============================================================ */
        .services {
            padding: 100px 5%;
            background: var(--bg-primary);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
        }

        .service-card {
            text-align: center;
            padding: 40px 30px;
            background: var(--bg-secondary);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            transition: all var(--transition);
        }

        .service-card:hover {
            transform: translateY(-5px);
            border-color: var(--gold);
            box-shadow: 0 10px 30px rgba(212, 168, 67, 0.1);
        }

        .service-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, rgba(212, 168, 67, 0.1), rgba(212, 168, 67, 0.05));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: var(--gold);
        }

        .service-card h3 {
            margin-bottom: 15px;
            font-size: 1.25rem;
        }

        .service-card p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        /* ============================================================
           FOOTER
        ============================================================ */
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

        .footer-logo span {
            color: var(--gold);
        }

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

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 15px;
        }

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
            .hero-content h1 { font-size: 3rem; }
            .services-grid { grid-template-columns: 1fr; }
            .footer-content { grid-template-columns: 1fr 1fr; }
            .nav-links { display: none; /* Can add hamburger menu logic later */ }
        }
        @media (max-width: 768px) {
            .footer-content { grid-template-columns: 1fr; }
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
            <li><a href="index.php" class="active">Trang Chủ</a></li>
            <li><a href="cars.php">Khám Phá Xe</a></li>
            <li><a href="compare.php">So Sánh</a></li>
            <li><a href="about.php">Giới Thiệu</a></li>
            <li><a href="contact.php">Liên Hệ</a></li>
        </ul>

        <div style="display: flex; align-items: center; gap: 15px;">
            <!-- Weather Widget -->
            <div id="weatherWidget" style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--text-dark); margin-right: 10px; padding-right: 15px; border-right: 1px solid var(--border);" title="Thời tiết Hà Nội hiện tại">
                <i class="fa-solid fa-cloud-sun" style="color: var(--gold); font-size: 16px;"></i> 
                <span id="weatherTemp">--°C</span>
            </div>

            <?php if (isset($_SESSION['admin_id'])): ?>
                <span style="font-weight: 600; color: var(--text-dark); font-size: 14px;">
                    <i class="fa-solid fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Khách hàng'); ?>
                </span>
                <a href="../logout.php" style="color: #ef4444; font-weight: 600; text-decoration: none; font-size: 14px;"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
            <?php else: ?>
                <a href="../login.php" style="color: var(--text-dark); font-weight: 600; text-decoration: none; font-size: 14px;"><i class="fa-solid fa-user"></i> Đăng nhập</a>
            <?php endif; ?>
            <a href="booking.php" class="btn-nav"><i class="fa-solid fa-calendar-check" style="margin-right: 8px;"></i> Lái Thử</a>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-content">
            <h1>Tuyên Ngôn Của Sự Đẳng Cấp</h1>
            <p>Trải nghiệm tinh hoa công nghệ và thiết kế hoàn mỹ. AutoSuperCar mang đến những kiệt tác xe hơi hàng đầu thế giới dành riêng cho bạn.</p>
            <a href="cars.php" class="btn-primary">
                Khám Phá Ngay <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </section>

    <!-- FEATURED CARS SECTION -->
    <section class="featured-cars">
        <div class="section-title">
            <h2>Mẫu Xe Nổi Bật</h2>
            <p>Tuyển tập những siêu phẩm được ưa chuộng nhất tại AutoSuperCar</p>
        </div>
        
        <div class="cars-grid" id="featuredCarsContainer">
            <!-- Loading indicator -->
            <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
                <i class="fa-solid fa-circle-notch fa-spin fa-3x" style="color: var(--gold);"></i>
                <p style="margin-top: 15px; color: var(--text-muted);">Đang tải dữ liệu xe...</p>
            </div>
            
            <!-- JavaScript will populate cars here based on API -->
        </div>
        
        <div style="text-align: center; margin-top: 50px;">
            <a href="cars.php" class="btn-primary" style="background: transparent; border: 2px solid var(--text-dark); color: var(--text-dark);">
                Xem Tất Cả Mẫu Xe
            </a>
        </div>
    </section>

    <!-- SERVICES SECTION -->
    <section class="services">
        <div class="section-title">
            <h2>Dịch Vụ Đẳng Cấp</h2>
            <p>Cam kết mang lại trải nghiệm hoàn hảo nhất cho khách hàng</p>
        </div>
        
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <h3>Bảo Hành Toàn Cầu</h3>
                <p>Chính sách bảo hành chính hãng lên đến 5 năm, mang lại sự an tâm tuyệt đối trên mọi hành trình.</p>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                <h3>Tài Chính Linh Hoạt</h3>
                <p>Hỗ trợ vay mua xe với lãi suất ưu đãi, thủ tục nhanh chóng, duyệt hồ sơ trong 24 giờ.</p>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fa-solid fa-user-tie"></i></div>
                <h3>Chăm Sóc VIP 24/7</h3>
                <p>Đội ngũ chuyên viên tư vấn và kỹ thuật viên sẵn sàng hỗ trợ bạn bất cứ lúc nào, bất cứ nơi đâu.</p>
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
                        <span>178 Đại Mỗ,Quận Nam Từ Liêm, TP. Hà Nội</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-phone"></i>
                        <span>+84 (0) 356 827 852</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-envelope"></i>
                        <span>[nnnam12341@gmail.com]</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> AutoSuperCar Showroom. All Rights Reserved. Designed for Luxury.</p>
        </div>
    </footer>

    <!-- JAVASCRIPT FOR API FETCH -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Navbar scroll effect
            window.addEventListener('scroll', function() {
                const navbar = document.querySelector('.navbar');
                if (window.scrollY > 50) {
                    navbar.style.padding = '15px 5%';
                    navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.1)';
                } else {
                    navbar.style.padding = '20px 5%';
                    navbar.style.boxShadow = '0 2px 15px rgba(0,0,0,0.05)';
                }
            });

            // Fetch Featured Cars
            fetchFeaturedCars();
            
            // Fetch Weather
            fetchWeather();
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
                    document.getElementById('weatherWidget').style.display = 'none';
                });
        }

        function fetchFeaturedCars() {
            // Using the API endpoint mentioned in README
            // Assuming get_cars.php returns a JSON with cars data
            fetch('../api/get_cars.php?limit=3')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const container = document.getElementById('featuredCarsContainer');
                    
                    if (data && data.status === 'success' && data.data.length > 0) {
                        container.innerHTML = ''; // Clear loading
                        
                        data.data.forEach(car => {
                            // Helper to format price
                            const formatPrice = (price) => {
                                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
                            };
                            
                            // Determine image (use main_image or fallback to generic)
                            // We know there are images like 'Porche 911 turbo.jpg', 'maybach.jpg'
                            let imgUrl = car.main_image ? '../assets/image/cars/' + car.main_image : '../assets/image/cars/Porche 911 turbo.jpg';
                            
                            const html = `
                                <div class="car-card">
                                    <div class="car-img-wrapper">
                                        <span class="car-badge">Mới Nhất</span>
                                        <img src="${imgUrl}" alt="${car.model_name}" onerror="this.src='../assets/image/cars/Toyota Camry.jpg'">
                                    </div>
                                    <div class="car-info">
                                        <div class="car-brand">${car.brand_name || 'Hãng Xe'}</div>
                                        <h3 class="car-title">${car.model_name}</h3>
                                        
                                        <div class="car-specs">
                                            <div class="spec-item">
                                                <i class="fa-solid fa-gauge-high"></i>
                                                <span>${car.category || 'N/A'}</span>
                                            </div>
                                            <div class="spec-item">
                                                <i class="fa-solid fa-calendar-days"></i>
                                                <span>${car.year || '2024'}</span>
                                            </div>
                                            <div class="spec-item">
                                                <i class="fa-solid fa-gas-pump"></i>
                                                <span>Tự Động</span>
                                            </div>
                                        </div>
                                        
                                        <div class="car-footer">
                                            <div class="car-price">${formatPrice(car.price)}</div>
                                            <a href="car_detail.php?id=${car.id}" class="btn-details">Chi Tiết</a>
                                        </div>
                                    </div>
                                </div>
                            `;
                            container.insertAdjacentHTML('beforeend', html);
                        });
                    } else {
                        // If no data, show static mockups to maintain the beautiful design
                        showStaticMockups(container);
                    }
                })
                .catch(error => {
                    console.error('Error fetching cars:', error);
                    const container = document.getElementById('featuredCarsContainer');
                    showStaticMockups(container);
                });
        }

        // Fallback static mockups if DB is empty
        function showStaticMockups(container) {
            container.innerHTML = `
                <div class="car-card">
                    <div class="car-img-wrapper">
                        <span class="car-badge">Bán Chạy</span>
                        <img src="../assets/image/cars/Porche 911 turbo.jpg" alt="Porsche 911 Turbo">
                    </div>
                    <div class="car-info">
                        <div class="car-brand">PORSCHE</div>
                        <h3 class="car-title">Porsche 911 Turbo S</h3>
                        
                        <div class="car-specs">
                            <div class="spec-item">
                                <i class="fa-solid fa-gauge-high"></i>
                                <span>Thể thao</span>
                            </div>
                            <div class="spec-item">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span>2024</span>
                            </div>
                            <div class="spec-item">
                                <i class="fa-solid fa-gas-pump"></i>
                                <span>Xăng</span>
                            </div>
                        </div>
                        
                        <div class="car-footer">
                            <div class="car-price">15.540.000.000 ₫</div>
                            <a href="car_detail.php?id=1" class="btn-details">Chi Tiết</a>
                        </div>
                    </div>
                </div>

                <div class="car-card">
                    <div class="car-img-wrapper">
                        <span class="car-badge" style="background: #0f172a;">Độc Quyền</span>
                        <img src="../assets/image/cars/maybach.jpg" alt="Mercedes Maybach">
                    </div>
                    <div class="car-info">
                        <div class="car-brand">MERCEDES-BENZ</div>
                        <h3 class="car-title">Maybach S680</h3>
                        
                        <div class="car-specs">
                            <div class="spec-item">
                                <i class="fa-solid fa-gauge-high"></i>
                                <span>Sedan</span>
                            </div>
                            <div class="spec-item">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span>2024</span>
                            </div>
                            <div class="spec-item">
                                <i class="fa-solid fa-gas-pump"></i>
                                <span>Xăng</span>
                            </div>
                        </div>
                        
                        <div class="car-footer">
                            <div class="car-price">15.990.000.000 ₫</div>
                            <a href="car_detail.php?id=2" class="btn-details">Chi Tiết</a>
                        </div>
                    </div>
                </div>

                <div class="car-card">
                    <div class="car-img-wrapper">
                        <span class="car-badge">Thể Thao</span>
                        <img src="../assets/image/cars/lambo.jpg" alt="Lamborghini Aventador">
                    </div>
                    <div class="car-info">
                        <div class="car-brand">LAMBORGHINI</div>
                        <h3 class="car-title">Aventador SVJ</h3>
                        
                        <div class="car-specs">
                            <div class="spec-item">
                                <i class="fa-solid fa-gauge-high"></i>
                                <span>Siêu xe</span>
                            </div>
                            <div class="spec-item">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span>2023</span>
                            </div>
                            <div class="spec-item">
                                <i class="fa-solid fa-gas-pump"></i>
                                <span>Xăng</span>
                            </div>
                        </div>
                        
                        <div class="car-footer">
                            <div class="car-price">60.000.000.000 ₫</div>
                            <a href="car_detail.php?id=3" class="btn-details">Chi Tiết</a>
                        </div>
                    </div>
                </div>
            `;
        }
    </script>
</body>
</html>
