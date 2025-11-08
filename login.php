<?php
include('db.php');
session_start();
$message = "";

// ✅ Create users table if not exists (safety)
$createTable = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    role ENUM('user','admin') NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($createTable);

// ✅ Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit;
        } else {
            $message = "<div class='alert alert-danger text-center'>Incorrect password. Please try again.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger text-center'>Username not found.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BidBack - Login</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #007bff, #6610f2);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        .login-container img {
            width: 80px;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        .btn-gradient {
            background: linear-gradient(45deg, #007bff, #6610f2);
            color: white;
            border: none;
        }
        .btn-gradient:hover {
            opacity: 0.9;
        }
        .form-control:focus {
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.4);
        }
    </style>
</head>
<body>
    <div class="login-container text-center">
        <img src="logo.png" alt="BidBack Logo">
        <h3 class="fw-bold mb-3">Welcome to BidBack</h3>
        <?= $message; ?>
        <form method="POST">
            <div class="mb-3 text-start">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="mb-3 text-start">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-gradient w-100 py-2">Login</button>
        </form>
        <p class="mt-3">Don't have an account? 
            <a href="signup.php" class="text-decoration-none fw-bold text-primary">Sign Up</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>