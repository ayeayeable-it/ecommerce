<?php
// models/Order.php
require_once __DIR__ . '/../config/db.php';

class OrderModel {
    private $conn;

   

    // existing monthly income
    public function getMonthlyIncome(int $year): array {
        $sql = "
            SELECT MONTH(created_at) AS month, SUM(total) AS income
            FROM `order`
            WHERE status='confirmed' AND YEAR(created_at)=?
            GROUP BY MONTH(created_at)
            ORDER BY MONTH(created_at);
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $year);
        $stmt->execute();
        $res = $stmt->get_result();

        $months = [];
        for ($m = 1; $m <= 12; $m++) $months[$m] = 0.00;

        while ($row = $res->fetch_assoc()) {
            $months[(int)$row['month']] = (float)$row['income'];
        }
        $stmt->close();
        return $months;
    }

    public function getTotalIncomeForYear(int $year): float {
        $sql = "SELECT SUM(total) AS total_income FROM `orders` WHERE status='confirmed' AND YEAR(created_at)=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $year);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        return $row && $row['total_income'] ? (float)$row['total_income'] : 0.0;
    }

    /**
     * Create order transactionally
     * $items = [
     *   ['product_id' => 1, 'qty' => 2, 'price' => 49.99],
     *   ...
     * ];
     * Returns ['success'=>true,'order_id'=>123] or ['success'=>false,'msg'=>'...']
     */
    public function createOrder(int $customerId, array $items, int $paymentTypeId = 1): array {
        if (empty($items)) {
            return ['success' => false, 'msg' => 'Cart is empty'];
        }

        // compute total server-side
        $total = 0.0;
        foreach ($items as $it) {
            $total += ($it['price'] * $it['qty']);
        }

        // begin transaction
        $this->conn->begin_transaction();

        // 1) insert order
        $status = 'confirmed'; // or 'pending' based on your logic
        $stmt = $this->conn->prepare("INSERT INTO `order` (customer_id, total, status) VALUES (?, ?, ?)");
        if (!$stmt) {
            $this->conn->rollback();
            return ['success' => false, 'msg' => $this->conn->error];
        }
        $stmt->bind_param("ids", $customerId, $total, $status);
        if (!$stmt->execute()) {
            $stmt->close();
            $this->conn->rollback();
            return ['success' => false, 'msg' => $stmt->error];
        }
        $orderId = $this->conn->insert_id;
        $stmt->close();

        // prepare statements for details and stock update
        $stmtDetail = $this->conn->prepare("INSERT INTO orderDetail (order_id, product_id, qty, price) VALUES (?, ?, ?, ?)");
        if (!$stmtDetail) {
            $this->conn->rollback();
            return ['success' => false, 'msg' => $this->conn->error];
        }

        $stmtStock = $this->conn->prepare("UPDATE product SET stock = stock - ? WHERE id = ? AND stock >= ?");
        if (!$stmtStock) {
            $stmtDetail->close();
            $this->conn->rollback();
            return ['success' => false, 'msg' => $this->conn->error];
        }

        // iterate items
        foreach ($items as $it) {
            $pid = (int)$it['product_id'];
            $qty = (int)$it['qty'];
            $price = (float)$it['price'];

            // insert orderDetail
            $stmtDetail->bind_param("iiid", $orderId, $pid, $qty, $price);
            if (!$stmtDetail->execute()) {
                $stmtDetail->close();
                $stmtStock->close();
                $this->conn->rollback();
                return ['success' => false, 'msg' => $stmtDetail->error];
            }

            // update stock
            $stmtStock->bind_param("iii", $qty, $pid, $qty);
            if (!$stmtStock->execute()) {
                $stmtDetail->close();
                $stmtStock->close();
                $this->conn->rollback();
                return ['success' => false, 'msg' => $stmtStock->error];
            }

            if ($stmtStock->affected_rows === 0) {
                // not enough stock for this product
                $stmtDetail->close();
                $stmtStock->close();
                $this->conn->rollback();
                return ['success' => false, 'msg' => "Insufficient stock for product id {$pid}"];
            }
        }

        $stmtDetail->close();
        $stmtStock->close();

        // insert payment (simple)
        $stmtPay = $this->conn->prepare("INSERT INTO payment (order_id, payment_type_id, amount) VALUES (?, ?, ?)");
        if (!$stmtPay) {
            $this->conn->rollback();
            return ['success' => false, 'msg' => $this->conn->error];
        }
        $stmtPay->bind_param("iid", $orderId, $paymentTypeId, $total);
        if (!$stmtPay->execute()) {
            $stmtPay->close();
            $this->conn->rollback();
            return ['success' => false, 'msg' => $stmtPay->error];
        }
        $stmtPay->close();

        // commit
        $this->conn->commit();
        return ['success' => true, 'order_id' => $orderId];
    }
}
?>