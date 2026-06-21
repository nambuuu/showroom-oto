<?php
require_once '../config/auth_guard.php';
require_once '../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: cars.php');
    exit;
}

// Fetch car data
$stmt = $pdo->prepare('SELECT c.*, ci.image AS thumbnail FROM cars c LEFT JOIN car_images ci ON ci.car_id = c.id AND ci.is_main = 1 WHERE c.id = :id');
$stmt->execute([':id' => $id]);
$car = $stmt->fetch();
if (!$car) {
    header('Location: cars.php?err=notfound');
    exit;
}

// Lấy thông số kỹ thuật hiện có
$specStmt = $pdo->prepare('SELECT * FROM car_specifications WHERE car_id = :id');
$specStmt->execute([':id' => $id]);
$car_specs = $specStmt->fetch(PDO::FETCH_ASSOC) ?: [];

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $name     = trim($_POST['model_name'] ?? '');
    $brand_id = $_POST['brand_id'] ?? '';
    $year     = (int)($_POST['year'] ?? 0);
    $price    = (float)($_POST['price'] ?? 0);
    $status   = $_POST['status'] ?? 'available';
    
    if ($name === '' || $brand_id === '' || $year <= 0 || $price <= 0) {
        $errors[] = 'Please enter valid and complete information.';
    }
    
    // Specifications from POST
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

    // Handle optional new thumbnail
    $thumbnailPath = $car['thumbnail'] ?? '';
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg','image/png','image/webp'];
        if (!in_array($_FILES['thumbnail']['type'], $allowed)) {
            $errors[] = 'Invalid image format (jpg/png/webp).';
        }
        
        $max_size = 5 * 1024 * 1024;
        if ($_FILES['thumbnail']['size'] > $max_size) {
            $errors[] = 'Image size must not exceed 5MB.';
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
                $errors[] = 'Error saving image.';
            } else {
                $thumbnailPath = 'assets/image/cars/' . $newName;
            }
        }
    }
    
    if (empty($errors)) {
        $upd = $pdo->prepare('UPDATE cars SET model_name = :model_name, brand_id = :brand_id, year = :year, price = :price, status = :status WHERE id = :id');
        $upd->execute([
            ':model_name' => $name,
            ':brand_id' => $brand_id,
            ':year'     => $year,
            ':price'    => $price,
            ':status'   => $status,
            ':id'       => $id
        ]);

        // Nếu có ảnh mới → cập nhật/thêm vào car_images
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK && $thumbnailPath !== '') {
            $imgName = basename($thumbnailPath);
            // Xóa ảnh main cũ
            $pdo->prepare('DELETE FROM car_images WHERE car_id = :id AND is_main = 1')->execute([':id' => $id]);
            // Thêm ảnh main mới
            $imgStmt = $pdo->prepare('INSERT INTO car_images (car_id, image, is_main) VALUES (:car_id, :image, 1)');
            $imgStmt->execute([
                ':car_id' => $id,
                ':image'  => $imgName
            ]);
            
            // Xóa file ảnh cũ khỏi server nếu có (Optional nhưng tốt)
            if (!empty($car['thumbnail']) && file_exists('../assets/image/cars/' . $car['thumbnail'])) {
                unlink('../assets/image/cars/' . $car['thumbnail']);
            }
        }

        // Save specs
        $checkSpec = $pdo->prepare('SELECT id FROM car_specifications WHERE car_id = :id');
        $checkSpec->execute([':id' => $id]);
        if ($checkSpec->fetch()) {
            $updSpec = $pdo->prepare('UPDATE car_specifications SET engine = :engine, horsepower = :horsepower, torque = :torque, transmission = :transmission, fuel_type = :fuel_type, fuel_efficiency = :fuel_efficiency, seating = :seating, drive_type = :drive_type, top_speed = :top_speed, acceleration = :acceleration WHERE car_id = :car_id');
            $updSpec->execute([
                ':engine' => $engine ?: null,
                ':horsepower' => $horsepower,
                ':torque' => $torque ?: null,
                ':transmission' => $transmission ?: null,
                ':fuel_type' => $fuel_type ?: null,
                ':fuel_efficiency' => $fuel_efficiency ?: null,
                ':seating' => $seating,
                ':drive_type' => $drive_type ?: null,
                ':top_speed' => $top_speed,
                ':acceleration' => $acceleration,
                ':car_id' => $id
            ]);
        } else {
            $insSpec = $pdo->prepare('INSERT INTO car_specifications (car_id, engine, horsepower, torque, transmission, fuel_type, fuel_efficiency, seating, drive_type, top_speed, acceleration) VALUES (:car_id, :engine, :horsepower, :torque, :transmission, :fuel_type, :fuel_efficiency, :seating, :drive_type, :top_speed, :acceleration)');
            $insSpec->execute([
                ':car_id' => $id,
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
        }

        header('Location: cars.php?msg=updated');
        exit;
    }
}

