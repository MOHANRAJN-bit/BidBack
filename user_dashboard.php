<?php 
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch user details
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $name = htmlspecialchars($user['name']);
    $role = htmlspecialchars($user['role']);
} else {
    echo "<script>alert('User not found. Please login again.'); window.location='login.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BidBack - User Dashboard</title>
    <link rel="icon" type="image/png" href="logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #74b9ff, #a29bfe);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            padding: 20px;
        }

        .main-container {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 900px;
            overflow: hidden;
        }

        /* HEADER */
        header {
            background: #f8f9fa;
            padding: 15px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #eee;
        }

        header .brand {
            display: flex;
            align-items: center;
        }

        header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .brand-name {
            font-weight: bold;
            color: #333;
            font-size: 1.3rem;
            margin-left: 12px;
        }

        #logoutBtn {
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            font-size: 1.2rem;
            transition: background 0.3s;
        }

        #logoutBtn:hover {
            background: #c0392b;
        }

        /* MAIN CONTENT */
        .content {
            padding: 40px 25px;
            text-align: center;
        }

        .welcome-box h2 {
            font-weight: 700;
            color: #2c3e50;
        }

        .welcome-box p {
            color: #555;
        }

        .btn-square {
            width: 160px;
            height: 160px;
            margin: 15px;
            border-radius: 20px;
            font-size: 1.1rem;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
            border: none;
        }

        .btn-square i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .btn-square:hover {
            transform: translateY(-5px);
            opacity: 0.9;
        }

        /* FOOTER */
        footer {
            background: #f8f9fa;
            border-top: 2px solid #eee;
            text-align: center;
            padding: 12px;
            font-size: 0.95rem;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="main-container">

        <!-- HEADER -->
        <header>
            <div class="brand">
                <img src="logo.png" alt="BidBack Logo">
                <span class="brand-name">BidBack</span>
            </div>
            <button id="logoutBtn" title="Logout">
                <i class="bi bi-power"></i>
            </button>
        </header>

        <!-- MAIN CONTENT -->
        <div class="content">
            <div class="welcome-box mb-4">
                <h2>Welcome, <?php echo $name; ?> 👋</h2>
                <p>User Login – <strong><?php echo $username; ?></strong></p>
            </div>

            <div class="d-flex flex-wrap justify-content-center">
                <button class="btn-square" style="background:#0984e3;" onclick="window.location='report_find_item.php'">
                    <i class="bi bi-plus-circle"></i>
                    Report Find Item
                </button>

                <button class="btn-square" style="background:#00b894;" onclick="window.location='find_lost_item.php'">
                    <i class="bi bi-search"></i>
                    Find Lost Item
                </button>

                <button class="btn-square" style="background:#fdcb6e; color:#2d3436;" onclick="window.location='bidding.php'">
                    <i class="bi bi-cash-coin"></i>
                    Bidding
                </button>

                <button class="btn-square" style="background:#6c5ce7;" onclick="window.location='reports.php'">
                    <i class="bi bi-file-earmark-text"></i>
                    Reports
                </button>
            </div>
        </div>

        <!-- FOOTER -->
        <footer>
            © <?php echo date("Y"); ?> BidBack — Smart Lost & Found with Bidding System
        </footer>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("logoutBtn").addEventListener("click", function() {
            if (confirm("Are you sure you want to logout?")) {
                window.location = "logout.php";
            }
        });
    </script>
</body>
</html>