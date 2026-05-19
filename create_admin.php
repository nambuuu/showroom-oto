<?php
require_once __DIR__ . '/config/db.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');

    if ($username === '' || $password === '' || $full_name === '' || $email === '') {
        $message = "Vui lòng nhập đầy đủ thông tin!";
        $messageType = "danger";
    } else {
        try {
            // Kiểm tra xem username đã tồn tại chưa
            $check = $pdo->prepare("SELECT id FROM admin_users WHERE username = :username OR email = :email");
            $check->execute([':username' => $username, ':email' => $email]);
            
            if ($check->rowCount() > 0) {
                $message = "Tên đăng nhập hoặc Email đã tồn tại!";
                $messageType = "danger";
            } else {
                // Mã hóa mật khẩu
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                
                // Thêm tài khoản với quyền superadmin
                $stmt = $pdo->prepare("INSERT INTO admin_users (username, password, full_name, email, role) VALUES (:username, :password, :full_name, :email, 'superadmin')");
                $stmt->execute([
                    ':username'  => $username,
                    ':password'  => $hashedPassword,
                    ':full_name' => $full_name,
                    ':email'     => $email
                ]);
                
                $message = "Tạo tài khoản Admin thành công! Bạn có thể <a href='login.php' style='color:#0d6efd;text-decoration:underline;'>đăng nhập ngay</a>.";
                $messageType = "success";
            }
        } catch (PDOException $e) {
            $message = "Lỗi Database: " . $e->getMessage();
            $messageType = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Tài Khoản Super Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            margin-top: 0;
            color: #111827;
            font-size: 24px;
            text-align: center;
            margin-bottom: 24px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #374151;
            font-size: 14px;
        }
        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.15s ease-in-out;
        }
        input[type="text"]:focus, input[type="password"]:focus, input[type="email"]:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #111827;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 10px;
        }
        button:hover {
            background-color: #374151;
        }
        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .warning-text {
            font-size: 12px;
            color: #ef4444;
            text-align: center;
            margin-top: 16px;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Tạo Super Admin</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Tên đăng nhập (Username)</label>
            <input type="text" id="username" name="username" required placeholder="admin">
        </div>
        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input type="password" id="password" name="password" required placeholder="••••••••">
        </div>
        <div class="form-group">
            <label for="full_name">Họ và Tên</label>
            <input type="text" id="full_name" name="full_name" required placeholder="Quản trị viên">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="admin@example.com">
        </div>
        <button type="submit">Tạo Tài Khoản Admin</button>
    </form>

    <div class="warning-text">
        ⚠️ Vui lòng xóa file này (create_admin.php) sau khi tạo xong tài khoản để đảm bảo bảo mật!
    </div>
</div>

</body>
</html>
