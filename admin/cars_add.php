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

    // Specifications
    $engine = trim($_POST['engine'] ?? '');
    $horsepower = (isset($_POST['horsepower']) && $_POST['horsepower'] !== '') ? (int)$_POST['horsepower'] : null;
    $torque = trim($_POST['torque'] ?? '');
    $transmission = trim($_POST['transmission'] ?? '');
    $fuel_type = trim($_POST['fuel_type'] ?? '');
    $fuel_efficiency = trim($_POST['fuel_efficiency'] ?? '');
    $seating = (isset($_POST['seating']) && $_POST['seating'] !== '') ? (int)$_POST['seating'] : null;
    $drive_type = trim($_POST['drive_type'] ?? '');
    $top_speed = (isset($_POST['top_speed']) && $_POST['top_speed'] !== '') ? (int)$_POST['top_speed'] : null;
    $acceleration = (isset($_POST['acceleration']) && $_POST['acceleration'] !== '') ? (float)$_POST['acceleration'] : null;

    // Handle image upload
    $thumbnailPath = '';
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg','image/png','image/webp'];
        if (!in_array($_FILES['thumbnail']['type'], $allowed)) {
            $errors[] = 'Định dạng ảnh không hợp lệ (chỉ chấp nhận jpg/png/webp).';
        }
        
        $max_size = 5 * 1024 * 1024; // 5MB
        if ($_FILES['thumbnail']['size'] > $max_size) {
            $errors[] = 'Kích thước ảnh không được vượt quá 5MB.';
        }

        if (empty($errors)) {
            $upload_dir = '../assets/image/cars/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
            $newName = uniqid('car_') . '.' . $ext;
            $dest = $upload_dir . $newName;
            
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

        // Lưu thông số kỹ thuật
        $specStmt = $pdo->prepare('INSERT INTO car_specifications (car_id, engine, horsepower, torque, transmission, fuel_type, fuel_efficiency, seating, drive_type, top_speed, acceleration) VALUES (:car_id, :engine, :horsepower, :torque, :transmission, :fuel_type, :fuel_efficiency, :seating, :drive_type, :top_speed, :acceleration)');
        $specStmt->execute([
            ':car_id' => $carId,
            ':engine' => $engine ?: null,
            ':horsepower' => $horsepower,
            ':torque' => $torque ?: null,
            ':transmission' => $transmission ?: null,
            ':fuel_type' => $fuel_type ?: null,
            ':fuel_efficiency' => $fuel_efficiency ?: null,
            ':seating' => $seating,
            ':drive_type' => $drive_type ?: null,
            ':top_speed' => $top_speed,
            ':acceleration' => $acceleration
        ]);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Xe – Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .form-glass {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: var(--shadow);
            max-width: 800px;
            margin: 0 auto;
        }
        .form-glass-header {
            margin-bottom: 24px;
            text-align: center;
        }
        .form-glass-header h3 {
            font-size: 24px;
            color: var(--gold);
            margin-bottom: 8px;
        }
        .form-label {
            color: var(--text-dim);
            font-size: 14px;
            font-weight: 500;
        }
        .form-control, .form-select {
            background: var(--bg-primary) !important;
            border: 1px solid var(--border) !important;
            color: var(--text) !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--gold) !important;
            box-shadow: 0 0 0 3px var(--gold-glow) !important;
        }
        .upload-preview {
            width: 100%;
            height: 200px;
            border: 2px dashed var(--border);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            overflow: hidden;
            background: var(--bg-secondary);
        }
        .upload-preview img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .upload-preview i {
            font-size: 48px;
            color: var(--text-muted);
        }
        .section-title {
            font-size: 14px;
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 8px;
            font-weight: 600;
        }
    </style>
