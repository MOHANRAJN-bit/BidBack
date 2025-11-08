<?php
include('db.php');
$message = "";

// ✅ Create users table if not exists
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

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $secret_code = isset($_POST['secret_code']) ? $_POST['secret_code'] : '';

    // Check if username already exists
    $checkUser = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $checkUser->bind_param("s", $username);
    $checkUser->execute();
    $result = $checkUser->get_result();

    if ($result->num_rows > 0) {
        $message = "<div class='alert alert-danger text-center'>Username already exists. Please choose another.</div>";
    } else {
        // If admin, check secret code
        if ($role == "admin" && $secret_code != "2252") {
            $message = "<div class='alert alert-danger text-center'>Invalid Secret Code for Admin Signup!</div>";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, username, phone, role, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $username, $phone, $role, $password);
            if ($stmt->execute()) {
                if ($role == "admin") {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: user_dashboard.php");
                }
                exit;
            } else {
                $message = "<div class='alert alert-danger text-center'>Error: " . $conn->error . "</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BidBack - Sign Up</title>
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
        .signup-container {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 420px;
        }
        .signup-container img {
            width: 80px;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        .form-control:focus {
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.4);
        }
        .modal-content {
            border-radius: 15px;
        }
        .btn-gradient {
            background: linear-gradient(45deg, #007bff, #6610f2);
            color: white;
            border: none;
        }
        .btn-gradient:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="signup-container text-center">
        <img src="logo.png" alt="BidBack Logo">
        <h3 class="fw-bold mb-3">BidBack - Sign Up</h3>
        <?= $message; ?>
        <form method="POST">
            <div class="mb-3 text-start">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="mb-3 text-start">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="mb-3 text-start">
                <label class="form-label">Phone Number</label>
                <input type="tel" class="form-control" name="phone" pattern="[0-9]{10}" required>
            </div>
            <div class="mb-3 text-start">
                <label class="form-label">Role</label>
                <select class="form-select" name="role" id="roleSelect" required>
                    <option value="" disabled selected>Select Role</option>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="mb-3 text-start">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <input type="hidden" name="secret_code" id="secret_code_input">

            <button type="submit" class="btn btn-gradient w-100 py-2">Sign Up</button>
        </form>

        <p class="mt-3">Already have an account? <a href="login.php" class="text-decoration-none fw-bold text-primary">Login</a></p>
    </div>

    <!-- Modal for Secret Code -->
    <div class="modal fade" id="secretModal" tabindex="-1" aria-labelledby="secretModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enter Admin Secret Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="password" id="secretCode" class="form-control" placeholder="Enter secret code">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-gradient" id="confirmCode">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const roleSelect = document.getElementById('roleSelect');
        const secretModal = new bootstrap.Modal(document.getElementById('secretModal'));
        const secretCodeInput = document.getElementById('secret_code_input');

        roleSelect.addEventListener('change', function() {
            if (this.value === 'admin') {
                secretModal.show();
            } else {
                secretCodeInput.value = '';
            }
        });

        document.getElementById('confirmCode').addEventListener('click', function() {
            const code = document.getElementById('secretCode').value.trim();
            secretCodeInput.value = code;
            secretModal.hide();
        });
    </script>
</body>
</html>
