<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: Login.php");
    exit();
}

$email = $_SESSION['email'];
$conn = new mysqli("localhost", "root", "", "sds");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT house_no FROM registration WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User data not found.");
}

$house_no = $user['house_no'];

$sql = "SELECT * FROM complaints WHERE house_no = ? ORDER BY date_time DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $house_no);
$stmt->execute();
$complaints = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>SDS | My Complaints History</title>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="home.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
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

<!-- Main Content -->
<div class="container mt-5">
    <h2 class="mb-4 text-primary">📋 My Complaint History</h2>

    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>Sr. No</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date Submitted</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($complaints->num_rows > 0): ?>
                    <?php while ($row = $complaints->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['subject']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                            <td><span class="badge bg-<?= $row['status'] == 'Resolved' ? 'success' : 'warning' ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                            <td><?= $row['date_time'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No complaints submitted yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="card-footer text-center">
        <a href="complain.php" class="btn btn-outline-primary"><i class="bi bi-arrow-left"></i> Back to New Complaints</a>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php $conn->close(); ?>
