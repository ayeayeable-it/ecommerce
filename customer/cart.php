<?php
session_start();
$cart = $_SESSION['cart'] ?? [];

// Handle clear cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear'])) {
    $_SESSION['cart'] = [];
    $_SESSION['flash'] = "Cart cleared.";
    header("Location: cart.php");
    exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Your Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <a href="index.php" class="btn btn-link">&larr; Continue shopping</a>

  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info">
      <?= htmlspecialchars($_SESSION['flash']) ?>
      <?php unset($_SESSION['flash']); ?>
    </div>
  <?php endif; ?>

  <h2>Your Cart</h2>

  <?php if (empty($cart)): ?>
    <p>Your cart is empty.</p>
  <?php else: ?>
    <form method="post">
      <table class="table table-bordered" id="cart-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Price (MMK)</th>
            <th>Qty</th>
            <th>Total (MMK)</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $grand = 0; ?>
          <?php foreach ($cart as $id => $item): ?>
            <?php $line = $item['price'] * $item['quantity']; $grand += $line; ?>
            <tr data-id="<?= $id ?>">
              <td><?= htmlspecialchars($item['name']) ?></td>
              <td class="price"><?= number_format($item['price'], 2) ?></td>
              <td>
                <input type="number" class="qty-input form-control" min="0" value="<?= (int)$item['quantity'] ?>" style="width:80px">
              </td>
              <td class="line-total"><?= number_format($line, 2) ?></td>
              <td>
                <a href="remove_from_cart.php?id=<?= $id ?>" class="btn btn-danger btn-sm">Remove</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" class="text-end"><strong>Grand Total</strong></td>
            <td colspan="2" id="grand-total"><strong><?= number_format($grand, 2) ?></strong></td>
          </tr>
        </tfoot>
      </table>
      <button type="submit" name="clear" class="btn btn-warning">Clear Cart</button>
      <a href="checkout.php" class="btn btn-success">Checkout</a>
    </form>
  <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cartTable = document.getElementById('cart-table');
    if (!cartTable) return;

    const grandTotalElem = document.getElementById('grand-total');

    function updateTotals() {
        let grandTotal = 0;
        const rows = cartTable.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const price = parseFloat(row.querySelector('.price').textContent.replace(/,/g,''));
            const qty = parseInt(row.querySelector('.qty-input').value) || 0;
            const lineTotal = price * qty;
            row.querySelector('.line-total').textContent = lineTotal.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
            grandTotal += lineTotal;
        });
        grandTotalElem.textContent = grandTotal.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
    }

    cartTable.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('input', updateTotals);
    });
});
</script>
</body>
</html>
