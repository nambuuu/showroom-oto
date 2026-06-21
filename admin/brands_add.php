<?php
require_once '../config/auth_guard.php';
require_once '../config/db.php';

$pageTitle = 'Add Brand';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $logo = '';

    if (empty($name)) {
        $error = 'Please enter brand name.';
    } else {
        // Upload logo if exists
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/image/brands/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $ext;
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetFile)) {
                $logo = $fileName;
            } else {
                $error = 'Image upload error.';
            }
        }

        if (empty($error)) {
            $stmt = $pdo->prepare('INSERT INTO brands (name, country, logo) VALUES (?, ?, ?)');
            if ($stmt->execute([$name, $country, $logo])) {
                $success = 'Brand added successfully.';
            } else {
                $error = 'Database error occurred.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> – Admin</title>
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
            max-width: 600px;
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
            width: 150px;
            height: 100px;
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
            font-size: 32px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content" id="mainContent">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-body">
        
        <div class="d-flex align-items-center mb-4">
            <a href="brands.php" class="btn btn-outline-gold me-3"><i class="bi bi-arrow-left"></i> Back</a>
            <h4 class="mb-0 text-gold" style="font-family: 'Orbitron', sans-serif;">Add New Brand</h4>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger" style="background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.3); color: #ef4444; max-width: 600px; margin: 0 auto 20px;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success" style="background: rgba(16,185,129,0.1); border-color: rgba(16,185,129,0.3); color: #10b981; max-width: 600px; margin: 0 auto 20px;">
                <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <div class="form-glass">
            <div class="form-glass-header">
                <h3><i class="bi bi-award-fill me-2"></i>Brand Information</h3>
                <p class="text-muted" style="font-size: 14px;">Please fill in all information to add a new brand.</p>
            </div>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="form-label">Brand name <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text" style="background: var(--bg-secondary); border-color: var(--border); color: var(--gold);"><i class="bi bi-tag-fill"></i></span>
                        <input type="text" name="name" class="form-control" required placeholder="Ex: Mercedes-Benz, BMW, Audi..." value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Country</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background: var(--bg-secondary); border-color: var(--border); color: var(--gold);"><i class="bi bi-globe"></i></span>
                        <input type="text" name="country" class="form-control" placeholder="Ex: Germany, Japan, USA..." value="<?= htmlspecialchars($_POST['country'] ?? '') ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Brand Logo</label>
                    <div class="d-flex align-items-center gap-3">
                        <div class="upload-preview" id="logoPreview">
                            <i class="bi bi-image"></i>
                        </div>
                        <div>
                            <input type="file" name="logo" id="logoInput" class="form-control mb-2" accept="image/*">
                            <small class="text-muted">Supported formats: JPG, PNG, WEBP. Max 2MB.</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-5">
                    <button type="reset" class="btn btn-secondary" style="background: var(--bg-secondary); border-color: var(--border); color: var(--text);">Reset</button>
                    <button type="submit" class="btn btn-gold"><i class="bi bi-plus-circle me-1"></i> Save brand</button>
                </div>
            </form>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>
<script>
    document.getElementById('logoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('logoPreview');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            }
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '<i class="bi bi-image"></i>';
        }
    });
</script>
</body>
</html>
