<?php
// customer/update_cart.php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Product.php';

$db = new Database();
$productModel = new ProductModel($db);

if (isset($_GET['action']) && $_GET['action'] === 'remove') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id && isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
        $_SESSION['flash'] = 'Item removed from cart.';
    }
    header('Location: cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qty']) && is_array($_POST['qty'])) {
    foreach ($_POST['qty'] as $pid => $qtyVal) {
        $pid = (int)$pid;
        $qty = max(1, (int)$qtyVal);
        if (!isset($_SESSION['cart'][$pid])) continue;

        // ensure not more than stock
        $prod = $productModel->getById($pid);
        if (!$prod) {
            unset($_SESSION['cart'][$pid]);
            continue;
        }
        if ($qty > (int)$prod['stock']) {
            $_SESSION['cart'][$pid]['qty'] = (int)$prod['stock'];
            $_SESSION['flash'] = 'Adjusted quantity to available stock for some items.';
        } else {
            $_SESSION['cart'][$pid]['qty'] = $qty;
        }
    }
}

header('Location: cart.php');
exit;
