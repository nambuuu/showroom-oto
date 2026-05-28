<?php
require_once '../config/db.php';
$car_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($car_id === 0) {
    header('Location: cars.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoSuperCar | Chi Tiết Xe</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Mrs+Saint+Delafield&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
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

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-dark);
            min-height: 100vh;
            overflow-x: hidden;
            line-height: 1.6;
        }

        a { text-decoration: none; color: inherit; transition: color var(--transition); }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; color: var(--text-dark); }

        /* NAVBAR */
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 5%; background-color: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px); position: fixed; top: 0; left: 0;
            width: 100%; z-index: 1000; box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }
        .navbar-brand { font-family: 'Outfit', sans-serif; font-size: 24px; font-weight: 900; color: var(--text-dark); display: flex; align-items: center; gap: 10px; }
        .navbar-brand span { color: var(--gold); }
        .nav-links { display: flex; gap: 30px; list-style: none; }
        .nav-links li a { font-size: 15px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: var(--text-dark); position: relative; padding: 5px 0; }
        .nav-links li a::after { content: ''; position: absolute; bottom: 0; left: 0; width: 0; height: 2px; background-color: var(--gold); transition: width var(--transition); }
        .nav-links li a:hover::after { width: 100%; }
        .nav-links li a:hover { color: var(--gold); }
        .btn-nav { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: #fff; padding: 10px 24px; border-radius: 30px; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; border: none; cursor: pointer; box-shadow: 0 4px 15px var(--gold-glow); transition: all var(--transition); margin-left: 15px; }
        .btn-nav:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(212, 168, 67, 0.4); color: #fff; }

        /* DETAIL CONTAINER */
        .detail-container {
            padding: 120px 5% 50px;
            display: grid;
            grid-template-columns: 3fr 2fr;
            gap: 50px;
        }

        /* GALLERY */
        .gallery {
            display: flex; flex-direction: column; gap: 15px;
        }
        .main-img {
            width: 100%; height: 500px; border-radius: var(--radius-lg);
            overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            background: var(--bg-secondary);
        }
        .main-img img { width: 100%; height: 100%; object-fit: cover; }
        
        .thumb-list {
            display: flex; gap: 15px; overflow-x: auto; padding-bottom: 10px;
        }
        .thumb-item {
            width: 120px; height: 80px; border-radius: 10px; overflow: hidden;
            cursor: pointer; border: 2px solid transparent; opacity: 0.6;
            transition: all var(--transition); flex-shrink: 0; background: var(--bg-secondary);
        }
        .thumb-item.active { border-color: var(--gold); opacity: 1; }
        .thumb-item:hover { opacity: 1; }
        .thumb-item img { width: 100%; height: 100%; object-fit: cover; }

        /* INFO SECTIONS */
        .info-panel {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.03);
            border: 1px solid var(--border);
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .brand-badge {
            display: inline-block; background: var(--gold); color: #fff;
            padding: 6px 15px; border-radius: 20px; font-size: 13px; font-weight: 700;
            text-transform: uppercase; margin-bottom: 15px;
        }

        .car-title { font-size: 2.5rem; line-height: 1.2; margin-bottom: 20px; }
        .car-price { font-size: 2rem; font-weight: 800; color: var(--gold); margin-bottom: 30px; font-family: 'Outfit', sans-serif;}

        .btn-large {
            display: flex; justify-content: center; align-items: center; gap: 10px;
            width: 100%; padding: 18px; border-radius: 12px; font-size: 16px;
            font-weight: 700; text-transform: uppercase; cursor: pointer;
            transition: all var(--transition); border: none; margin-bottom: 15px;
        }
        .btn-book { background: var(--text-dark); color: #fff; }
        .btn-book:hover { background: var(--gold); box-shadow: 0 10px 20px var(--gold-glow); }
        
        .btn-compare { background: transparent; border: 2px solid var(--text-dark); color: var(--text-dark); }
        .btn-compare:hover { background: var(--bg-secondary); }

        .quick-specs {
            display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
            margin-top: 30px; padding-top: 30px; border-top: 1px solid var(--border);
        }
        .q-spec { display: flex; align-items: center; gap: 15px; }
        .q-spec i { font-size: 24px; color: var(--gold); width: 30px; text-align: center; }
        .q-spec-info span { display: block; font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;}
        .q-spec-info strong { font-size: 15px; color: var(--text-dark); }

        /* TABS for DESCRIPTION AND SPECS */
        .content-tabs { margin-top: 50px; grid-column: 1 / -1; }
        .tabs-header { display: flex; gap: 40px; border-bottom: 1px solid var(--border); margin-bottom: 40px; }
        .tab-btn {
            background: transparent; border: none; font-size: 1.1rem; font-weight: 700;
            color: var(--text-muted); padding: 15px 0; cursor: pointer;
            position: relative; font-family: 'Outfit', sans-serif;
        }
        .tab-btn.active { color: var(--text-dark); }
        .tab-btn::after {
            content: ''; position: absolute; bottom: -1px; left: 0; width: 0; height: 3px;
            background: var(--gold); transition: width var(--transition);
        }
        .tab-btn.active::after { width: 100%; }

        .tab-content { display: none; animation: fadeIn 0.5s; font-size: 1.1rem; color: #444; line-height: 1.8; }
        .tab-content.active { display: block; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .specs-table { width: 100%; border-collapse: collapse; }
        .specs-table tr { border-bottom: 1px solid var(--border); }
        .specs-table th { text-align: left; padding: 20px 10px; color: var(--text-muted); font-weight: 500; width: 40%; }
        .specs-table td { padding: 20px 10px; font-weight: 600; color: var(--text-dark); }

        /* Loading */
        #loadingLayer {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: #fff; z-index: 9999; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .detail-container { grid-template-columns: 1fr; }
            .info-panel { position: static; }
        }

        /* FOOTER */
        footer { background: #0f172a; color: #fff; padding: 80px 5% 30px; margin-top: 50px; }
        .footer-content { display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 40px; margin-bottom: 50px; }
        .footer-logo { font-family: 'Outfit', sans-serif; font-size: 24px; font-weight: 900; margin-bottom: 20px; }
        .footer-logo span { color: var(--gold); }
        .footer-about { color: var(--text-dim); font-size: 0.95rem; margin-bottom: 20px; line-height: 1.8; }
        .footer-title { font-size: 1.2rem; margin-bottom: 25px; color: #fff; }
        .footer-links { list-style: none; }
        .footer-links li { margin-bottom: 15px; }
        .footer-links a { color: var(--text-dim); transition: color var(--transition); }
        .footer-links a:hover { color: var(--gold); padding-left: 5px; }
        .contact-info li { display: flex; gap: 15px; margin-bottom: 20px; color: var(--text-dim); }
        .contact-info i { color: var(--gold); font-size: 1.2rem; margin-top: 3px; }
        .footer-bottom { text-align: center; padding-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); color: var(--text-dim); font-size: 0.9rem; }
    </style>
</head>
<body>

    <div id="loadingLayer">
        <i class="fa-solid fa-circle-notch fa-spin fa-3x" style="color: var(--gold); margin-bottom: 20px;"></i>
        <h2 style="color: var(--text-dark);">Đang Tải Dữ Liệu Xe...</h2>
    </div>

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">AUTO<span>SUPERCAR</span></a>
        <ul class="nav-links">
            <li><a href="index.php">Trang Chủ</a></li>
            <li><a href="cars.php">Khám Phá Xe</a></li>
            <li><a href="compare.php">So Sánh</a></li>
            <li><a href="about.php">Giới Thiệu</a></li>
            <li><a href="contact.php">Liên Hệ</a></li>
        </ul>
        <div style="display: flex; align-items: center; gap: 15px;">
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

    <!-- MAIN CONTENT -->
    <div class="detail-container">
        
        <!-- IMAGE GALLERY -->
        <div class="gallery">
            <div class="main-img">
                <img id="mainImage" src="" alt="Main Image" onerror="this.src='../assets/image/cars/Porche 911 turbo.jpg'">
            </div>
            <div class="thumb-list" id="thumbList">
                <!-- Thumbs populated by JS -->
            </div>
        </div>

        <!-- INFO PANEL -->
        <div class="info-panel">
            <div class="brand-badge" id="carBrandBadge">Loading...</div>
            <h1 class="car-title" id="carTitle">Loading...</h1>
            <div class="car-price" id="carPrice">...</div>

            <button class="btn-large btn-book" onclick="window.location.href='booking.php?car_id=<?php echo $car_id; ?>'">
                <i class="fa-solid fa-calendar-check"></i> Đặt Lịch Lái Thử
            </button>
            <button class="btn-large btn-compare" onclick="addToCompare()">
                <i class="fa-solid fa-code-compare"></i> Thêm Vào So Sánh
            </button>

            <div class="quick-specs">
                <div class="q-spec">
                    <i class="fa-solid fa-gauge-high"></i>
                    <div class="q-spec-info">
                        <span>Tốc Độ Tối Đa</span>
                        <strong id="qsSpeed">-- km/h</strong>
                    </div>
                </div>
                <div class="q-spec">
                    <i class="fa-solid fa-bolt"></i>
                    <div class="q-spec-info">
                        <span>Tăng Tốc 0-100</span>
                        <strong id="qsAccel">-- s</strong>
                    </div>
                </div>
                <div class="q-spec">
                    <i class="fa-solid fa-horse-head"></i>
                    <div class="q-spec-info">
                        <span>Công Suất</span>
                        <strong id="qsHp">-- HP</strong>
                    </div>
                </div>
                <div class="q-spec">
                    <i class="fa-solid fa-gas-pump"></i>
                    <div class="q-spec-info">
                        <span>Nhiên Liệu</span>
                        <strong id="qsFuel">--</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABS CONTENT -->
        <div class="content-tabs">
            <div class="tabs-header">
                <button class="tab-btn active" onclick="switchTab('desc')">Tổng Quan</button>
                <button class="tab-btn" onclick="switchTab('specs')">Thông Số Kỹ Thuật</button>
            </div>
            
            <div id="tab-desc" class="tab-content active">
                <div id="carDescription" style="white-space: pre-line;">Đang tải mô tả...</div>
            </div>
            
            <div id="tab-specs" class="tab-content">
                <table class="specs-table">
                    <tbody id="specsTableBody">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <div class="footer-bottom" style="border:none; padding: 0;">
            <p>&copy; <?php echo date('Y'); ?> AutoSuperCar Showroom. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        const carId = <?php echo $car_id; ?>;
        
        document.addEventListener('DOMContentLoaded', () => {
            fetchWeather();
            fetchCarData();
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
                .catch(error => console.error('Error fetching weather:', error));
        }

        function fetchCarData() {
            fetch(`../api/get_car.php?id=${carId}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('loadingLayer').style.display = 'none';
                    if (data.status === 'success') {
                        renderCarData(data.data);
                    } else {
                        alert('Không tìm thấy dữ liệu xe!');
                        window.location.href = 'cars.php';
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Lỗi khi tải dữ liệu xe!');
                });
        }

        function renderCarData(data) {
            const car = data.car;
            const specs = data.specifications || {};
            const images = data.images || [];

            // Title and basic info
            document.getElementById('carBrandBadge').textContent = car.brand_name || 'Hãng Xe';
            document.getElementById('carTitle').textContent = car.model_name;
            document.getElementById('carDescription').textContent = car.description || 'Chưa có thông tin mô tả chi tiết cho mẫu xe này.';
            document.title = car.model_name + ' | AutoSuperCar';

            const formatPrice = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(car.price);
            document.getElementById('carPrice').textContent = formatPrice;

            // Gallery
            const mainImg = document.getElementById('mainImage');
            const thumbList = document.getElementById('thumbList');
            
            if (images.length > 0) {
                // Set main image (find the one with is_main = 1 or the first one)
                let mainImageObj = images.find(img => img.is_main == 1) || images[0];
                mainImg.src = '../assets/image/cars/' + mainImageObj.image;

                // Set thumbnails
                thumbList.innerHTML = '';
                images.forEach((img, index) => {
                    const imgUrl = '../assets/image/cars/' + img.image;
                    const isActive = img.id === mainImageObj.id ? 'active' : '';
                    thumbList.innerHTML += `
                        <div class="thumb-item ${isActive}" onclick="changeMainImage('${imgUrl}', this)">
                            <img src="${imgUrl}" onerror="this.src='../assets/image/cars/Porche 911 turbo.jpg'">
                        </div>
                    `;
                });
            }

            // Quick Specs
            document.getElementById('qsSpeed').textContent = specs.top_speed ? specs.top_speed + ' km/h' : '-- km/h';
            document.getElementById('qsAccel').textContent = specs.acceleration ? specs.acceleration + ' s' : '-- s';
            document.getElementById('qsHp').textContent = specs.horsepower ? specs.horsepower + ' HP' : '-- HP';
            document.getElementById('qsFuel').textContent = specs.fuel_type || '--';

            // Full Specs Table
            const tbody = document.getElementById('specsTableBody');
            tbody.innerHTML = `
                <tr><th>Động Cơ</th><td>${specs.engine || '--'}</td></tr>
                <tr><th>Công Suất</th><td>${specs.horsepower ? specs.horsepower + ' HP' : '--'}</td></tr>
                <tr><th>Mô-men Xoắn</th><td>${specs.torque || '--'}</td></tr>
                <tr><th>Hộp Số</th><td>${specs.transmission || '--'}</td></tr>
                <tr><th>Loại Nhiên Liệu</th><td>${specs.fuel_type || '--'}</td></tr>
                <tr><th>Tiêu Thụ Nhiên Liệu</th><td>${specs.fuel_efficiency || '--'}</td></tr>
                <tr><th>Hệ Dẫn Động</th><td>${specs.drive_type || '--'}</td></tr>
                <tr><th>Số Chỗ Ngồi</th><td>${specs.seating || '--'}</td></tr>
                <tr><th>Tốc Độ Tối Đa</th><td>${specs.top_speed ? specs.top_speed + ' km/h' : '--'}</td></tr>
                <tr><th>Khả năng tăng tốc (0-100 km/h)</th><td>${specs.acceleration ? specs.acceleration + ' s' : '--'}</td></tr>
            `;
        }

        function changeMainImage(url, element) {
            document.getElementById('mainImage').src = url;
            document.querySelectorAll('.thumb-item').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
        }

        function switchTab(tabId) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById('tab-' + tabId).classList.add('active');
        }

        function addToCompare() {
            let compareList = JSON.parse(localStorage.getItem('compareList')) || [];
            const idStr = carId.toString();
            
            if (!compareList.includes(idStr)) {
                if (compareList.length >= 3) {
                    alert('Bạn chỉ có thể so sánh tối đa 3 xe cùng lúc.');
                    return;
                }
                compareList.push(idStr);
                localStorage.setItem('compareList', JSON.stringify(compareList));
                alert('Đã thêm xe vào danh sách so sánh!');
            } else {
                alert('Xe này đã có trong danh sách so sánh.');
            }
            // Navigate to compare page
            window.location.href = 'compare.php';
        }
    </script>
</body>
</html> 
