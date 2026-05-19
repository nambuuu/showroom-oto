<?php
require_once __DIR__ . '/config/db.php';

if (isset($_SESSION['admin_id'])) {
    if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'superadmin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$loginError = '';
$registerError = '';
$registerSuccess = false;

// handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $loginError = 'Please enter your username and password.';
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

                if ($user['role'] === 'superadmin') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $loginError = 'Incorrect username or password.';
            }
        } catch (PDOException $e) {
            $loginError = 'System is busy, please try again later.';
        }
    }
}

// handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm'] ?? '';

    if ($firstName === '' || $lastName === '' || $email === '' || $username === '' || $password === '') {
        $registerError = 'Please fill in all required fields.';
    } elseif ($password !== $confirm) {
        $registerError = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $registerError = 'Password must be at least 6 characters.';
    } else {
        try {
            $check = $pdo->prepare("SELECT id FROM admin_users WHERE username = :username LIMIT 1");
            $check->execute([':username' => $username]);
            if ($check->fetch()) {
                $registerError = 'Username is already taken.';
            } else {
                $check2 = $pdo->prepare("SELECT id FROM admin_users WHERE email = :email LIMIT 1");
                $check2->execute([':email' => $email]);
                if ($check2->fetch()) {
                    $registerError = 'Email is already in use.';
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
            $registerError = 'System is busy, please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to AutoElite Showroom - Premium Car Showroom Management System">
    <title>Login | AutoElite Showroom</title>
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

        /* right side - form */
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
        <img src="assets/image/cars/BMW.jpg" alt="BMW" class="active">
        <img src="assets/image/cars/Toyota Camry.jpg" alt="Toyota Camry">
        <img src="assets/image/cars/Porche 911 turbo.jpg" alt="Porsche 911 Turbo">
    </div>
    <div class="slider-overlay"></div>
    <div class="slider-content">
        <span class="brand-tag" id="slide-tag">BMW</span>
        <h2 id="slide-title">BMW</h2>
        <p id="slide-desc">The ultimate driving machine</p>
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
        <h1 id="form-title">Welcome Back</h1>
        <p id="form-desc">Sign in to manage your showroom</p>
    </div>

    <div class="tab-switcher">
        <button id="tab-login" class="active" onclick="switchTab('login')">Login</button>
        <button id="tab-register" onclick="switchTab('register')">Register</button>
    </div>

    <!-- LOGIN FORM -->
    <div id="panel-login" class="form-panel active">
        <?php if ($loginError): ?>
            <div class="alert-msg alert-error" id="login-err">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($loginError) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" autocomplete="off">
            <input type="hidden" name="action" value="login">
            <div class="input-group">
                <label for="login-user">Username</label>
                <div class="input-wrap">
                    <input type="text" id="login-user" name="username" placeholder="Enter your username" required>
                    <i class="fas fa-user"></i>
                </div>
            </div>
            <div class="input-group">
                <label for="login-pass">Password</label>
                <div class="input-wrap">
                    <input type="password" id="login-pass" name="password" placeholder="Enter your password" required>
                    <i class="fas fa-lock"></i>
                    <button type="button" class="toggle-pass" onclick="togglePassword('login-pass', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="form-options">
                <label><input type="checkbox" name="remember"> Remember me</label>
                <a href="#">Forgot password?</a>
            </div>
            <button type="submit" class="btn-submit">Sign In</button>
        </form>

        <div class="divider">or continue with</div>
        <div class="social-login">
            <button type="button"><i class="fab fa-google"></i> Google</button>
            <button type="button"><i class="fab fa-facebook-f"></i> Facebook</button>
        </div>
    </div>

    <!-- REGISTER FORM -->
    <div id="panel-register" class="form-panel">
        <?php if ($registerSuccess): ?>
            <div class="alert-msg alert-success" id="reg-ok">
                <i class="fas fa-check-circle"></i> Registration successful! You can now sign in.
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
                    <label for="reg-fname">Last Name</label>
                    <div class="input-wrap">
                        <input type="text" id="reg-fname" name="last_name" placeholder="Smith" required>
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="input-group">
                    <label for="reg-lname">First Name</label>
                    <div class="input-wrap">
                        <input type="text" id="reg-lname" name="first_name" placeholder="John" required>
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
                <label for="reg-user">Username</label>
                <div class="input-wrap">
                    <input type="text" id="reg-user" name="username" placeholder="Choose a username" required>
                    <i class="fas fa-at"></i>
                </div>
            </div>
            <div class="input-row">
                <div class="input-group">
                    <label for="reg-pass">Password</label>
                    <div class="input-wrap">
                        <input type="password" id="reg-pass" name="password" placeholder="Minimum 6 characters" required>
                        <i class="fas fa-lock"></i>
                        <button type="button" class="toggle-pass" onclick="togglePassword('reg-pass', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="input-group">
                    <label for="reg-confirm">Confirm</label>
                    <div class="input-wrap">
                        <input type="password" id="reg-confirm" name="confirm" placeholder="Re-enter your password" required>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-submit" style="margin-top: 6px;">Create Account</button>
        </form>
    </div>
</div>

<script>
const slides = [
    { tag: 'BMW', title: 'BMW', desc: 'The ultimate driving machine' },
    { tag: 'TOYOTA', title: 'Toyota Camry', desc: 'Refined comfort meets outstanding performance' },
    { tag: 'PORSCHE', title: 'Porsche 911 Turbo', desc: 'The icon of speed and prestige' }
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

// switch between login / register tabs
function switchTab(tab) {
    document.getElementById('tab-login').classList.toggle('active', tab === 'login');
    document.getElementById('tab-register').classList.toggle('active', tab === 'register');
    document.getElementById('panel-login').classList.toggle('active', tab === 'login');
    document.getElementById('panel-register').classList.toggle('active', tab === 'register');

    if (tab === 'login') {
        document.getElementById('form-title').textContent = 'Welcome Back';
        document.getElementById('form-desc').textContent = 'Sign in to manage your showroom';
    } else {
        document.getElementById('form-title').textContent = 'Create a New Account';
        document.getElementById('form-desc').textContent = 'Fill in your details to get started';
    }
}

// toggle password visibility
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

// if registration error or success, auto-switch to register tab
<?php if ($registerError || $registerSuccess): ?>
    switchTab('register');
<?php endif; ?>
</script>

</body>
</html>
