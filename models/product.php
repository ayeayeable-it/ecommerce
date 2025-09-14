<?php
// models/Product.php
require_once __DIR__ . '/../config/db.php';

class ProductModel {
    private $conn;

    public function __construct(Database $db) {
        $this->conn = $db->conn;
    }

    // Get all products
    public function getAll($limit = null) {
        $sql = "SELECT p.*, pt.name AS type_name
                FROM product p
                LEFT JOIN productType pt ON p.product_type_id = pt.id
                ORDER BY p.id DESC";
        if ($limit) $sql .= " LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        if ($limit) {
            $stmt->bind_param("i", $limit);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    // Search products by name or type
    public function search($term) {
        $like = "%{$term}%";
        $sql = "SELECT p.*, pt.name AS type_name
                FROM product p
                LEFT JOIN productType pt ON p.product_type_id = pt.id
                WHERE p.name LIKE ? OR pt.name LIKE ?
                ORDER BY p.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $like, $like);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    // Get product by id
    public function getById($id) {
        $sql = "SELECT p.*, pt.name AS type_name
                FROM product p
                LEFT JOIN productType pt ON p.product_type_id = pt.id
                WHERE p.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        return $row;
    }

    // Reduce stock safely within checkout transaction (but helper here)
    public function reduceStock($productId, $qty) {
        $sql = "UPDATE product SET stock = stock - ? WHERE id = ? AND stock >= ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $qty, $productId, $qty);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected > 0;
    }
}
?>