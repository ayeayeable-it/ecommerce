<?php
session_start();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
        $_SESSION['flash'] = "Item removed from cart.";
    }
}

header("Location: cart.php");
exit;
