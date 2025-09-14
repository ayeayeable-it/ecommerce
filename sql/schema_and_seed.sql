-- Create DB (run once)
CREATE DATABASE IF NOT EXISTS able_ecom CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE able_ecom;

-- customers (minimal)
CREATE TABLE IF NOT EXISTS customer (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(150) UNIQUE,
  password VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- products
CREATE TABLE IF NOT EXISTS product (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150),
  product_type_id INT,
  price DECIMAL(10,2),
  stock INT DEFAULT 0,
  image VARCHAR(100),
  description TEXT
);

-- productType
CREATE TABLE IF NOT EXISTS productType (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100)
);

-- orders
CREATE TABLE IF NOT EXISTS `order` (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT,
  total DECIMAL(10,2) DEFAULT 0,
  status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- orderDetail
CREATE TABLE IF NOT EXISTS orderDetail (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  product_id INT,
  qty INT
);

-- paymentType
CREATE TABLE IF NOT EXISTS paymentType (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100)
);

-- payment
CREATE TABLE IF NOT EXISTS payment (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  payment_type_id INT,
  amount DECIMAL(10,2),
  paid_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- sample admin account (you can secure later)
CREATE TABLE IF NOT EXISTS admin_user (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE,
  password VARCHAR(255)
);

-- seed product type
INSERT IGNORE INTO productType (id,name) VALUES (1,'Handbag'),(2,'Clutch');

-- seed products
INSERT IGNORE INTO product (id,name,product_type_id,price,stock,image,description) VALUES
(1,'Classic Tote',1,49.99,10,'https://cdn.stocksnap.io/img-thumbs/960w/hiker-photo_Z9PC4ER5NO.jpg','This is product'),
(2,'Evening Clutch',2,29.99,20,'https://cdn.stocksnap.io/img-thumbs/960w/hiker-mountains_5TYDMNWU0B.jpg','Hiking is best forever');

-- seed customers
INSERT IGNORE INTO customer (id,name,email,password) VALUES
(1,'Aye','aye@example.com','dummy');

-- seed admin (password: admin123) - hashed in PHP later; for seed we insert plain and will replace
INSERT IGNORE INTO admin_user (username, password) VALUES ('admin','$2y$10$8Qw0m2s0d5bG9j6kq2Pp8OXm7YQvM6qJz6nF0zR2Wq1a6h8X9yR8e');
-- the password above is bcrypt('admin123')

-- seed some orders across months for a particular year (e.g., 2025)
INSERT INTO `order` (customer_id, total, status, created_at) VALUES
(1, 120.00, 'confirmed', '2025-01-15 10:00:00'),
(2, 85.50, 'confirmed', '2025-01-25 12:30:00'),
(1, 200.00, 'confirmed', '2025-02-10 14:00:00'),
(2, 45.00, 'cancelled', '2025-03-05 11:00:00'),
(1, 300.00, 'confirmed', '2025-03-20 09:00:00'),
(1, 150.00, 'confirmed', '2025-07-02 12:00:00'),
(2, 99.99, 'confirmed', '2025-07-15 15:00:00'),
(1, 75.00, 'pending', '2025-08-01 10:00:00');
