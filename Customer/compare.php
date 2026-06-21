<?php
require_once '../config/db.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="Viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto DreamCars | Compare Cars</title>
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
        .nav-links li a:hover::after, .nav-links li a.active::after { width: 100%; }
        .nav-links li a:hover, .nav-links li a.active { color: var(--gold); }
        .btn-nav { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: #fff; padding: 10px 24px; border-radius: 30px; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; border: none; cursor: pointer; box-shadow: 0 4px 15px var(--gold-glow); transition: all var(--transition); margin-left: 15px; }
        .btn-nav:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(212, 168, 67, 0.4); color: #fff; }

        /* HEADER SECTION */
        .page-header {
            padding: 120px 5% 40px; background: var(--bg-primary); text-align: center;
            border-bottom: 1px solid var(--border);
        }
        .page-header h1 { font-size: 2.5rem; margin-bottom: 10px; }
        .page-header p { color: var(--text-muted); font-size: 1.1rem; }

        /* COMPARE SECTION */
        .compare-container { padding: 50px 5%; min-height: 500px; }

        .empty-state { text-align: center; padding: 100px 20px; background: var(--bg-card); border-radius: var(--radius-lg); border: 1px dashed var(--border); }
        .empty-state i { font-size: 4rem; color: var(--border); margin-bottom: 20px; }
        .empty-state p { color: var(--text-muted); font-size: 1.1rem; margin-bottom: 20px; }
        .btn-add-cars { background: var(--text-dark); color: #fff; padding: 12px 30px; border-radius: 30px; display: inline-block; font-weight: 600; }
        .btn-add-cars:hover { background: var(--gold); }

        .compare-table-wrapper {
            background: var(--bg-card); border-radius: var(--radius-lg);
            box-shadow: 0 10px 40px rgba(0,0,0,0.03); border: 1px solid var(--border);
            overflow-x: auto; display: none;
        }

        .compare-table { width: 100%; border-collapse: collapse; text-align: center; }
        .compare-table th, .compare-table td { padding: 20px; border-bottom: 1px solid var(--border); border-right: 1px solid var(--border); width: 25%; }
        .compare-table th:last-child, .compare-table td:last-child { border-right: none; }
        .compare-table tbody tr:last-child td { border-bottom: none; }
        
        .compare-table td.feature-label {
            text-align: left; font-weight: 700; color: var(--text-muted);
            background: var(--bg-secondary); width: 20%;
        }

        /* Car Header in Table */
        .car-header { display: flex; flex-direction: column; align-items: center; gap: 15px; position: relative; }
        .remove-btn {
            position: absolute; top: -10px; right: -10px; width: 30px; height: 30px;
            background: #ef4444; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            border: none; cursor: pointer; transition: all var(--transition);
        }
        .remove-btn:hover { transform: scale(1.1); }
        .car-img-box { width: 100%; height: 150px; border-radius: 10px; overflow: hidden; background: var(--bg-secondary); }
        .car-img-box img { width: 100%; height: 100%; object-fit: cover; }
        .car-name { font-size: 1.2rem; font-family: 'Outfit', sans-serif; font-weight: 700; color: var(--text-dark); }
        .car-price { font-size: 1.4rem; font-weight: 800; color: var(--gold); font-family: 'Outfit', sans-serif; }
        .car-brand { font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }

        .btn-buy { background: var(--text-dark); color: #fff; padding: 10px 20px; border-radius: 20px; font-weight: 600; font-size: 13px; margin-top: 10px; border: none; cursor: pointer;}
        .btn-buy:hover { background: var(--gold); }

        /* Loading */
        #loadingLayer {
            text-align: center; padding: 100px;
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

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">AUTO<span>DREAMCARS</span></a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="cars.php">Explore Cars</a></li>
            <li><a href="compare.php" class="active">Compare</a></li>
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
        <h1>Compare Car Models</h1>
        <p>Choose the car that best fits your needs and style</p>
    </div>

    <!-- COMPARE CONTENT -->
    <div class="compare-container">
        
        <div id="loadingLayer">
            <i class="fa-solid fa-circle-notch fa-spin fa-3x" style="color: var(--gold); margin-bottom: 20px;"></i>
            <h3 style="color: var(--text-muted);">Preparing comparison data...</h3>
        </div>

        <div class="empty-state" id="emptyState" style="display: none;">
            <i class="fa-solid fa-code-compare"></i>
            <h2>No Cars in Comparison List</h2>
            <p>Browse our collection and select up to 3 cars for detailed comparison.</p>
            <a href="cars.php" class="btn-add-cars">Explore Car Models</a>
        </div>

        <div class="compare-table-wrapper" id="compareTableWrapper">
            <div style="padding: 15px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: var(--bg-primary);">
                <span style="font-weight: 600; font-size: 14px; color: var(--text-muted);"><i class="fa-solid fa-circle-info" style="color: var(--gold);"></i> Detailed specifications comparison table</span>
                <a href="cars.php" style="color: var(--gold); font-size: 13px; font-weight: 600; text-decoration: underline;"><i class="fa-solid fa-plus"></i> Add another car</a>
            </div>
            <table class="compare-table">
                <thead>
                    <tr id="compareHeaders">
                        <td class="feature-label">Basic Information</td>
                        <!-- Populated dynamically -->
                    </tr>
                </thead>
                <tbody id="compareBody">
                    <!-- Populated dynamically -->
                </tbody>
            </table>
        </div>

    </div>

    <!-- FOOTER -->
    <footer>
        <div class="footer-bottom" style="border:none; padding: 0;">
            <p>&copy; <?php echo date('Y'); ?> Auto DreamCars Showroom. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        let compareList = JSON.parse(localStorage.getItem('compareList')) || [];

        document.addEventListener('DOMContentLoaded', () => {
            fetchWeather();
            if (compareList.length === 0) {
                showEmptyState();
            } else {
                fetchCompareData();
            }
        });

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

        function showEmptyState() {
            document.getElementById('loadingLayer').style.display = 'none';
            document.getElementById('compareTableWrapper').style.display = 'none';
            document.getElementById('emptyState').style.display = 'block';
        }

        function fetchCompareData() {
            const promises = compareList.map(id => 
                fetch(`../api/get_car.php?id=${id}`).then(res => res.json())
            );

            Promise.all(promises)
                .then(results => {
                    document.getElementById('loadingLayer').style.display = 'none';
                    const validCars = results.filter(r => r.status === 'success').map(r => r.data);
                    
                    if (validCars.length === 0) {
                        compareList = [];
                        localStorage.removeItem('compareList');
                        showEmptyState();
                        return;
                    }

                    renderCompareTable(validCars);
                })
                .catch(err => {
                    console.error("Error loading comparison data:", err);
                    alert("An error occurred while loading car data.");
                });
        }

        function renderCompareTable(carsData) {
            document.getElementById('compareTableWrapper').style.display = 'block';
            document.getElementById('emptyState').style.display = 'none';

            const headersRow = document.getElementById('compareHeaders');
            const tbody = document.getElementById('compareBody');
            
            // Generate Headers
            let headersHTML = '<td class="feature-label">Car Model</td>';
            carsData.forEach((data, index) => {
                const car = data.car;
                let mainImg = data.images.find(img => img.is_main == 1) || data.images[0];
                let imgUrl = mainImg ? '../assets/image/cars/' + mainImg.image : '../assets/image/cars/Porche 911 turbo.jpg';
                const formatPrice = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(car.price);
                
                headersHTML += `
                    <td>
                        <div class="car-header">
                            <button class="remove-btn" onclick="removeFromCompare('${car.id}')" title="Remove from comparison"><i class="fa-solid fa-xmark"></i></button>
                            <div class="car-img-box"><img src="${imgUrl}" onerror="this.src='../assets/image/cars/Toyota Camry.jpg'"></div>
                            <div>
                                <div class="car-brand">${car.brand_name || 'Brand'}</div>
                                <div class="car-name">${car.model_name}</div>
                            </div>
                            <div class="car-price">${formatPrice}</div>
                            <button class="btn-buy" onclick="window.location.href='booking.php?car_id=${car.id}'">Book Test Drive</button>
                        </div>
                    </td>
                `;
            });

            // Fill empty slots if less than 3 cars
            const emptySlots = 3 - carsData.length;
            for (let i = 0; i < emptySlots; i++) {
                headersHTML += `
                    <td>
                        <div style="height: 100%; display:flex; flex-direction:column; align-items:center; justify-content:center; color: var(--border);">
                            <i class="fa-solid fa-car" style="font-size: 3rem; margin-bottom: 15px;"></i>
                            <a href="cars.php" class="btn-buy" style="background:var(--bg-secondary); color:var(--text-muted); border: 1px dashed var(--text-muted);">Add car</a>
                        </div>
                    </td>
                `;
            }
            headersRow.innerHTML = headersHTML;

            // Prepare spec rows
            const specLabels = [
                { key: 'category', label: 'Category', isFromCarTable: true },
                { key: 'year', label: 'Year', isFromCarTable: true },
                { key: 'engine', label: 'Engine', isFromCarTable: false },
                { key: 'horsepower', label: 'Horsepower (HP)', isFromCarTable: false },
                { key: 'torque', label: 'Torque', isFromCarTable: false },
                { key: 'transmission', label: 'Transmission', isFromCarTable: false },
                { key: 'fuel_type', label: 'Fuel Type', isFromCarTable: false },
                { key: 'fuel_efficiency', label: 'Fuel Efficiency', isFromCarTable: false },
                { key: 'drive_type', label: 'Drive Type', isFromCarTable: false },
                { key: 'seating', label: 'Seating Capacity', isFromCarTable: false },
                { key: 'top_speed', label: 'Top Speed (km/h)', isFromCarTable: false },
                { key: 'acceleration', label: 'Acceleration 0-100 km/h (s)', isFromCarTable: false }
            ];

            let bodyHTML = '';
            specLabels.forEach(spec => {
                bodyHTML += `<tr><td class="feature-label">${spec.label}</td>`;
                
                carsData.forEach(data => {
                    const source = spec.isFromCarTable ? data.car : (data.specifications || {});
                    let val = source[spec.key];
                    if (val === null || val === undefined || val === '') val = '--';
                    bodyHTML += `<td><span style="font-weight: 500; color: #444;">${val}</span></td>`;
                });

                for (let i = 0; i < emptySlots; i++) {
                    bodyHTML += `<td style="background: rgba(248, 250, 252, 0.5);">--</td>`;
                }
                
                bodyHTML += `</tr>`;
            });

            tbody.innerHTML = bodyHTML;
        }

        function removeFromCompare(idStr) {
            const index = compareList.indexOf(idStr);
            if (index > -1) {
                compareList.splice(index, 1);
                localStorage.setItem('compareList', JSON.stringify(compareList));
                
                if (compareList.length === 0) {
                    showEmptyState();
                } else {
                    document.getElementById('loadingLayer').style.display = 'block';
                    document.getElementById('compareTableWrapper').style.display = 'none';
                    fetchCompareData();
                }
            }
        }
    </script>
</body>
</html>
