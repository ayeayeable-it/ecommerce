<?php
// api/get_monthly_income.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error'=>'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Order.php';

$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date("Y");

$db = new Database();
$orderModel = new OrderModel($db);

$months = $orderModel->getMonthlyIncome($year);

$data = [];
for ($i=1;$i<=12;$i++) {
    $data[] = $months[$i];
}

header("Content-Type: application/json");
echo json_encode([
    "year"   => $year,
    "labels" => ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
    "data"   => $data
]);
