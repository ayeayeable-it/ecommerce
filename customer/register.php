<?php
require_once __DIR__ . '/../config/db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $db = new Database();
    $conn = $db->getConnection(); // ✅ Use public method

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM customer WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $message = "Email already registered!";
    } else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO customer (name,email,password) VALUES (?,?,?)");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);
        if ($stmt->execute()) {
            $message = "Registration successful! You can login now.";
        } else {
            $message = "Error: " . $stmt->error;
        }
    }
    $stmt->close();
}
?>
