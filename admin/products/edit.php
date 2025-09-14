<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/ProductModel.php';

$db = new Database();
$productModel = new ProductModel($db);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $productModel->getById($id);

if (!$product) {
    header('Location: index.php');
    exit;
}

$types = $productModel->getAllTypes();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'],
        'product_type_id' => (int)$_POST['product_type_id'],
        'price' => (float)$_POST['price'],
        'stock' => (int)$_POST['stock'],
        'image' => $_POST['image'],
        'description' => $_POST['description']
    ];

    $productModel->updateProduct($id, $data);
    $message = "Product updated successfully!";
    header("loation:index.php");
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Edit Product - Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="../dashboard.php">Able Academy - Admin</a>
        <div class="d-flex">
            <span class="navbar-text me-3">Logged in as <?= htmlspecialchars($_SESSION['admin_user'] ?? 'admin') ?></span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Edit Product</h3>
        <a href="index.php" class="btn btn-secondary">← Back to Products</a>
    </div>

    <?php if($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label>Name</label>
            <input name="name" value="<?= htmlspecialchars($product['name']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Type</label>
            <select name="product_type_id" class="form-control" required>
                <option value="">Select Type</option>
                <?php foreach($types as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= $product['product_type_id'] == $t['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Price</label>
            <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stock" value="<?= $product['stock'] ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Image URL</label>
            <input type="text" name="image" value="<?= htmlspecialchars($product['image']) ?>" class="form-control">
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>
        <button class="btn btn-success">Update Product</button>
    </form>
</div>
</body>
</html>
