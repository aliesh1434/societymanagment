<?php
$message = '';
$alertType = 'info';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $connect = mysqli_connect("localhost", "root", "", "sds");

    if (!$connect) {
        $message = "Connection failed: " . mysqli_connect_error();
        $alertType = "danger";
    } else {
        $name = mysqli_real_escape_string($connect, $_POST['name']);
        $house_no = mysqli_real_escape_string($connect, $_POST['house_no']);
        $ammount = mysqli_real_escape_string($connect, $_POST['ammount']);
        $festival = mysqli_real_escape_string($connect, $_POST['festival']);
        $year = mysqli_real_escape_string($connect, $_POST['year']);

        if (!empty($name) && $house_no != "None" && is_numeric($ammount) && $festival != "None") {
            $query = "INSERT INTO religious (name, house_no, ammount, festival, year) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connect, $query);
            mysqli_stmt_bind_param($stmt, "sssss", $name, $house_no, $ammount, $festival, $year);

            if (mysqli_stmt_execute($stmt)) {
                $message = "<div class='login-feedback'>
    <div class='tick-box tick-success'>
        <svg class='tick-svg' viewBox='0 0 52 52'>
            <circle class='tick-circle' cx='26' cy='26' r='25'/>
            <path class='tick-check' d='M14,27 L22,35 L38,19'/>
        </svg>
        <p class='tick-message text-success'>Religious Fund Added Successfully!</p>
    </div>
    <script>
        setTimeout(() => {
            window.location.href = 'Religious.php';
        }, 1500);
    </script>
</div>";
                $alertType = "success";
            } else {
                $redirectPage = 'Religious.php';
            $loginMessage = "
            <div class='login-feedback'>
                <div class='tick-box tick-error'>
                    <svg class='tick-svg' viewBox='0 0 52 52'>
                        <circle class='tick-circle error' cx='26' cy='26' r='25' fill='none'/>
                        <path class='tick-check error' fill='none' d='M16,16 L36,36 M36,16 L16,36'/>
                    </svg>
                    <p class='tick-message text-danger'>Please enter proper details</p>
                </div>
                <script>
                    setTimeout(() => {
                        window.location.href = 'Religious.php';
                    }, 1500);
                </script>
            </div>";
            }

            mysqli_stmt_close($stmt);
        } else {
            $message = "Invalid input. Please fill in all fields correctly.";
            $alertType = "warning";
        }

        mysqli_close($connect);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>SDS | Religious Fund</title>
    <meta charset="UTF-8">
    <link rel="icon" href="home.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .form-container {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
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
            <li class="nav-item"><a class="nav-link" href="religiousexpense.php"><i class="bi bi-flower1 me-1"></i>Religious Expenses</a></li>
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
        <div class="container text-center mt-3">
             <h3 class="text-center mb-4"><i class="bi bi-flower1 me-2"></i><u>Religious Fund Entry</u></h3>
        </div>


    <?php if (!empty($message)) : ?>
        <div class="alert alert-<?= $alertType ?> alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

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
        <label class="form-label"><i class="bi bi-house-door me-1"></i> House No.:</label>
        <select id="house_no" name="house_no" class="form-select" required>
            <option value="">-- Select House No --</option>
            <option value="None">None</option>
            <option value="Soc">Society</option>
            <?php for ($i = 1; $i <= 47; $i++) echo "<option value=\"$i\">$i</option>"; ?>
        </select>
        <div class="invalid-feedback">Please select a house number.</div>
    </div>

    <div class="mb-3">
        <label for="name" class="form-label"><i class="bi bi-person-circle me-1"></i> Name:</label>
        <input type="text" id="name" name="name" class="form-control" placeholder="Enter Name" required>
        <div class="invalid-feedback">Please enter name.</div>
    </div>

    <div class="mb-3">
        <label class="form-label"><i class="bi bi-currency-rupee me-1"></i> Amount</label>
        <input type="text" name="ammount" class="form-control"  placeholder="Enter Amount" required>
        <div class="invalid-feedback">Please enter a valid amount</div>
    </div>

    <div class="mb-3">
        <label class="form-label"><i class="bi bi-calendar3 me-1"></i> Year</label>
        <select name="year" class="form-select" required>
            <option value="">None</option>
            <?php
            $currentYear = date("Y");
            for ($y = 2013; $y <= $currentYear + 3; $y++) echo "<option value=\"$y\">$y</option>";
            ?>
        </select>
        <div class="invalid-feedback">Please select a year.</div>
    </div>

    <div class="col-12 d-flex justify-content-between">
        <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle me-1"></i> Submit
        </button>
        <a href="Showdatareligious.php" class="btn btn-info">
            <i class="bi bi-table me-1"></i> Show Data
        </a>
    </div>
        </div>
</form>


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
