<?php
session_start();
include 'conn.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    if (
        !empty($_POST['reg_name']) &&
        !empty($_POST['reg_password'])
    ) {
        $name = $_POST['reg_name'];
        $password = password_hash($_POST['reg_password'], PASSWORD_DEFAULT);
        $role = 'user';
        $stmt = $conn->prepare("SELECT id FROM users WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            echo "<script>alert('Name already exists.');</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $password, $role);
            if ($stmt->execute()) {
                echo "<script>alert('Registered successfully. Please login.');</script>";
            } else {
                echo "<script>alert('Registration failed.');</script>";
            }
        }
    } else {
        echo "<script>alert('All fields are required.');</script>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $name = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $row['name'];
                $_SESSION['role'] = $row['role'] ?? '';
                header("Location: dashboard.php");
                exit();
            } else {
                echo "<script>alert('Name or password is incorrect. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Name or password is incorrect. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Please enter both name and password.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Student Information System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #232526 0%, #414345 100%);
      font-family: 'Inter', sans-serif;
      position: relative;
      overflow: hidden;
    }
    .glass-card {
      background: rgba(255,255,255,0.13);
      border-radius: 18px;
      box-shadow: 0 8px 32px 0 rgba(31,38,135,0.37);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255,255,255,0.18);
      padding: 40px 32px 32px 32px;
      width: 350px;
      color: #fff;
      text-align: center;
      position: relative;
      z-index: 2;
    }
    .glass-card h5 {
      font-weight: 700;
      margin-bottom: 18px;
      letter-spacing: 1px;
    }
    .form-label {
      float: left;
      margin-bottom: 6px;
      font-weight: 500;
      color: #f8f9fa;
    }
    .form-control {
      background: rgba(255,255,255,0.18);
      border: none;
      border-radius: 8px;
      color: #fff;
      margin-bottom: 18px;
      padding: 12px;
      font-size: 1rem;
      transition: background 0.2s;
    }
    .form-control:focus {
      background: rgba(255,255,255,0.28);
      color: #232526;
      box-shadow: 0 0 0 2px  #2733a2;
    }
    #loginButton {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 8px;
      background: linear-gradient(90deg,  #2733a2 0%, #777eb4 100%);
      color: #fff;
      font-weight: bold;
      font-size: 1.1rem;
      letter-spacing: 1px;
      box-shadow: 0 4px 12px rgba(220,53,69,0.15);
      transition: background 0.3s, transform 0.2s;
    }
    #loginButton:hover {
      background: linear-gradient(90deg,  #2733a2 0%, #777eb4 100%);
      transform: translateY(-2px) scale(1.03);
    }
    .login-logo {
      width: 60px;
      height: 60px;
      margin-bottom: 16px;
      border-radius: 50%;
      background: linear-gradient(90deg,  #2733a2 0%, #777eb4 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      color: #fff;
      box-shadow: 0 2px 8px rgba(220,53,69,0.18);
      margin-left: auto;
      margin-right: auto;
    }
    .footer-text {
      margin-top: 18px;
      font-size: 0.95rem;
      color: #e0e0e0;
      opacity: 0.7;
    }
    .bg-circle {
      position: absolute;
      border-radius: 50%;
      filter: blur(60px);
      opacity: 0.5;
      z-index: 1;
    }
    .bg-circle.one {
      width: 320px; height: 320px;
      background: #2733a2;
      top: -80px; left: -100px;
    }
    .bg-circle.two {
      width: 220px; height: 220px;
      background: #2733a2;
      bottom: -60px; right: -60px;
    }
  </style>
</head>
<body>
  <div class="bg-circle one"></div>
  <div class="bg-circle two"></div>
  <div class="glass-card">
    <div class="login-logo">
      <span>🎓</span>
    </div>
    <h5>Monitoring System</h5>
    <form method="post" action="">
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="username" class="form-control" required autocomplete="username">
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required autocomplete="current-password">
      </div>
      <button type="submit" id="loginButton" name="login" class="btn">Login</button>
    </form>
    <div class="footer-text">
      New here? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" style="color:#ffd700;text-decoration:underline;">Register Here</a>
      <br>
      &copy; <?php echo date('Y'); ?> Monitoring System
    </div>
  </div>

  <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content" style="background: #232526; color: #fff; border-radius: 16px; border: 2px solid #2733a2;">
        <form method="post" action="">
          <div class="modal-header" style="border-bottom: 1px solid #2733a2;">
            <h5 class="modal-title" id="registerModalLabel">Register</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Name</label>
              <input type="text" name="reg_name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="reg_password" class="form-control" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" name="register" class="btn btn-primary w-100" style="background:#2733a2;border:none;">Register</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>