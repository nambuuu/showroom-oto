<?php
require_once '../config/auth_guard.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    // Validate required fields
    $name       = trim($_POST['model_name'] ?? '');
    $brand_id   = $_POST['brand_id'] ?? '';
    $year       = (int)($_POST['year'] ?? 0);
    $price      = (float)($_POST['price'] ?? 0);
    $status     = $_POST['status'] ?? 'available';
    $errors     = [];
    if ($name === '' || $brand_id === '' || $year <= 0 || $price <= 0) {
        $errors[] = 'Vui lòng nhập đầy đủ thông tin hợp lệ.';
    }
    // Handle image upload
    $thumbnailPath = '';
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg','image/png','image/webp'];
        if (!in_array($_FILES['thumbnail']['type'], $allowed)) {
            $errors[] = 'Định dạng ảnh không hợp lệ (chỉ chấp nhận jpg/png/webp).';
        }
        if ($_FILES['thumbnail']['size'] > MAX_FILE_SIZE) {
            $errors[] = 'Kích thước ảnh không được vượt quá 5MB.';
        }
        if (empty($errors)) {
            $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
            $newName = uniqid('car_') . '.' . $ext;
            $dest = UPLOAD_PATH . $newName;
            if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $dest)) {
                $errors[] = 'Lỗi khi lưu ảnh.';
            } else {
                $thumbnailPath = 'assets/image/cars/' . $newName; // relative for frontend
            }
        }
    }
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO cars (model_name, brand_id, year, price, status) VALUES (:model_name, :brand_id, :year, :price, :status)');
        $stmt->execute([
            ':model_name' => $name,
            ':brand_id' => $brand_id,
            ':year'     => $year,
            ':price'    => $price,
            ':status'   => $status
        ]);
        $carId = $pdo->lastInsertId();

        // Lưu ảnh vào bảng car_images
        if ($thumbnailPath !== '') {
            $imgName = basename($thumbnailPath); // chỉ lấy tên file
            $imgStmt = $pdo->prepare('INSERT INTO car_images (car_id, image, is_main) VALUES (:car_id, :image, 1)');
            $imgStmt->execute([
                ':car_id' => $carId,
                ':image'  => $imgName
            ]);
        }

        header('Location: cars.php?msg=added');
        exit;
    }
}

// Get brand list for dropdown
$brandStmt = $pdo->query('SELECT id, name FROM brands ORDER BY name');
$brands = $brandStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Xe – Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="container py-4">
        <h2 class="text-gold mb-4">Thêm Xe Mới</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><?php echo implode('<br>', $errors); ?></div>
        <?php endif; ?>
        <form method="POST" action="" enctype="multipart/form-data" class="row g-3">
            <input type="hidden" name="action" value="add">
            <div class="col-md-6">
                <label class="form-label">Tên xe</label>
                <input type="text" name="model_name" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Hãng</label>
                <select name="brand_id" class="form-select" required>
                    <option value="">-- Chọn hãng --</option>
                    <?php foreach ($brands as $b): ?>
                        <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Năm</label>
                <input type="number" name="year" class="form-control" min="1900" max="2099" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Giá (VND)</label>
                <input type="number" name="price" class="form-control" min="0" step="1000" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select" required>
                    <option value="available">Đang bán</option>
                    <option value="sold_out">Hết hàng</option>
                    <option value="coming_soon">Sắp ra mắt</option>
                </select>
            </div>
            <div class="col-md-9">
                <label class="form-label">Ảnh thumbnail</label>
                <input type="file" name="thumbnail" class="form-control" accept="image/jpeg,image/png,image/webp" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Lưu Xe</button>
                <a href="cars.php" class="btn btn-outline-gold ms-2">Hủy</a>
            </div>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
