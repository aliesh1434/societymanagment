<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connect = mysqli_connect("localhost", "root", "", "sds");

    if (!$connect) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    $t_detail = isset($_POST['t_detail']) ? trim($_POST['t_detail']) : '';
    $ammount = isset($_POST['ammount']) ? trim($_POST['ammount']) : '';
    $year = isset($_POST['year']) ? trim($_POST['year']) : '';

    $errors = [];
    if (empty($t_detail)) {
        $errors[] = "Transaction details are required.";
    }

    if (empty($errors)) {
        $query = "INSERT INTO maintenancetransactions (t_detail, ammount, year) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "sss", $t_detail, $ammount, $year);

        if (mysqli_stmt_execute($stmt)) {
            echo "<div class='login-feedback'>
    <div class='tick-box tick-success'>
        <svg class='tick-svg' viewBox='0 0 52 52'>
            <circle class='tick-circle' cx='26' cy='26' r='25'/>
            <path class='tick-check' d='M14,27 L22,35 L38,19'/>
        </svg>
        <p class='tick-message text-success'>Maintenance Expense Added Successfully!</p>
    </div>
    <script>
        setTimeout(() => {
            window.location.href = 'maintenanceexpenses.php';
        }, 2500);
    </script>
</div>";
        } else {
            echo "<script>alert('Error adding data: " . mysqli_error($connect) . "');</script>";
        }

        mysqli_stmt_close($stmt);
    } else {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    }

    mysqli_close($connect);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SDS | Maintenance Expenses</title>
    <link rel="icon" type="image/png" href="home.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS and Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">  
<style>
  body {
    padding-top: 90px; /* Extra space for desktop fixed navbar */
  }

  @media (max-width: 768px) {
    body {
      padding-top: 100px; /* Extra space for mobile expanded navbar */
    }
  }
</style>

</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 fixed-top">
    <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
        <img src="SDS Logo.png" alt="Society Dashboard" height="40" class="me-2">
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
            <li class="nav-item"><a class="nav-link" href="adminrental.php"><i class="bi bi-house me-1"></i>Rental Details</a></li>
            <li class="nav-item"><a class="nav-link" href="yearlyreport.php"><i class="bi bi-calendar3 me-1"></i>Yearly Report</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_manage_complaints.php"><i class="bi bi-chat-dots me-1"></i>View Complaints</a></li>
            <li class="nav-item"><a class="nav-link" href="viewpayments.php"><i class="bi bi-credit-card-2-back me-1"></i>View Payments</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-box-arrow-left me-1"></i>Logout</a></li>
        </ul>
    </div>
</nav>

<!-- Main Content -->
 <div class="container my-4">
    <div class="card shadow-sm p-4">
    <h3 class="text-center mb-4"><i class="bi bi-tools"></i> Maintenance Expenses</h3>
<!-- Inside your <form> -->
<form method="POST" action="" class="needs-validation" novalidate>
    <div class="mb-3">
        <label for="t_detail" class="form-label">
            <i class="bi bi-card-text"></i> Transaction Details
        </label>
        <input type="text" class="form-control" id="t_detail" name="t_detail" placeholder="Enter transaction details" required>
        <div class="invalid-feedback">Please enter transaction details.</div>
    </div>

    <div class="mb-3">
        <label for="ammount" class="form-label">
            <i class="bi bi-currency-rupee"></i> Transaction Amount
        </label>
        <input type="number" class="form-control" id="ammount" name="ammount" step="0.01" min="0.01" placeholder="Enter amount" required>
        <div class="invalid-feedback">Please enter a valid amount (greater than 0).</div>
    </div>

    <div class="mb-3">
        <label for="year" class="form-label">
            <i class="bi bi-calendar2-range"></i> Year
        </label>
        <input type="text" class="form-control" id="year" name="year" placeholder="e.g., 2013-2014" pattern="^\d{4}(-\d{4})?$" required>
        <div class="invalid-feedback">Enter a valid year (e.g., 2013-2014).</div>
    </div>

    <div class="col-12 d-flex justify-content-between">
        <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle me-1"></i> Submit
        </button>
        <a href="Showdatamaintenanceexpenses.php" class="btn btn-info">
            <i class="bi bi-table me-1"></i> Show Data
        </a>
    </div>
</form>

</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Bootstrap 5 client-side validation
(() => {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>

</body>
</html>
