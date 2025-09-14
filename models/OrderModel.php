<?php
class OrderModel {
    private $conn;

    public function __construct($db) {
        if ($db instanceof Database) {
            $this->conn = $db->getConnection();
        } else {
            $this->conn = $db;
        }

        if (!$this->conn) {
            die("Database connection failed in OrderModel.");
        }
    }

    // Total income for a given year
    public function getTotalIncomeForYear($year) {
        $sql = "SELECT SUM(total) AS total_income FROM `order` WHERE YEAR(created_at) = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return 0;

        $stmt->bind_param("i", $year);
        $total_income = 0;
        $stmt->execute();
        $stmt->bind_result($total_income);
        $stmt->fetch();
        $stmt->close();

        return $total_income ?: 0;
    }

    // Monthly income for a given year (Jan-Dec)
    public function getMonthlyIncome($year) {
        $sql = "SELECT MONTH(created_at) AS month, SUM(total) AS income 
                FROM `order` 
                WHERE YEAR(created_at) = ? 
                GROUP BY MONTH(created_at)";
        $stmt = $this->conn->prepare($sql);
        $months = array_fill(1, 12, 0); // initialize all months
        if (!$stmt) return $months;

        $stmt->bind_param("i", $year);
        $month = 0;
        $income = 0.0;
        $stmt->execute();
        $stmt->bind_result($month, $income);
        while ($stmt->fetch()) {
            $months[(int)$month] = (float)$income;
        }
        $stmt->close();

        return $months;
    }

    // Return data formatted for Chart.js
    public function getOrdersByMonth($year) {
        $labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $monthlyIncome = $this->getMonthlyIncome($year);
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = isset($monthlyIncome[$i]) ? $monthlyIncome[$i] : 0;
        }
        return ['labels' => $labels, 'data' => $data];
    }
    // Get pending orders
    public function getOrdersByStatus($status) {
        $sql = "SELECT o.id, o.customer_id, o.total, o.status, o.created_at, c.name AS customer_name
                FROM `order` o
                JOIN customer c ON o.customer_id=c.id
                WHERE o.status=? ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $res = $stmt->get_result();
        $orders = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $orders;
    }

    // Update order status
    public function updateStatus($orderId, $status) {
        $sql = "UPDATE `order` SET status=? WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param("si", $status, $orderId);
        $stmt->execute();
        $stmt->close();
        return true;
    }

}
?>
