<?php
// customer/order_success.php
session_start();
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Order Placed</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="card p-4">
    <h3>Thank you! Your order was placed.</h3>
    <?php if ($orderId): ?>
      <p>Your order ID: <strong>#<?= $orderId ?></strong></p>
    <?php endif; ?>
    <a href="index.php" class="btn btn-primary">Continue shopping</a>
  </div>
</div>
</body>
</html>
