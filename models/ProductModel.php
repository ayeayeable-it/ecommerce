<?php
class ProductModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db instanceof Database ? $db->getConnection() : $db;
        if (!$this->conn) die("Database connection failed in ProductModel.");
    }

    // === Customer methods ===
    public function getAll() {
        $sql = "SELECT p.id, p.name, p.description, p.price, p.stock, pt.name AS type_name, p.image
                FROM product p
                LEFT JOIN productType pt ON p.product_type_id = pt.id";
        $res = $this->conn->query($sql);
        $products = [];
        while ($row = $res->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT p.id, p.name, p.description, p.price, p.stock, pt.name AS type_name, p.image,p.product_type_id
                                      FROM product p
                                      LEFT JOIN productType pt ON p.product_type_id = pt.id
                                      WHERE p.id=? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function searchProducts($keyword) {
        $keyword = $this->conn->real_escape_string($keyword);
        $sql = "SELECT p.id, p.name, p.description, p.price, p.stock, pt.name AS type_name, p.image
                FROM product p
                LEFT JOIN productType pt ON p.product_type_id = pt.id
                WHERE p.name LIKE '%$keyword%' OR p.description LIKE '%$keyword%'";
        $res = $this->conn->query($sql);
        $products = [];
        while ($row = $res->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    // === Admin methods ===
    public function getAllTypes() {
        $types = [];
        $res = $this->conn->query("SELECT * FROM productType ORDER BY name ASC");
        while ($row = $res->fetch_assoc()) {
            $types[] = $row;
        }
        return $types;
    }

    public function createProduct($data) {
        $stmt = $this->conn->prepare("INSERT INTO product (name, product_type_id, price, stock, image, description) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("sidiss", $data['name'], $data['product_type_id'], $data['price'], $data['stock'], $data['image'], $data['description']);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    }

    public function updateProduct($id, $data) {
        $stmt = $this->conn->prepare("UPDATE product SET name=?, product_type_id=?, price=?, stock=?, image=?, description=? WHERE id=?");
        $stmt->bind_param("sidissi", $data['name'], $data['product_type_id'], $data['price'], $data['stock'], $data['image'], $data['description'], $id);
        $stmt->execute();
        $stmt->close();
        return true;
    }

    public function deleteProduct($id) {
        $stmt = $this->conn->prepare("DELETE FROM product WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        return true;
    }
}
?>
