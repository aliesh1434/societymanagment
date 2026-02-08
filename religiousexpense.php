<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $connect = mysqli_connect("localhost", "root", "", "sds");

    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $t_detail = mysqli_real_escape_string($connect, $_POST['t_detail']);
    $ammount = filter_var($_POST['ammount'], FILTER_VALIDATE_FLOAT);
    $festival = mysqli_real_escape_string($connect, $_POST['festival']);
    $year = mysqli_real_escape_string($connect, $_POST['year']);

    if (!empty($t_detail) && is_numeric($ammount) && $festival != "None" && !empty($year)) {
        $query = "INSERT INTO transactions (t_detail, ammount, festival, year) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "sdss", $t_detail, $ammount, $festival, $year);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Data added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding data.');</script>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Invalid input. Please fill in all fields correctly.');</script>";
    }
    mysqli_close($connect);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>SDS | Religious Fund Expenses</title>
    <link rel="icon" type="image/png" href="home.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
<style>
        
        .navbar-brand img {
            height: 40px;
        }
        .nav-link:hover {
            color: #ffc107 !important;
            font-weight: bold;
        }
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

<!-- Main Form -->
<div class="container my-4">
    <div class="card shadow-sm p-4">
        <div class="container mt-3">
    <h4 class="text-center mb-4"><i class="bi bi-flower1 me-2"></i><u>Religious Fund Expenses</u></h4>
    <form method="POST" class="row g-3 needs-validation" novalidate>
    <div class="mb-3">
        <label for="festival" class="form-label">
            <i class="bi bi-stars me-2"></i>Festival
        </label>
        <select id="festival" name="festival" class="form-select" required>
            <option value="">-- Select Festival --</option>
            <option value="Holi">Holi</option>
            <option value="Janmashtami">Janmashtami</option>
            <option value="Ganeshotsav">Ganeshotsav</option>
            <option value="Navratri">Navratri</option>
            <option value="Shanti Havan">Shanti Havan</option>
        </select>
        <div class="invalid-feedback">Please select a festival.</div>
    </div>

        <div class="mb-3">
            <label class="form-label"><i class="bi bi-file-text me-2"></i>Expense Details</label>
            <input type="text" class="form-control" id="t_detail" name="t_detail" placeholder="Enter details" required>
            <div class="invalid-feedback">Please enter transaction details.</div>
        </div>

        <div class="mb-3">
            <label for="ammount" class="form-label"><i class="bi bi-currency-rupee me-2"></i>Expense Amount</label>
            <input type="text" class="form-control" id="ammount" name="ammount" placeholder="Enter amount" required>
            <div class="invalid-feedback">Please enter a valid amount.</div>
        </div>

        <div class="mb-3">
            <label for="year" class="form-label"><i class="bi bi-calendar-range me-2"></i>Year</label>
            <input type="text" class="form-control" id="year" name="year" placeholder="e.g., 2023" pattern="^\d{4}$" required>
            <div class="invalid-feedback">Please enter a valid year (4 digits).</div>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i>Submit</button>
            <a href="Showreligiousdataexpenses.php" class="btn btn-primary px-4"><i class="bi bi-eye me-1"></i>Show Data</a>
        </div>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
