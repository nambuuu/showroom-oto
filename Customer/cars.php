<?php
require_once '../config/db.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto DreamCars | Explore Cars</title>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-secondary);
            color: var(--text-dark);
            min-height: 100vh;
            overflow-x: hidden;
            line-height: 1.6;
        }

        a { text-decoration: none; color: inherit; transition: color var(--transition); }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; color: var(--text-dark); }

        /* NAVBAR */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 5%;
            background-color: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }

        .navbar-brand {
            font-family: 'Outfit', sans-serif;
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
            content: ''; position: absolute; bottom: 0; left: 0; width: 0; height: 2px;
            background-color: var(--gold); transition: width var(--transition);
        }

        .nav-links li a:hover::after, .nav-links li a.active::after { width: 100%; }
        .nav-links li a:hover, .nav-links li a.active { color: var(--gold); }

        .btn-nav {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: #fff; padding: 10px 24px; border-radius: 30px;
            font-weight: 600; font-size: 14px; text-transform: uppercase;
            letter-spacing: 1px; border: none; cursor: pointer;
            box-shadow: 0 4px 15px var(--gold-glow); transition: all var(--transition);
            margin-left: 15px;
        }
        .btn-nav:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(212, 168, 67, 0.4); color: #fff; }

        /* HEADER SECTION */
        .page-header {
            padding: 120px 5% 40px;
            background: var(--bg-primary);
            text-align: center;
            border-bottom: 1px solid var(--border);
        }
        .page-header h1 { font-size: 2.5rem; margin-bottom: 10px; }
        .page-header p { color: var(--text-muted); font-size: 1.1rem; }

        /* SHOWROOM LAYOUT */
        .showroom-layout {
            display: flex;
            gap: 20px;
            padding: 40px 2% 40px 2%;
            align-items: flex-start;
        }

        /* SIDEBAR FILTER */
        .sidebar-filter {
            width: 240px;
            flex-shrink: 0;
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            position: sticky;
            top: 90px;
        }

        .filter-title {
            font-size: 0.85rem;
            margin-bottom: 14px;
            color: var(--text-dark);
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid var(--gold);
            display: inline-block;
            padding-bottom: 4px;
        }

        .filter-group {
            display: flex;
            align-items: center;
            background: var(--bg-secondary);
            border-radius: 10px;
            padding: 4px 10px;
            border: 1px solid var(--border);
            margin-bottom: 10px;
            transition: all var(--transition);
        }

        .filter-group:focus-within, .filter-group:hover {
            border-color: var(--gold);
        }

        .filter-group i { color: var(--gold); margin-right: 8px; width: 16px; text-align: center; font-size: 12px; }
        
        .filter-group input, .filter-group select {
            border: none;
            background: transparent;
            padding: 9px 0;
            font-size: 13px;
            color: var(--text-dark);
            outline: none;
            font-family: 'Outfit', sans-serif;
            width: 100%;
        }

        .filter-group select { cursor: pointer; }

        /* CARS CONTENT */
        .cars-content {
            flex: 1;
            min-width: 0;
        }


        .cars-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .car-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all var(--transition);
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
        }

        .car-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: var(--border-gold);
        }

        .car-img-wrapper {
            position: relative;
            aspect-ratio: 16 / 9;
            width: 100%;
            overflow: hidden;
            background-color: transparent;
            padding: 10px; /* Add a little padding so cars don't touch the edges */
        }

        .car-img-wrapper img {
            width: 100%; height: 100%; object-fit: contain;
            transition: transform 0.5s ease;
        }

        .car-card:hover .car-img-wrapper img { transform: scale(1.05); }

        .car-badge {
            position: absolute; top: 15px; right: 15px;
            background: var(--gold); color: #fff;
            padding: 5px 12px; border-radius: 20px;
            font-size: 12px; font-weight: 700; text-transform: uppercase;
        }

        .car-info {
            padding: 25px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .car-brand {
            color: var(--gold); font-size: 12px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;
        }

        .car-title {
            font-size: 1.25rem; font-weight: 700; margin-bottom: 15px;
            color: var(--text-dark); font-family: 'Outfit', sans-serif;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .car-specs {
            display: flex; justify-content: space-between;
            margin-bottom: 20px; padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }

        .spec-item { display: flex; flex-direction: column; align-items: center; gap: 5px; color: var(--text-muted); font-size: 13px; }
        .spec-item i { color: var(--gold); font-size: 16px; }

        .car-footer {
            display: flex; justify-content: space-between; align-items: center;
            margin-top: auto;
        }

        .car-price { font-size: 1.4rem; font-weight: 800; color: var(--text-dark); }

        .action-buttons {
            display: flex; gap: 10px;
        }

        .btn-action {
            background: transparent; border: 1px solid var(--border);
            color: var(--text-dark); padding: 8px 12px; border-radius: 8px;
            font-size: 13px; cursor: pointer; transition: all var(--transition);
            display: inline-flex; align-items: center; justify-content: center;
        }

        .btn-action:hover { background: var(--bg-secondary); border-color: var(--gold); color: var(--gold); }
        .btn-action.active { background: var(--gold); color: #fff; border-color: var(--gold); }

        .btn-details {
            background: var(--text-dark); border: none;
            color: #fff; padding: 8px 20px; border-radius: 20px;
            font-weight: 600; font-size: 13px; transition: all var(--transition);
        }

        .btn-details:hover { background: var(--gold); }

        .loading, .no-results {
            text-align: center; grid-column: 1/-1; padding: 50px;
            color: var(--text-muted); font-size: 1.1rem;
        }

        .pagination {
            display: flex; justify-content: center; gap: 10px;
            margin-top: 40px;
        }
        .page-btn {
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: var(--bg-card); border: 1px solid var(--border);
            color: var(--text-dark); cursor: pointer; font-weight: 600;
            transition: all var(--transition);
        }
        .page-btn:hover, .page-btn.active {
            background: var(--gold); color: #fff; border-color: var(--gold);
        }

        /* COMPARE FLOAT WIDGET */
        .compare-widget {
            position: fixed; bottom: 30px; right: 30px;
            background: var(--text-dark); color: #fff;
            padding: 15px 25px; border-radius: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            display: flex; align-items: center; gap: 15px;
            z-index: 1000; transform: translateY(100px); opacity: 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .compare-widget.show { transform: translateY(0); opacity: 1; }
        .compare-count {
            background: var(--gold); color: #fff;
            width: 24px; height: 24px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700;
        }
        .btn-compare-go {
            background: transparent; border: 1px solid var(--gold);
            color: var(--gold); padding: 5px 15px; border-radius: 20px;
            text-decoration: none;
            font-size: 13px; font-weight: 600; transition: all var(--transition);
        }
        .btn-compare-go:hover { background: var(--gold); color: #fff; }

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

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .showroom-layout {
                flex-direction: column;
                padding: 30px 5%;
            }
            .sidebar-filter {
                width: 100%;
                position: static;
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
                padding: 20px;
                margin-bottom: 20px;
            }
            .filter-title {
                width: 100%;
                margin-bottom: 10px;
            }
            .filter-group {
                flex: 1;
                min-width: 200px;
                margin-bottom: 0;
            }
        }
        @media (max-width: 768px) {
            .filter-group {
                min-width: 100%;
            }
            .cars-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">AUTO<span>DREAMCARS</span></a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="cars.php" class="active">Explore Cars</a></li>
            <li><a href="compare.php">Compare</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
        <div style="display: flex; align-items: center; gap: 15px;">
            <div id="weatherWidget" style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--text-dark); margin-right: 10px; padding-right: 15px; border-right: 1px solid var(--border);" title="Current Hanoi weather">
                <i class="fa-solid fa-cloud-sun" style="color: var(--gold); font-size: 16px;"></i> 
                <span id="weatherTemp">--°C</span>
            </div>
            <?php if (isset($_SESSION['admin_id'])): ?>
                <span style="font-weight: 600; color: var(--text-dark); font-size: 14px;">
                    <i class="fa-solid fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Customer'); ?>
                </span>
                <a href="../logout.php" style="color: #ef4444; font-weight: 600; text-decoration: none; font-size: 14px;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            <?php else: ?>
                <a href="../login.php" style="color: var(--text-dark); font-weight: 600; text-decoration: none; font-size: 14px;"><i class="fa-solid fa-user"></i> Login</a>
            <?php endif; ?>
            <a href="booking.php" class="btn-nav"><i class="fa-solid fa-calendar-check" style="margin-right: 8px;"></i> Test Drive</a>
        </div>
    </nav>

    <!-- HEADER -->
    <div class="page-header">
        <h1>Explore The Collection</h1>
        <p>Find the masterpiece that fits your style</p>
    </div>

    <!-- MAIN LAYOUT -->
    <div class="showroom-layout">
        <!-- FILTER SIDEBAR -->
        <aside class="sidebar-filter">
            <h3 class="filter-title">Search Filters</h3>
            <div class="filter-group">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" placeholder="Car name, e.g. Porsche...">
            </div>
            <div class="filter-group">
                <i class="fa-solid fa-car"></i>
                <select id="categoryFilter">
                    <option value="">All categories</option>
                    <option value="sedan">Sedan</option>
                    <option value="suv">SUV</option>
                    <option value="hatchback">Hatchback</option>
                    <option value="truck">Truck</option>
                    <option value="coupe">Coupe</option>
                </select>
            </div>
            <div class="filter-group">
                <i class="fa-solid fa-building"></i>
                <select id="brandFilter">
                    <option value="">All brands</option>
                    <!-- Thêm qua API sau -->
                </select>
            </div>
            <div class="filter-group">
                <i class="fa-solid fa-users"></i>
                <select id="seatingFilter">
                    <option value="">All seating capacities</option>
                    <option value="2">2 seats</option>
                    <option value="4">4 seats</option>
                    <option value="5">5 seats</option>
                    <option value="6">6 seats</option>
                    <option value="7">7 seats</option>
                    <option value="8">8 seats</option>
                    <option value="9">9 seats</option>
                </select>
            </div>
            <div class="filter-group">
                <i class="fa-solid fa-money-bill"></i>
                <select id="priceFilter">
                    <option value="">All prices</option>
                    <option value="1000000000">Under 1 billion</option>
                    <option value="5000000000">Under 5 billion</option>
                    <option value="10000000000">Under 10 billion</option>
                    <option value="20000000000">Under 20 billion</option>
                </select>
            </div>
            <div class="filter-group">
                <i class="fa-solid fa-calendar-days"></i>
                <select id="yearFilter">
                    <option value="">All years</option>
                    <option value="2026">2026</option>
                    <option value="2025">2025</option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                    <option value="2022">2022</option>
                    <option value="2021">2021</option>
                    <option value="2020">2020</option>
                </select>
            </div>
        </aside>

        <!-- CARS CONTENT -->
        <div class="cars-content">
            <div class="cars-grid" id="carsContainer">
                <div class="loading"><i class="fa-solid fa-circle-notch fa-spin fa-2x"></i><br><br>Loading data...</div>
            </div>
            <div class="pagination" id="paginationContainer"></div>
        </div>
    </div>

    <!-- COMPARE WIDGET -->
    <div class="compare-widget" id="compareWidget">
        <div>Selected for comparison:</div>
        <div class="compare-count" id="compareCount">0</div>
        <a href="compare.php" class="btn-compare-go">Compare Now</a>
        <button class="btn-action" style="color: #fff; border:none; margin-left: 10px;" onclick="clearCompare()"><i class="fa-solid fa-trash"></i></button>
    </div>

    <!-- FOOTER -->
    <footer>
        <div class="footer-bottom" style="border:none; padding: 0;">
            <p>&copy; <?php echo date('Y'); ?> Auto DreamCars Showroom. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        let currentPage = 1;
        let compareList = JSON.parse(localStorage.getItem('compareList')) || [];

        document.addEventListener('DOMContentLoaded', () => {
            fetchWeather();
            fetchBrands();
            fetchCars();
            updateCompareWidget();

            document.getElementById('searchInput').addEventListener('input', debounce(() => { currentPage = 1; fetchCars(); }, 500));
            document.getElementById('categoryFilter').addEventListener('change', () => { currentPage = 1; fetchCars(); });
            document.getElementById('brandFilter').addEventListener('change', () => { currentPage = 1; fetchCars(); });
            document.getElementById('seatingFilter').addEventListener('change', () => { currentPage = 1; fetchCars(); });
            document.getElementById('priceFilter').addEventListener('change', () => { currentPage = 1; fetchCars(); });
            document.getElementById('yearFilter').addEventListener('change', () => { currentPage = 1; fetchCars(); });
        });

        function debounce(func, timeout = 300) {
            let timer;
            return (...args) => { clearTimeout(timer); timer = setTimeout(() => { func.apply(this, args); }, timeout); };
        }

        function fetchWeather() {
            fetch('../api/get_weather.php')
                .then(response => response.json())
                .then(data => {
                    if (data && data.current_weather) {
                        const temp = Math.round(data.current_weather.temperature);
                        document.getElementById('weatherTemp').innerHTML = `${temp}°C <span style="color: var(--text-muted); font-weight: 500; font-size: 11px;">Hanoi</span>`;
                    }
                })
                .catch(error => console.error('Error fetching weather:', error));
        }

        function fetchBrands() {
            fetch('../api/get_brands.php')
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        const select = document.getElementById('brandFilter');
                        data.data.forEach(brand => {
                            select.innerHTML += `<option value="${brand.id}">${brand.name}</option>`;
                        });
                    }
                }).catch(e => console.log('No brand API found'));
        }

        function fetchCars() {
            const container = document.getElementById('carsContainer');
            container.innerHTML = '<div class="loading"><i class="fa-solid fa-circle-notch fa-spin fa-2x"></i><br><br>Loading data...</div>';
            
            const search = document.getElementById('searchInput').value;
            const category = document.getElementById('categoryFilter').value;
            const brand = document.getElementById('brandFilter').value;
            const seating = document.getElementById('seatingFilter').value;
            const maxPrice = document.getElementById('priceFilter').value;
            const year = document.getElementById('yearFilter').value;
            
            let url = `../api/get_cars.php?page=${currentPage}&limit=9`;
            if (search)   url += `&search=${encodeURIComponent(search)}`;
            if (category) url += `&category=${encodeURIComponent(category)}`;
            if (brand)    url += `&brand_id=${encodeURIComponent(brand)}`;
            if (seating)  url += `&seating=${encodeURIComponent(seating)}`;
            if (maxPrice) url += `&max_price=${encodeURIComponent(maxPrice)}`;
            if (year)     url += `&year=${encodeURIComponent(year)}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    container.innerHTML = '';
                    if (data.status === 'success' && data.data.length > 0) {
                        data.data.forEach(car => {
                            const formatPrice = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(car.price);
                            let imgUrl = car.main_image ? '../assets/image/cars/' + car.main_image : '../assets/image/cars/Porche 911 turbo.jpg';
                            const isCompared = compareList.includes(car.id.toString());
                            
                            const html = `
                                <div class="car-card">
                                    <div class="car-img-wrapper">
                                        ${car.is_featured == 1 ? '<span class="car-badge">Featured</span>' : ''}
                                        <img src="${imgUrl}" alt="${car.model_name}" onerror="this.src='../assets/image/cars/Toyota Camry.jpg'">
                                    </div>
                                    <div class="car-info">
                                        <div class="car-brand">${car.brand_name || 'Brand'}</div>
                                        <h3 class="car-title" title="${car.model_name}">${car.model_name}</h3>
                                        <div class="car-specs">
                                            <div class="spec-item"><i class="fa-solid fa-gauge-high"></i><span>${car.category || 'N/A'}</span></div>
                                            <div class="spec-item"><i class="fa-solid fa-calendar-days"></i><span>${car.year || '2024'}</span></div>
                                            <div class="spec-item"><i class="fa-solid fa-users"></i><span>${car.seating ? car.seating + ' seats' : 'N/A'}</span></div>
                                        </div>
                                        <div class="car-footer">
                                            <div class="car-price">${formatPrice}</div>
                                            <div class="action-buttons">
                                                <button class="btn-action ${isCompared ? 'active' : ''}" onclick="toggleCompare('${car.id}', this)" title="Add to comparison">
                                                    <i class="fa-solid fa-code-compare"></i>
                                                </button>
                                                <a href="car_detail.php?id=${car.id}" class="btn-details">Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            container.insertAdjacentHTML('beforeend', html);
                        });
                        renderPagination(data.pagination);
                    } else {
                        container.innerHTML = '<div class="no-results"><i class="fa-solid fa-car-burst fa-3x"></i><br><br>No suitable cars found.</div>';
                        document.getElementById('paginationContainer').innerHTML = '';
                    }
                })
                .catch(err => {
                    console.error(err);
                    container.innerHTML = '<div class="no-results">An error occurred while loading data.</div>';
                });
        }

        function renderPagination(pagination) {
            const container = document.getElementById('paginationContainer');
            container.innerHTML = '';
            if (pagination.total_pages <= 1) return;

            for (let i = 1; i <= pagination.total_pages; i++) {
                container.innerHTML += `<button class="page-btn ${i === pagination.page ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
            }
        }

        function changePage(page) {
            currentPage = page;
            window.scrollTo({ top: document.querySelector('.showroom-layout').offsetTop - 80, behavior: 'smooth' });
            fetchCars();
        }

        function toggleCompare(id, btn) {
            const index = compareList.indexOf(id.toString());
            if (index === -1) {
                if (compareList.length >= 3) {
                    alert('You can only compare up to 3 cars at a time.');
                    return;
                }
                compareList.push(id.toString());
                btn.classList.add('active');
            } else {
                compareList.splice(index, 1);
                btn.classList.remove('active');
            }
            localStorage.setItem('compareList', JSON.stringify(compareList));
            updateCompareWidget();
        }

        function clearCompare() {
            compareList = [];
            localStorage.removeItem('compareList');
            document.querySelectorAll('.btn-action.active').forEach(b => b.classList.remove('active'));
            updateCompareWidget();
        }

        function updateCompareWidget() {
            const widget = document.getElementById('compareWidget');
            const count = document.getElementById('compareCount');
            count.textContent = compareList.length;
            
            if (compareList.length > 0) {
                widget.classList.add('show');
            } else {
                widget.classList.remove('show');
            }
        }
    </script>
</body>
</html>
