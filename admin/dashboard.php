<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/OrderModel.php';

$db = new Database();
$orderModel = new OrderModel($db);

$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// Total income for the card
$totalIncome = $orderModel->getTotalIncomeForYear($year);

// Monthly data for Chart.js
$monthlyData = $orderModel->getOrdersByMonth($year);
$labels = json_encode($monthlyData['labels']);
$data = json_encode($monthlyData['data']);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Able Academy - Admin</a>
    <div class="d-flex">
      <span class="navbar-text me-3">Logged in as <?=htmlspecialchars($_SESSION['admin_user'] ?? 'admin')?></span>
      <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container my-4">

  <!-- Top Metrics -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card p-3 bg-success text-white">
        <h6>Total Income (<?= $year ?>)</h6>
        <h3><?= number_format($totalIncome, 2) ?> MMK</h3>
      </div>
    </div>
    <div class="col-md-9">
      <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="mb-0">Monthly Income</h5>
          <form id="yearForm" class="d-flex">
            <input type="number" id="yearInput" class="form-control form-control-sm me-2" value="<?= $year ?>" style="width:110px;">
            <button class="btn btn-sm btn-primary" type="button" id="btnLoad">Load</button>
          </form>
        </div>
        <canvas id="incomeChart" height="120"></canvas>
      </div>
    </div>
  </div>

  <!-- Admin Function Shortcuts -->
  <h5 class="mb-3">Quick Access</h5>
  <div class="row g-3">
    <div class="col-md-3">
      <a href="products/index.php" class="text-decoration-none">
        <div class="card p-4 text-center shadow-sm hover-shadow">
          <i class="bi bi-box-seam fs-2 mb-2"></i>
          <h6>Products</h6>
        </div>
      </a>
    </div>
    <div class="col-md-3">
      <a href="product_types/index.php" class="text-decoration-none">
        <div class="card p-4 text-center shadow-sm hover-shadow">
          <i class="bi bi-tags fs-2 mb-2"></i>
          <h6>Product Types</h6>
        </div>
      </a>
    </div>
    <div class="col-md-3">
      <a href="orders/index.php" class="text-decoration-none">
        <div class="card p-4 text-center shadow-sm hover-shadow">
          <i class="bi bi-receipt fs-2 mb-2"></i>
          <h6>Orders</h6>
        </div>
      </a>
    </div>
    <div class="col-md-3">
      <a href="customers/index.php" class="text-decoration-none">
        <div class="card p-4 text-center shadow-sm hover-shadow">
          <i class="bi bi-people fs-2 mb-2"></i>
          <h6>Customers</h6>
        </div>
      </a>
    </div>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('incomeChart').getContext('2d');
    const labels = <?= $labels ?>;
    const data = <?= $data ?>;

    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Income',
                data: data,
                borderWidth: 1,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: function(val){ return 'MMK ' + val; } }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'MMK ' + Number(context.parsed.y).toFixed(2);
                        }
                    }
                }
            }
        }
    });

    document.getElementById('btnLoad').addEventListener('click', function(){
        const year = document.getElementById('yearInput').value || new Date().getFullYear();
        window.location.href = 'dashboard.php?year=' + year;
    });
});
</script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
.hover-shadow:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    transition: all 0.3s ease-in-out;
}
</style>

</body>
</html>
