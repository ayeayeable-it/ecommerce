<?php
require_once __DIR__ . '/../config/db.php';

// Create Database object
$db = new Database();
$conn = $db->getConnection(); // ✅ use public method

// Admin credentials
$username = "admin";
$password = "admin123";
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Prepare and execute insert
$stmt = $conn->prepare("INSERT INTO admin_user (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashed);

if ($stmt->execute()) {
    echo "Admin account created successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
