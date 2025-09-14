<?php 
// customer/product.php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/ProductModel.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: index.php');
    exit;
}

$db = new Database();
$conn = $db->getConnection();        // ✅ use mysqli object
$productModel = new ProductModel($conn);

$p = $productModel->getById($id);
if (!$p) {
    $_SESSION['flash'] = 'Product not found';
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($p['name']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <a href="index.php" class="btn btn-link">&larr; Back to shop</a>
 <div class="col-sm-6 col-md-4 col-lg-3">
              <div class="card shadow-sm">
     <img src="<?= htmlspecialchars($p['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']) ?>">
    <h3><?= htmlspecialchars($p['name']) ?></h3>
    <p><strong>Type:</strong> <?= htmlspecialchars($p['type_name'] ?? '-') ?></p>
    <p><strong>Price:</strong> <?= number_format($p['price'],2) ?> MMK</p>
    <p><strong>Stock:</strong> <?= (int)$p['stock'] ?></p>

    <form method="post" action="add_to_cart.php" class="row g-2">
      <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
      <div class="col-auto">
        <input type="number" name="qty" min="1" max="<?= (int)$p['stock'] ?>" value="1" class="form-control">
      </div>
      <div class="col-auto">
        <button class="btn btn-success" <?= (int)$p['stock'] <= 0 ? 'disabled' : '' ?>>Add to cart</button>
      </div>
    </form>
  </div>
</div>
</div>
</body>
</html>
