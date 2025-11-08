<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Create table if not exists
$createTable = "
CREATE TABLE IF NOT EXISTS found_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(50) NOT NULL,
    found_place VARCHAR(100) NOT NULL,
    found_date DATE NOT NULL,
    found_time VARCHAR(20) NOT NULL,
    photo VARCHAR(255) DEFAULT 'logo.png',
    approval_status ENUM('approved','pending','rejected') DEFAULT 'pending',
    approval_remark VARCHAR(255) DEFAULT 'Waiting for admin review',
    status ENUM('waiting approval','waiting for claim','bidding','claimed') DEFAULT 'waiting approval',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($createTable);

$message = "";

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item_name = $_POST['item_name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $found_place = $_POST['found_place'];
    $found_date = $_POST['found_date'];
    $found_time = $_POST['found_time'];

    $photo_name = "logo.png";

    // Handle file upload
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES["photo"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            $photo_name = $fileName;
        }
    }

    $sql = "INSERT INTO found_items (username, item_name, description, category, found_place, found_date, found_time, photo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $username, $item_name, $description, $category, $found_place, $found_date, $found_time, $photo_name);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success mt-3'>Item reported successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger mt-3'>Error reporting item. Try again.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Found Item | BidBack</title>
    <link rel="icon" type="image/png" href="logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #81ecec, #74b9ff);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Poppins', sans-serif;
        }
        header {
            background: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        header img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
        }
        .container {
            flex: 1;
            background: #fff;
            margin-top: 30px;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            max-width: 700px;
        }
        footer {
            background: rgba(0,0,0,0.3);
            color: white;
            text-align: center;
            padding: 10px;
        }
        #photoPreview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            display: none;
            margin-top: 10px;
        }
        .btn-back {
            background: #636e72;
            color: white;
        }
        .btn-back:hover {
            background: #2d3436;
        }
    </style>
</head>
<body>

<!-- HEADER -->
<header>
    <div class="d-flex align-items-center">
        <img src="logo.png" alt="BidBack Logo">
        <h5 class="ms-2 mt-2 fw-bold text-dark">BidBack</h5>
    </div>
    <button class="btn btn-back btn-sm" onclick="window.location='user_dashboard.php'">
        <i class="bi bi-arrow-left"></i> Back
    </button>
</header>

<!-- BODY -->
<div class="container">
    <h3 class="text-center mb-4 fw-bold text-primary">Report Found Item</h3>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Item Name</label>
            <input type="text" name="item_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select" required>
                <option value="">-- Select Category --</option>
                <option>Stationary</option>
                <option>Wood</option>
                <option>Iron</option>
                <option>Steel</option>
                <option>Cloth</option>
                <option>Bag</option>
                <option>Electronics</option>
                <option>Jewellery</option>
                <option>Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Found Place</label>
            <input type="text" name="found_place" class="form-control" required>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Found Date</label>
                <input type="date" name="found_date" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Found Time</label>
                <input type="time" name="found_time" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Upload Photo (Optional)</label>
            <input type="file" name="photo" class="form-control" accept="image/*" onchange="previewImage(event)">
            <img id="photoPreview" alt="Preview">
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success px-5">
                <i class="bi bi-upload"></i> Submit
            </button>
        </div>
        <?php echo $message; ?>
    </form>
</div>

<!-- FOOTER -->
<footer>
    © <?php echo date("Y"); ?> BidBack — Smart Lost & Found with Bidding System
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function previewImage(event) {
    const preview = document.getElementById('photoPreview');
    preview.src = URL.createObjectURL(event.target.files[0]);
    preview.style.display = 'block';
}
</script>
</body>
</html>
