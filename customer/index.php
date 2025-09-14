<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/ProductModel.php';

$db = new Database();
$conn = $db->getConnection();
$productModel = new ProductModel($conn);

// Get products
$products = $productModel->getAll();
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// Login state
$logged_in = isset($_SESSION['customer_id']);
$customer_name = $_SESSION['customer_name'] ?? '';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Able Academy Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .card:hover { transform: scale(1.03); transition: 0.3s; }
    .card-img-top { height: 200px; object-fit: cover; }
    .icon-btn { position: relative; font-size: 1.4rem; color: white; margin-left: 15px; }
    .cart-count {
        position: absolute;
        top: -5px;
        right: -10px;
        background: red;
        color: white;
        border-radius: 50%;
        font-size: 12px;
        width: 20px;
        height: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
  </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
        <svg width="50" height="41" viewBox="0 0 55 41" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M35.5 0.5C45.9934 0.5 54.5 9.00659 54.5 19.5V40.5C50.0817 40.5 46.5 36.9183 46.5 32.5V19.5C46.5 13.4249 41.5751 8.5 35.5 8.5H30.2988C27.6467 8.50004 25.1029 9.55339 23.2275 11.4287L9.67188 24.9854C8.92179 25.7354 8.50006 26.7527 8.5 27.8135V28.5C8.5 30.7091 10.2909 32.5 12.5 32.5H30.5C30.5 36.7801 27.1389 40.2748 22.9121 40.4893L22.5 40.5H12.5C5.87259 40.5 0.5 35.1274 0.5 28.5V27.8135C0.500062 24.631 1.76427 21.5785 4.01465 19.3281L17.5713 5.77246C20.9469 2.39685 25.525 0.500044 30.2988 0.5H35.5Z" fill="#FF500B"></path>
<path d="M37.5 12.5C40.2614 12.5 42.5 14.7386 42.5 17.5V40.5C38.0817 40.5 34.5 36.9183 34.5 32.5V20.5H31.1562L24.6207 27.0355C23.683 27.9732 22.4113 28.5 21.0852 28.5H12.9775C12.5588 28.5 12.3491 27.9937 12.6452 27.6976L26.3789 13.9648C27.3165 13.0272 28.588 12.5 29.9141 12.5H37.5Z" fill="#FF500B"></path>
</svg>

        <h3> Able Bag Shop</h3>
    </a>
    
    <div class="d-flex align-items-center ms-auto">
        <!-- Cart Icon -->
        <a href="cart.php" class="position-relative icon-btn">
            <i class="bi bi-cart-fill"></i>
            <?php if($cart_count): ?>
                <span class="cart-count"><?= $cart_count ?></span>
            <?php endif; ?>
        </a>

        <!-- User Icon -->
        <?php if($logged_in): ?>
            <div class="dropdown ms-3">
                <a href="#" class="text-white text-decoration-none" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle" style="font-size:1.6rem;"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="dropdown-header"><?= htmlspecialchars($customer_name) ?></li>
                    <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </div>
        <?php else: ?>
            <a href="login.php" class="text-white ms-3" title="Login"><i class="bi bi-box-arrow-in-right" style="font-size:1.6rem;"></i></a>
        <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Main and Footer remain same as previous code -->


<!-- Main content -->
<div class="container py-5">
  <div>
    <h2>Welcome</h2>
    <p>"women's designer clutch bag" or "vegan leather tote for work"</p>
  </div>
  <h2 class="mb-4">Our Products</h2>
  <div class="row g-4">
    <?php if(!empty($products)): ?>
        <?php foreach($products as $p): ?>
            <div class="col-sm-6 col-md-4 col-lg-3">
              <div class="card shadow-sm">
                <img src="<?= htmlspecialchars($p['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']) ?>">
                <div class="card-body">
                  <h5 class="card-title"><?= htmlspecialchars($p['name']) ?></h5>
                  <p class="card-text"><?= number_format($p['price'], 2) ?> MMK</p>
                  <div class="d-flex justify-content-between align-items-center">
                    <a href="product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-primary">View</a>
                    <form method="post" action="add_to_cart.php" class="d-flex">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <input type="number" name="qty" min="1" max="<?= (int)$p['stock'] ?>" value="1" class="form-control form-control-sm me-2" style="width:60px">
                        <button class="btn btn-sm btn-success" <?= (int)$p['stock'] <= 0 ? 'disabled' : '' ?>>Add</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No products found.</p>
    <?php endif; ?>
  </div>
</div>

<!-- Footer -->
<footer class="bg-primary text-white py-4 mt-5">
  <div class="container text-center">
    &copy; <?= date('Y') ?> Able Academy. All rights reserved.
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