// Brand list for dropdown
$brandStmt = $pdo->query('SELECT id, name FROM brands ORDER BY name');
$brands = $brandStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Car – Admin</title>
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
            <a href="cars.php" class="btn btn-outline-gold me-3"><i class="bi bi-arrow-left"></i> Back</a>
            <h4 class="mb-0 text-gold" style="font-family: 'Orbitron', sans-serif;">Edit Car</h4>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" style="background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.3); color: #ef4444; max-width: 800px; margin: 0 auto 20px;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo implode('<br>', $errors); ?>
            </div>
        <?php endif; ?>

        <div class="form-glass">
            <div class="form-glass-header">
                <h3><i class="bi bi-pencil-square me-2"></i>Update Car Information</h3>
                <p class="text-muted" style="font-size: 14px;">Edit information for car <strong><?= htmlspecialchars($car['model_name']) ?></strong>.</p>
            </div>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="section-title">Basic information</div>
                        
                        <div class="mb-3">
                            <label class="form-label">Car name <span class="text-danger">*</span></label>
                            <input type="text" name="model_name" class="form-control" value="<?php echo htmlspecialchars($car['model_name']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Brand <span class="text-danger">*</span></label>
                            <select name="brand_id" class="form-select" required>
                                <?php foreach ($brands as $b): ?>
                                    <option value="<?php echo $b['id']; ?>"<?php if ($car['brand_id'] == $b['id']) echo ' selected'; ?>><?php echo htmlspecialchars($b['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label">Year <span class="text-danger">*</span></label>
                                <input type="number" name="year" class="form-control" value="<?php echo $car['year']; ?>" min="1900" max="2099" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="available"<?php if($car['status']==='available') echo ' selected'; ?>>Available</option>
                                    <option value="sold_out"<?php if($car['status']==='sold_out') echo ' selected'; ?>>Sold Out</option>
                                    <option value="coming_soon"<?php if($car['status']==='coming_soon') echo ' selected'; ?>>Coming Soon</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Listed Price (VND) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="price" class="form-control" value="<?php echo $car['price']; ?>" min="0" step="1000" required>
                                <span class="input-group-text" style="background: var(--bg-secondary); border-color: var(--border); color: var(--gold);">₫</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="section-title">Thumbnail image</div>
                        <div class="upload-preview" id="thumbnailPreview">
                            <?php if (!empty($car['thumbnail'])): ?>
                                <img src="../assets/image/cars/<?php echo htmlspecialchars($car['thumbnail']); ?>" alt="thumb">
                            <?php else: ?>
                                <i class="bi bi-camera"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <input type="file" name="thumbnail" id="thumbnailInput" class="form-control mb-2" accept="image/jpeg,image/png,image/webp">
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Leave blank if you do not want to change the image.</small>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-2">
                    <div class="col-12">
                        <div class="section-title">Specifications </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Engine</label>
                        <input type="text" name="engine" class="form-control" value="<?= htmlspecialchars($car_specs['engine'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Horsepower (HP)</label>
                        <input type="number" name="horsepower" class="form-control" value="<?= htmlspecialchars($car_specs['horsepower'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Torque</label>
                        <input type="text" name="torque" class="form-control" value="<?= htmlspecialchars($car_specs['torque'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Transmission</label>
                        <input type="text" name="transmission" class="form-control" value="<?= htmlspecialchars($car_specs['transmission'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Fuel type</label>
                        <input type="text" name="fuel_type" class="form-control" value="<?= htmlspecialchars($car_specs['fuel_type'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Fuel efficiency</label>
                        <input type="text" name="fuel_efficiency" class="form-control" value="<?= htmlspecialchars($car_specs['fuel_efficiency'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Seating</label>
                        <input type="number" name="seating" class="form-control" value="<?= htmlspecialchars($car_specs['seating'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Drive type</label>
                        <input type="text" name="drive_type" class="form-control" value="<?= htmlspecialchars($car_specs['drive_type'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Top speed (km/h)</label>
                        <input type="number" name="top_speed" class="form-control" value="<?= htmlspecialchars($car_specs['top_speed'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Acceleration 0-100km/h (s)</label>
                        <input type="number" step="0.1" name="acceleration" class="form-control" value="<?= htmlspecialchars($car_specs['acceleration'] ?? '') ?>">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-5 border-top pt-4" style="border-color: var(--border) !important;">
                    <a href="cars.php" class="btn btn-secondary" style="background: var(--bg-secondary); border-color: var(--border); color: var(--text);">Cancel</a>
                    <button type="submit" class="btn btn-gold"><i class="bi bi-save me-1"></i> Update information</button>
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
            <?php if (!empty($car['thumbnail'])): ?>
                preview.innerHTML = `<img src="../assets/image/cars/<?php echo htmlspecialchars($car['thumbnail']); ?>" alt="thumb">`;
            <?php else: ?>
                preview.innerHTML = '<i class="bi bi-camera"></i>';
            <?php endif; ?>
        }
    });
</script>
</body>
</html>
