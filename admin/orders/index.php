<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/db.php';

// OrderModel to handle order CRUD
require_once __DIR__ . '/../../models/OrderModel.php';

$db = new Database();
$orderModel = new OrderModel($db);

// Handle confirm or reject actions
if (isset($_GET['action'], $_GET['id'])) {
    $orderId = (int)$_GET['id'];
    $action = $_GET['action'];
    if ($action === 'confirm') {
        $orderModel->updateStatus($orderId, 'confirmed');
    } elseif ($action === 'reject') {
        $orderModel->updateStatus($orderId, 'rejected');
    }
    header('Location: index.php');
    exit;
}

// Get pending orders
$pendingOrders = $orderModel->getOrdersByStatus('pending');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Orders - Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
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

    <h3 class="mb-3">Pending Orders</h3>

    <table class="table table-bordered table-striped bg-white">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($pendingOrders): ?>
            <?php foreach ($pendingOrders as $order): ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td><?= number_format($order['total'],2) ?> MMK</td>
                    <td><?= htmlspecialchars($order['status']) ?></td>
                    <td><?= $order['created_at'] ?></td>
                    <td>
                        <a href="?id=<?= $order['id'] ?>&action=confirm" class="btn btn-sm btn-success mb-1" onclick="return confirm('Confirm this order?')">Confirm</a>
                        <a href="?id=<?= $order['id'] ?>&action=reject" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Reject this order?')">Reject</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">No pending orders</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
