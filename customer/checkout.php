<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$cart = $_SESSION['cart'] ?? [];
$customer_id = $_SESSION['customer_id'] ?? null;

if (!$cart || !$customer_id) {
    $_SESSION['flash'] = "Your cart is empty or you are not logged in.";
    header("Location: cart.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Start transaction
$conn->begin_transaction();

try {
    // 1️⃣ Insert into order table
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    $stmt = $conn->prepare("INSERT INTO `order` (customer_id, total, status, created_at) VALUES (?, ?, ?, NOW())");
    $status = 'pending';
    $stmt->bind_param("ids", $customer_id, $total, $status);
    $stmt->execute();

    $order_id = $stmt->insert_id; // Get the inserted order ID
    $stmt->close();

    // 2️⃣ Insert each item into orderDetail table
    $stmtDetail = $conn->prepare("INSERT INTO orderDetail (order_id, product_id, qty) VALUES (?, ?, ?)");
    foreach ($cart as $item) {
        $stmtDetail->bind_param("iii", $order_id, $item['id'], $item['quantity']);
        $stmtDetail->execute();
    }
    $stmtDetail->close();

    // Commit transaction
    $conn->commit();

    // Clear cart
    $_SESSION['cart'] = [];
    $_SESSION['flash'] = "Checkout complete! Your order #$order_id has been placed.";

    header("Location: index.php");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['flash'] = "Checkout failed: " . $e->getMessage();
    header("Location: cart.php");
    exit;
}
