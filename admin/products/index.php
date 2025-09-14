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
$products = $productModel->getAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Products - Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="../dashboard.php">Able Academy - Admin</a>
        <div class="d-flex">
            <span class="navbar-text me-3">
                Logged in as <?= htmlspecialchars($_SESSION['admin_user'] ?? 'admin') ?>
            </span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="mb-3">
    <a href="../dashboard.php" class="btn btn-secondary">&larr; Back to Dashboard</a>
</div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Products</h3>
        <a href="create.php" class="btn btn-success">+ Create Product</a>
    </div>

    <table class="table table-striped table-bordered bg-white">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Image</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($products): ?>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= htmlspecialchars($p['type_name'] ?? '-') ?></td>
                        <td><?= number_format($p['price'], 2) ?></td>
                        <td><?= (int)$p['stock'] ?></td>
                        <td>
                            <?php if ($p['image']): ?>
                                <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width:50px;height:auto;">
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($p['description']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-primary mb-1">Edit</a>
                            <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Are you sure to delete this product?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" class="text-center">No products found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
