<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$message = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $db = new Database();
    
    $conn = $db->getConnection(); 

    // Check if email already exists
   
    $stmt =$conn->prepare("SELECT id,password FROM admin_user WHERE username=?");
    $stmt->bind_param("s",$username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password,$admin['password'])) {
        $_SESSION['admin_id']=$admin['id'];
        $_SESSION['admin_user']=$username;
        header("Location: dashboard.php");
        exit;
    } else {
        $message="Invalid credentials";
    }
    $stmt->close();
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow">
        <div class="card-body">
          <h3 class="card-title mb-3">Admin Login</h3>
          <?php if($message): ?>
            <div class="alert alert-danger"><?=$message?></div>
          <?php endif; ?>
          <form method="post">
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input class="form-control" name="username" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" class="form-control" name="password" required>
            </div>
            <button class="btn btn-primary w-100">Login</button>
          </form>
        </div>
      </div>
      <p class="text-muted small mt-2">Default login: admin / admin123</p>
    </div>
  </div>
</div>
</body>
</html>