</head>
<body>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content" id="mainContent">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-body">
        
        <div class="d-flex align-items-center mb-4">
            <a href="cars.php" class="btn btn-outline-gold me-3"><i class="bi bi-arrow-left"></i> Quay lại</a>
            <h4 class="mb-0 text-gold" style="font-family: 'Orbitron', sans-serif;">Thêm Xe Mới</h4>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" style="background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.3); color: #ef4444; max-width: 800px; margin: 0 auto 20px;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo implode('<br>', $errors); ?>
            </div>
        <?php endif; ?>

        <div class="form-glass">
            <div class="form-glass-header">
                <h3><i class="bi bi-car-front-fill me-2"></i>Thông Tin Xe</h3>
                <p class="text-muted" style="font-size: 14px;">Vui lòng điền đầy đủ và chính xác thông tin để thêm xe mới.</p>
            </div>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="section-title">Thông tin cơ bản</div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tên xe <span class="text-danger">*</span></label>
                            <input type="text" name="model_name" class="form-control" required placeholder="VD: S 450 Luxury" value="<?= htmlspecialchars($_POST['model_name'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Hãng sản xuất <span class="text-danger">*</span></label>
                            <select name="brand_id" class="form-select" required>
                                <option value="">-- Chọn hãng --</option>
                                <?php foreach ($brands as $b): ?>
                                    <option value="<?php echo $b['id']; ?>" <?= (isset($_POST['brand_id']) && $_POST['brand_id'] == $b['id']) ? 'selected' : '' ?>>
                                        <?php echo htmlspecialchars($b['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label">Năm SX <span class="text-danger">*</span></label>
                                <input type="number" name="year" class="form-control" min="1900" max="2099" required placeholder="2024" value="<?= htmlspecialchars($_POST['year'] ?? '') ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="available" <?= (isset($_POST['status']) && $_POST['status'] == 'available') ? 'selected' : '' ?>>Đang bán</option>
                                    <option value="sold_out" <?= (isset($_POST['status']) && $_POST['status'] == 'sold_out') ? 'selected' : '' ?>>Hết hàng</option>
                                    <option value="coming_soon" <?= (isset($_POST['status']) && $_POST['status'] == 'coming_soon') ? 'selected' : '' ?>>Sắp ra mắt</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giá niêm yết (VND) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="price" class="form-control" min="0" step="1000" required placeholder="VD: 5500000000" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
                                <span class="input-group-text" style="background: var(--bg-secondary); border-color: var(--border); color: var(--gold);">₫</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="section-title">Hình ảnh đại diện</div>
                        <div class="upload-preview" id="thumbnailPreview">
                            <i class="bi bi-camera"></i>
                        </div>
                        <div>
                            <input type="file" name="thumbnail" id="thumbnailInput" class="form-control mb-2" accept="image/jpeg,image/png,image/webp" required>
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Định dạng: JPG, PNG, WEBP. Dung lượng tối đa: 5MB.</small>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-2">
                    <div class="col-12">
                        <div class="section-title">Thông số kỹ thuật </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Động cơ</label>
                        <input type="text" name="engine" class="form-control" placeholder="VD: 3.0L V6" value="<?= htmlspecialchars($_POST['engine'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Mã lực (HP)</label>
                        <input type="number" name="horsepower" class="form-control" placeholder="VD: 367" value="<?= htmlspecialchars($_POST['horsepower'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Mô-men xoắn</label>
                        <input type="text" name="torque" class="form-control" placeholder="VD: 500 Nm" value="<?= htmlspecialchars($_POST['torque'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Hộp số</label>
                        <input type="text" name="transmission" class="form-control" placeholder="VD: 9G-TRONIC" value="<?= htmlspecialchars($_POST['transmission'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Loại nhiên liệu</label>
                        <input type="text" name="fuel_type" class="form-control" placeholder="VD: Xăng" value="<?= htmlspecialchars($_POST['fuel_type'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Mức tiêu thụ</label>
                        <input type="text" name="fuel_efficiency" class="form-control" placeholder="VD: 8.5 L/100km" value="<?= htmlspecialchars($_POST['fuel_efficiency'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Số chỗ ngồi</label>
                        <input type="number" name="seating" class="form-control" placeholder="VD: 5" value="<?= htmlspecialchars($_POST['seating'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Hệ dẫn động</label>
                        <input type="text" name="drive_type" class="form-control" placeholder="VD: 4MATIC" value="<?= htmlspecialchars($_POST['drive_type'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tốc độ tối đa (km/h)</label>
                        <input type="number" name="top_speed" class="form-control" placeholder="VD: 250" value="<?= htmlspecialchars($_POST['top_speed'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tăng tốc 0-100km/h (s)</label>
                        <input type="number" step="0.1" name="acceleration" class="form-control" placeholder="VD: 5.1" value="<?= htmlspecialchars($_POST['acceleration'] ?? '') ?>">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-5 border-top pt-4" style="border-color: var(--border) !important;">
                    <a href="cars.php" class="btn btn-secondary" style="background: var(--bg-secondary); border-color: var(--border); color: var(--text);">Hủy bỏ</a>
                    <button type="submit" class="btn btn-gold"><i class="bi bi-plus-circle me-1"></i> Lưu thông tin xe</button>
                </div>
            </form>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>

<script>
    document.getElementById('thumbnailInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('thumbnailPreview');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            }
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '<i class="bi bi-camera"></i>';
        }
    });
</script>
</body>
</html>
