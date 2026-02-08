<?php
// Establish database connection
$connect = mysqli_connect("localhost", "root", "", "sds");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch notices from the database
$notices = mysqli_query($connect, "SELECT * FROM notices ORDER BY date_posted DESC");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SDS | Notice Board</title>
    <link rel="icon" type="image/png" href="home.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>


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

<!-- Notices Section -->
<div class="container mt-3">
    <?php while ($row = mysqli_fetch_assoc($notices)) { ?>
        <div class="card mb-4 shadow-sm">
            <h3 class="my-3 text-danger"><u>Notice Board</u></h3>
            <div class="card-body">
                <h5 class="card-title"><b><?= htmlspecialchars($row['title']); ?></b></h5>
                <p class="card-text"><?= nl2br(htmlspecialchars($row['content'])); ?></p>
                <p class="text-muted small mb-0">Posted on: <?= htmlspecialchars($row['date_posted']); ?></p>
            </div>
        </div>
    <?php } ?>

    <div class="card-footer text-center">
        <a href="Userdashboard.php" class="btn btn-outline-primary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
