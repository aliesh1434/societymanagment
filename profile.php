<?php
session_start();

// Check login
if (!isset($_SESSION['email'])) {
    header("Location: Login.php");
    exit();
}

$email = $_SESSION['email'];

// Connect to DB
$connect = mysqli_connect("localhost", "root", "", "sds");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch user data
$query = "SELECT  house_no, role, mob, password FROM registration WHERE email = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$house_no = $user['house_no'] ?? 'N/A';
$mobile = $user['mob'] ?? 'N/A';
$role = $user['role'] ?? 'N/A';
$password = $user['password'] ?? 'N/A';

// Fetch house type
$housetype_query = "SELECT house_details,name FROM house_plot WHERE house_no = ?";
$stmt = $connect->prepare($housetype_query); 
$stmt->bind_param("s", $house_no);
$stmt->execute();
$result = $stmt->get_result();
$houseType = $result->fetch_assoc();
$stmt->close();

$housetype = $houseType['house_details'] ?? 'N/A';
$name = $houseType['name'] ?? 'N/A';

// Get monthly maintenance payment for current user (grouped by month)
$data = [];
for ($m = 1; $m <= 12; $m++) {
    $month = str_pad($m, 2, "0", STR_PAD_LEFT);
    $year = date("Y");

    $sql = "SELECT SUM(ammount) AS total FROM maintenance WHERE house_no = ? AND MONTH(m_date) = ? AND YEAR(m_date) = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("sii", $house_no, $m, $year);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $data[] = (int)($res['total'] ?? 0);
    $stmt->close();
}

mysqli_close($connect);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile - SDS</title>
    <link rel="icon" type="image/png" href="home.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="SDS Logo.png" height="40" alt="Logo">
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

<!-- Profile Container -->
<div class="container mt-5 pt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white text-center">
                    <h4><i class="bi bi-person-circle"></i> Your Profile</h4>
                </div>
                <div class="card-body">
                    <p><strong><i class="bi bi-person-fill"></i> Name:</strong> <?= htmlspecialchars($name); ?></p>
                    <p><strong><i class="bi bi-house-door-fill"></i> House No:</strong> <?= htmlspecialchars($house_no); ?></p> 
                    <p><strong><i class="bi bi-telephone-fill"></i> Mobile:</strong> <?= htmlspecialchars($mobile); ?></p>
                    <p><strong><i class="bi bi-geo-alt-fill"></i>House Type:</strong> <?= htmlspecialchars($housetype); ?></p>
                    <p><strong><i class="bi bi-person-badge-fill"></i> Role:</strong> <?= htmlspecialchars($role); ?></p><br>
                    <p><strong><i class="bi bi-person-badge"></i> Login Credentials:</strong></p>

                    <p><strong><i class="bi bi-envelope-fill"></i> Email:</strong> <?= htmlspecialchars($email); ?></p>
                    <p><strong><i class="bi bi-lock-fill"></i> Password:</strong> <?= htmlspecialchars($password); ?></p>
                </div>
                <div class="card-footer text-center">
                    <a href="Userdashboard.php" class="btn btn-outline-primary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.js"></script>

</body>
</html>
