<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "sds");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch payment confirmations with user email
$sql = "
    SELECT pc.house_no, pc.filename, pc.upload_time, r.email 
    FROM payment_confirmation pc
    LEFT JOIN registration r ON pc.house_no = r.house_no
    ORDER BY pc.upload_time DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SDS | Payment Screenshots</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="home.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        img {
            max-width: 150px;
            height: auto;
            border-radius: 5px;
        }
  body {
    padding-top: 90px; /* Extra space for desktop fixed navbar */
  }

  @media (max-width: 768px) {
    body {
      padding-top: 100px; /* Extra space for mobile expanded navbar */
    }
  }
        .navbar-brand img {
            height: 40px;
        }
          .table-responsive {
    max-height: 400px;
    overflow-y: auto;
}

.table thead th {
    position: sticky;
    top: 0;
    background-color: #212529; /* Bootstrap dark bg */
    color: white;
    z-index: 1;
}
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 fixed-top">
    <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="SDS Logo.jpg" alt="Society Dashboard" height="40" class="me-2">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button> 
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav ms-auto d-flex gap-2">
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="maintenance.php"><i class="bi bi-tools me-1"></i>Maintenance</a></li>
            <li class="nav-item"><a class="nav-link" href="Religious.php"><i class="bi bi-flower1 me-1"></i>Religious Fund</a></li>
            <li class="nav-item"><a class="nav-link" href="balance.php"><i class="bi bi-cash-stack me-1"></i>Balance</a></li>
            <li class="nav-item"><a class="nav-link" href="Receipt.php"><i class="bi bi-receipt-cutoff me-1"></i>Receipts</a></li>
            <li class="nav-item"><a class="nav-link" href="Bankdebit.php"><i class="bi bi-bank me-1"></i>Bank</a></li>
            <li class="nav-item"><a class="nav-link" href="housedetails.php"><i class="bi bi-building me-1"></i>House Details</a></li>
            <li class="nav-item"><a class="nav-link" href="yearlyreport.php"><i class="bi bi-calendar3 me-1"></i>Yearly Report</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_manage_complaints.php"><i class="bi bi-chat-dots me-1"></i>View Complaints</a></li>
            <li class="nav-item"><a class="nav-link" href="viewpayments.php"><i class="bi bi-credit-card-2-back me-1"></i>View Payments</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-box-arrow-left me-1"></i>Logout</a></li>
        </ul>
    </div>
</nav>

<!-- Main Content -->
<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <h3 class="card-title text-center mb-4"><i class="bi bi-credit-card-2-back-fill"></i> Uploaded Payment Screenshots</h3>
            <p class="text-center text-muted mb-4">Below are the payment confirmation screenshots uploaded by users.</p>
        <div class="table-responsive">
               <table class="table table-bordered table-striped table-hover mt-3">
                    <thead class="table-dark">
                    <tr>
                        <th><i class="bi bi-house-door-fill"></i> House No</th>
                        <th><i class="bi bi-envelope"></i> User Email</th>
                        <th><i class="bi bi-file-image"></i> Screenshot</th>
                        <th><i class="bi bi-clock-history"></i> Upload Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['house_no']) ?></td>
                                <td><?= htmlspecialchars($row['email'] ?? 'Not Found') ?></td>
                                <td>
                                    <?php
                                        $file = 'payment_uploads/' . htmlspecialchars($row['filename']);
                                        if (file_exists($file)) {
                                            echo '<a href="'.$file.'" target="_blank"><img src="'.$file.'" alt="Screenshot"></a>';
                                        } else {
                                            echo '<span class="text-danger">File not found</span>';
                                        }
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($row['upload_time']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No payment uploads found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
