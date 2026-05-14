<?php
require_once __DIR__ . '/config/db.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: admin/dashboard.php');
    exit;
}

$loginError = '';
$registerError = '';
$registerSuccess = false;

// xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $loginError = 'Vui lòng nhập đầy đủ tài khoản và mật khẩu.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password, full_name, role FROM admin_users WHERE username = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['admin_id']   = $user['id'];
                $_SESSION['admin_name'] = $user['full_name'];
                $_SESSION['admin_role'] = $user['role'];

                $update = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = :id");
                $update->execute([':id' => $user['id']]);

                header('Location: admin/dashboard.php');
                exit;
            } else {
                $loginError = 'Sai tài khoản hoặc mật khẩu.';
            }
        } catch (PDOException $e) {
            $loginError = 'Hệ thống đang bận, vui lòng thử lại sau.';
        }
    }
}

// xử lý đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm'] ?? '';

    if ($firstName === '' || $lastName === '' || $email === '' || $username === '' || $password === '') {
        $registerError = 'Vui lòng điền đầy đủ thông tin.';
    } elseif ($password !== $confirm) {
        $registerError = 'Mật khẩu xác nhận không khớp.';
    } elseif (strlen($password) < 6) {
        $registerError = 'Mật khẩu phải có ít nhất 6 ký tự.';
    } else {
        try {
            $check = $pdo->prepare("SELECT id FROM admin_users WHERE username = :username LIMIT 1");
            $check->execute([':username' => $username]);
            if ($check->fetch()) {
                $registerError = 'Tên đăng nhập đã được sử dụng.';
            } else {
                $check2 = $pdo->prepare("SELECT id FROM admin_users WHERE email = :email LIMIT 1");
                $check2->execute([':email' => $email]);
                if ($check2->fetch()) {
                    $registerError = 'Email đã được sử dụng.';
                } else {
                    $hash = password_hash($password, PASSWORD_BCRYPT);
                    $fullName = $lastName . ' ' . $firstName;

                    $ins = $pdo->prepare("INSERT INTO admin_users (username, password, full_name, email, role) VALUES (:username, :password, :full_name, :email, 'customer')");
                    $ins->execute([
                        ':username'  => $username,
                        ':password'  => $hash,
                        ':full_name' => $fullName,
                        ':email'     => $email
                    ]);
                    $registerSuccess = true;
                }
            }
        } catch (PDOException $e) {
            $registerError = 'Hệ thống đang bận, vui lòng thử lại sau.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Đăng nhập vào AutoElite Showroom - Hệ thống quản lý showroom ô tô cao cấp">
    <title>Đăng nhập | AutoElite Showroom</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0a0a0a;
            color: #fff;
            overflow: hidden;
        }

        .login-left {
            flex: 1;
            position: relative;
            overflow: hidden;
        }

        .slider-wrapper {
            position: absolute;
            inset: 0;
            z-index: 0;
        }

        .slider-wrapper img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 1.2s ease, transform 6s ease;
            transform: scale(1.08);
        }

        .slider-wrapper img.active {
            opacity: 1;
            transform: scale(1);
        }

        .slider-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.6) 100%);
            z-index: 1;
        }

        .slider-content {
            position: absolute;
            bottom: 60px;
            left: 50px;
            z-index: 2;
        }

        .slider-content .brand-tag {
            display: inline-block;
            background: rgba(200,155,60,0.2);
            border: 1px solid rgba(200,155,60,0.4);
            color: #c89b3c;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 5px 14px;
            border-radius: 20px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .slider-content h2 {
            font-size: 2.4rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 6px;
        }

        .slider-content p {
            font-size: 1rem;
            color: rgba(255,255,255,0.6);
            font-weight: 300;
        }

        .slider-dots {
            position: absolute;
            bottom: 30px;
            left: 50px;
            display: flex;
            gap: 8px;
            z-index: 2;
        }

        .slider-dots span {
            width: 28px;
            height: 3px;
            background: rgba(255,255,255,0.25);
            border-radius: 2px;
            cursor: pointer;
            transition: all 0.4s ease;
        }

        .slider-dots span.active {
            width: 48px;
            background: #c89b3c;
        }

        /* phần bên phải - form */
        .login-right {
            width: 480px;
            min-height: 100vh;
            background: #111111;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px 45px;
            position: relative;
            border-left: 1px solid rgba(255,255,255,0.06);
        }

        .logo-area {
            margin-bottom: 36px;
        }

        .logo-area .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.5px;
        }

        .logo-area .logo-text span {
            color: #c89b3c;
        }

        .form-header h1 {
            font-size: 1.7rem;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .form-header p {
            color: rgba(255,255,255,0.45);
            font-size: 0.9rem;
            margin-bottom: 28px;
        }

        .tab-switcher {
            display: flex;
            background: rgba(255,255,255,0.04);
            border-radius: 10px;
            padding: 4px;
            margin-bottom: 28px;
        }

        .tab-switcher button {
            flex: 1;
            padding: 10px;
            border: none;
            background: transparent;
            color: rgba(255,255,255,0.4);
            font-family: 'Outfit', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .tab-switcher button.active {
            background: #c89b3c;
            color: #000;
            font-weight: 600;
        }

        .form-panel { display: none; }
        .form-panel.active { display: block; }

        .input-group {
            margin-bottom: 18px;
            position: relative;
        }

        .input-group label {
            display: block;
            font-size: 0.8rem;
            color: rgba(255,255,255,0.5);
            margin-bottom: 6px;
            font-weight: 500;
        }

        .input-group .input-wrap {
            position: relative;
        }

        .input-group .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.2);
            font-size: 0.85rem;
            transition: color 0.3s ease;
        }

        .input-group input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 8px;
            color: #fff;
            font-family: 'Outfit', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .input-group input:focus {
            border-color: #c89b3c;
            background: rgba(200,155,60,0.04);
        }

        .input-group input:focus + i,
        .input-group .input-wrap:focus-within i {
            color: #c89b3c;
        }

        .input-row {
            display: flex;
            gap: 12px;
        }

        .input-row .input-group { flex: 1; }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            font-size: 0.82rem;
        }

        .form-options label {
            display: flex;
            align-items: center;
            gap: 6px;
            color: rgba(255,255,255,0.5);
            cursor: pointer;
        }

        .form-options label input[type="checkbox"] {
            accent-color: #c89b3c;
        }

        .form-options a {
            color: #c89b3c;
            text-decoration: none;
            font-weight: 500;
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #c89b3c, #a67c2e);
            border: none;
            border-radius: 8px;
            color: #000;
            font-family: 'Outfit', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(200,155,60,0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 22px 0;
            color: rgba(255,255,255,0.2);
            font-size: 0.78rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.08);
        }

        .social-login {
            display: flex;
            gap: 10px;
        }

        .social-login button {
            flex: 1;
            padding: 10px;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 8px;
            background: rgba(255,255,255,0.03);
            color: rgba(255,255,255,0.6);
            font-family: 'Outfit', sans-serif;
            font-size: 0.85rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .social-login button:hover {
            background: rgba(255,255,255,0.07);
            border-color: rgba(255,255,255,0.15);
        }

        .alert-msg {
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-error {
            background: rgba(220,53,69,0.12);
            border: 1px solid rgba(220,53,69,0.3);
            color: #ff6b7a;
        }

        .alert-success {
            background: rgba(40,167,69,0.12);
            border: 1px solid rgba(40,167,69,0.3);
            color: #5bff7f;
        }

        .toggle-pass {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255,255,255,0.2);
            cursor: pointer;
            font-size: 0.85rem;
            transition: color 0.3s;
        }

        .toggle-pass:hover { color: #c89b3c; }

        @media (max-width: 900px) {
            .login-left { display: none; }
            .login-right {
                width: 100%;
                border-left: none;
            }
        }

        @media (max-width: 480px) {
            .login-right { padding: 30px 24px; }
        }
    </style>
</head>
<body>

<div class="login-left">
    <div class="slider-wrapper">
        <img src="assets/image/cars/porsche 911.jpg" alt="Porsche 911" class="active">
        <img src="assets/image/cars/bmw 430i Gran Coupe 2022.jpg" alt="BMW 430i Gran Coupe">
        <img src="assets/image/cars/Toyota Camry 2025.jpg" alt="Toyota Camry 2025">
    </div>
    <div class="slider-overlay"></div>
    <div class="slider-content">
        <span class="brand-tag" id="slide-tag">PORSCHE</span>
        <h2 id="slide-title">Porsche 911 Turbo S</h2>
        <p id="slide-desc">Biểu tượng tốc độ và đẳng cấp</p>
    </div>
    <div class="slider-dots">
        <span class="active" data-index="0"></span>
        <span data-index="1"></span>
        <span data-index="2"></span>
    </div>
</div>

<div class="login-right">
    <div class="logo-area">
        <div class="logo-text"><span>Auto</span>SupperCar</div>
    </div>

    <div class="form-header">
        <h1 id="form-title">Chào mừng trở lại</h1>
        <p id="form-desc">Đăng nhập để quản lý showroom của bạn</p>
    </div>

    <div class="tab-switcher">
        <button id="tab-login" class="active" onclick="switchTab('login')">Đăng nhập</button>
        <button id="tab-register" onclick="switchTab('register')">Đăng ký</button>
    </div>

    <!-- FORM ĐĂNG NHẬP -->
    <div id="panel-login" class="form-panel active">
        <?php if ($loginError): ?>
            <div class="alert-msg alert-error" id="login-err">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($loginError) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" autocomplete="off">
            <input type="hidden" name="action" value="login">
            <div class="input-group">
                <label for="login-user">Tên đăng nhập</label>
                <div class="input-wrap">
                    <input type="text" id="login-user" name="username" placeholder="Nhập tên đăng nhập" required>
                    <i class="fas fa-user"></i>
                </div>
            </div>
            <div class="input-group">
                <label for="login-pass">Mật khẩu</label>
                <div class="input-wrap">
                    <input type="password" id="login-pass" name="password" placeholder="Nhập mật khẩu" required>
                    <i class="fas fa-lock"></i>
                    <button type="button" class="toggle-pass" onclick="togglePassword('login-pass', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="form-options">
                <label><input type="checkbox" name="remember"> Ghi nhớ đăng nhập</label>
                <a href="#">Quên mật khẩu?</a>
            </div>
            <button type="submit" class="btn-submit">Đăng nhập</button>
        </form>

        <div class="divider">hoặc tiếp tục với</div>
        <div class="social-login">
            <button type="button"><i class="fab fa-google"></i> Google</button>
            <button type="button"><i class="fab fa-facebook-f"></i> Facebook</button>
        </div>
    </div>

    <!-- FORM ĐĂNG KÝ -->
    <div id="panel-register" class="form-panel">
        <?php if ($registerSuccess): ?>
            <div class="alert-msg alert-success" id="reg-ok">
                <i class="fas fa-check-circle"></i> Đăng ký thành công! Bạn có thể đăng nhập ngay.
            </div>
        <?php endif; ?>

        <?php if ($registerError): ?>
            <div class="alert-msg alert-error" id="reg-err">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($registerError) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" autocomplete="off">
            <input type="hidden" name="action" value="register">
            <div class="input-row">
                <div class="input-group">
                    <label for="reg-fname">Họ</label>
                    <div class="input-wrap">
                        <input type="text" id="reg-fname" name="last_name" placeholder="Nguyễn" required>
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="input-group">
                    <label for="reg-lname">Tên</label>
                    <div class="input-wrap">
                        <input type="text" id="reg-lname" name="first_name" placeholder="Văn A" required>
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
            <div class="input-group">
                <label for="reg-email">Email</label>
                <div class="input-wrap">
                    <input type="email" id="reg-email" name="email" placeholder="email@example.com" required>
                    <i class="fas fa-envelope"></i>
                </div>
            </div>
            <div class="input-group">
                <label for="reg-user">Tên đăng nhập</label>
                <div class="input-wrap">
                    <input type="text" id="reg-user" name="username" placeholder="Chọn tên đăng nhập" required>
                    <i class="fas fa-at"></i>
                </div>
            </div>
            <div class="input-row">
                <div class="input-group">
                    <label for="reg-pass">Mật khẩu</label>
                    <div class="input-wrap">
                        <input type="password" id="reg-pass" name="password" placeholder="Tối thiểu 6 ký tự" required>
                        <i class="fas fa-lock"></i>
                        <button type="button" class="toggle-pass" onclick="togglePassword('reg-pass', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="input-group">
                    <label for="reg-confirm">Xác nhận</label>
                    <div class="input-wrap">
                        <input type="password" id="reg-confirm" name="confirm" placeholder="Nhập lại mật khẩu" required>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-submit" style="margin-top: 6px;">Tạo tài khoản</button>
        </form>
    </div>
</div>

<script>
const slides = [
    { tag: 'PORSCHE', title: 'Porsche 911 Turbo S', desc: 'Biểu tượng tốc độ và đẳng cấp' },
    { tag: 'BMW', title: 'BMW 430i Gran Coupe', desc: 'Sang trọng từ mọi góc nhìn' },
    { tag: 'TOYOTA', title: 'Toyota Camry 2025', desc: 'Sự lựa chọn hoàn hảo cho mọi hành trình' }
];

let current = 0;
const images = document.querySelectorAll('.slider-wrapper img');
const dots = document.querySelectorAll('.slider-dots span');

function goToSlide(index) {
    images[current].classList.remove('active');
    dots[current].classList.remove('active');
    current = index;
    images[current].classList.add('active');
    dots[current].classList.add('active');
    document.getElementById('slide-tag').textContent = slides[current].tag;
    document.getElementById('slide-title').textContent = slides[current].title;
    document.getElementById('slide-desc').textContent = slides[current].desc;
}

dots.forEach(dot => {
    dot.addEventListener('click', () => goToSlide(parseInt(dot.dataset.index)));
});

setInterval(() => goToSlide((current + 1) % slides.length), 5000);

// chuyển tab login / register
function switchTab(tab) {
    document.getElementById('tab-login').classList.toggle('active', tab === 'login');
    document.getElementById('tab-register').classList.toggle('active', tab === 'register');
    document.getElementById('panel-login').classList.toggle('active', tab === 'login');
    document.getElementById('panel-register').classList.toggle('active', tab === 'register');

    if (tab === 'login') {
        document.getElementById('form-title').textContent = 'Chào mừng trở lại';
        document.getElementById('form-desc').textContent = 'Đăng nhập để quản lý showroom của bạn';
    } else {
        document.getElementById('form-title').textContent = 'Tạo tài khoản mới';
        document.getElementById('form-desc').textContent = 'Điền thông tin để bắt đầu sử dụng hệ thống';
    }
}

// toggle hiển thị mật khẩu
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// nếu có lỗi đăng ký hoặc đăng ký thành công thì auto chuyển sang tab register
<?php if ($registerError || $registerSuccess): ?>
    switchTab('register');
<?php endif; ?>
</script>

</body>
</html>
