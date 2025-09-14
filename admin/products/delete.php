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

if ($id) {
    $productModel->deleteProduct($id);
}

header('Location: index.php');
exit;
?>
