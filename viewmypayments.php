<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: Login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "sds");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_SESSION['email'];

// Get user's house number
$stmt = $conn->prepare("SELECT house_no FROM registration WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}

$house_no = $user['house_no'];

// Fetch payment screenshots for the house
$stmt = $conn->prepare("SELECT * FROM payment_confirmation WHERE house_no = ? ORDER BY upload_time DESC");
$stmt->bind_param("s", $house_no);
$stmt->execute();
$screenshots = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View My Payments</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        .table img {
            width: 150px;
            height: auto;
            border-radius: 0.25rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #0d6efd;
            padding: 15px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header .nav-links a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
        }
        .back a {
            margin-top: 20px;
            display: inline-block;
            text-decoration: none;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="SDS Logo.jpg" height="40" alt="Logo">
        </a>
        <div class="ms-auto">
            <a href="Userdashboard.php" class="btn btn-outline-light me-2">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="Index.php" class="btn btn-outline-light">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h3 class="mb-4"><i class="bi bi-card-image"></i> My Uploaded Payment Screenshots</h3>

    <?php if ($screenshots->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Screenshot</th>
                        <th>Upload Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $screenshots->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php
                                    $file = 'payment_uploads/' . htmlspecialchars($row['filename']);
                                    if (file_exists($file)) {
                                        echo '<img src="'.$file.'" class="img-thumbnail" alt="Screenshot">';
                                    } else {
                                        echo '<span class="text-danger">File missing</span>';
                                    }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($row['upload_time']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-circle"></i> You have not uploaded any payment screenshots yet.
        </div>
    <?php endif; ?>

    <div class="card-footer text-center">
                    <a href="Userdashboard.php" class="btn btn-outline-primary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
                </div>
</div>

<!-- Bootstrap JS (Optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php $conn->close(); ?>
