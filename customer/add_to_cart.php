<?php
// customer/add_to_cart.php
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/ProductModel.php';

$db = new Database();
$conn = $db->getConnection();
$productModel = new ProductModel($conn);

// Make sure it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int) $_POST['product_id'];
    $qty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;

    $product = $productModel->getById($product_id);

    if ($product) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Update quantity if already in cart
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $qty;
        } else {
            $_SESSION['cart'][$product_id] = [
                'id'       => $product['id'],
                'name'     => $product['name'],
                'price'    => $product['price'],
                'quantity' => $qty
            ];
        }

        $_SESSION['flash'] = "Added {$qty} × {$product['name']} to cart!";
    } else {
        $_SESSION['flash'] = "Product not found.";
    }
} else {
    $_SESSION['flash'] = "Invalid request.";
}

header("Location: cart.php");
exit;
