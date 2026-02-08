<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $connect = new mysqli("localhost", "root", "", "sds");

    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    $year = $_POST['year_range'];
    $opening = floatval($_POST['opening_balance']);
    $bank = floatval($_POST['bank_balance']);

    // Check if year already exists to prevent duplicates
    $check = $connect->prepare("SELECT * FROM yearlyreport WHERE year = ?");
    $check->bind_param("s", $year);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
    // Year exists, perform UPDATE
    $stmt = $connect->prepare("UPDATE yearlyreport SET opening_balance = ?, bankbalance = ? WHERE year = ?");
    $stmt->bind_param("dds", $opening, $bank, $year);
    if ($stmt->execute()) {
        $message = "<div class='login-feedback'>
                <div class='tick-box tick-success'>
                    <svg class='tick-svg' viewBox='0 0 52 52'>
                        <circle class='tick-circle' cx='26' cy='26' r='25'/>
                        <path class='tick-check' d='M14,27 L22,35 L38,19'/>
                    </svg>
                    <p class='tick-message text-success'>Budgets Updated Successfully!</p>
                </div>
                <script>
    setTimeout(() => {
        window.location.href = 'yearlyreport_add.php';
    }, 2500);
</script>
            </div>";
    } else {
        $message = "<div class='alert alert-danger'>Failed to update budget.</div>";
    }
} else {
    // New year, perform INSERT
    $stmt = $connect->prepare("INSERT INTO yearlyreport (year, opening_balance, bankbalance) VALUES (?, ?, ?)");
    $stmt->bind_param("sdd", $year, $opening, $bank);
    if ($stmt->execute()) {
        $message = "<div class='login-feedback'>
                <div class='tick-box tick-success'>
                    <svg class='tick-svg' viewBox='0 0 52 52'>
                        <circle class='tick-circle' cx='26' cy='26' r='25'/>
                        <path class='tick-check' d='M14,27 L22,35 L38,19'/>
                    </svg>
                    <p class='tick-message text-success'>Budgets Added Successfully!</p>
                </div>
                <script>
    setTimeout(() => {
        window.location.href = 'yearlyreport_add.php';
    }, 2500);
</script>
            </div>";
    } else {
        $message = "<div class='alert alert-danger'>Failed to add budget.</div>";
    }
}
    $check->close();
    $connect->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>SDS | Add Budgets</title>
    <meta charset="UTF-8">
    <link rel="icon" href="home.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
.login-feedback {
    position: fixed;
    top: 20%;
    left: 50%;
    transform: translate(-50%, -20%);
    z-index: 9999;
    text-align: center;
}

.tick-box {
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    display: inline-block;
}

.tick-svg {
    width: 80px;
    height: 80px;
}

.tick-circle {
    fill: none;
    stroke: #4CAF50;
    stroke-width: 4;
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    animation: strokeCircle 0.6s forwards;
}

.tick-check {
    fill: none;
    stroke: #4CAF50;
    stroke-width: 4;
    stroke-linecap: round;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: strokeCheck 0.3s 0.6s forwards;
}

.tick-circle.error,
.tick-check.error {
    stroke: #f44336;
}

.tick-message {
    margin-top: 15px;
    font-size: 1.2rem;
    font-weight: 600;
}

@keyframes strokeCircle {
    to {
        stroke-dashoffset: 0;
    }
}

@keyframes strokeCheck {
    to {
        stroke-dashoffset: 0;
    }
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
            <li class="nav-item"><a class="nav-link" href="adminrental.php"><i class="bi bi-house me-1"></i>Rental Details</a></li>
            <li class="nav-item"><a class="nav-link" href="yearlyreport.php"><i class="bi bi-calendar3 me-1"></i>Yearly Report</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_manage_complaints.php"><i class="bi bi-chat-dots me-1"></i>View Complaints</a></li>
            <li class="nav-item"><a class="nav-link" href="viewpayments.php"><i class="bi bi-credit-card-2-back me-1"></i>View Payments</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-box-arrow-left me-1"></i>Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <?php if (isset($message)) echo $message; ?>
    <div class="card shadow-sm p-4">
        <h2 class="mb-4"><i class="bi bi-plus-circle me-2"></i>Add Yearly Report Budgets</h2>


<form method="POST" class="row g-3 needs-validation" novalidate>
            <div class="mb-3">
                <label for="year_range" class="form-label">Financial Year</label>
                <select name="year_range" id="year_range" class="form-select" required onchange="fetchYearData(this.value)">
    <option value="">-- Select Year --</option>
    <?php
    $startYear = 2013;
    $currentYear = date('Y');
    for ($y = $startYear; $y <= $currentYear + 3; $y++) {
        $next = $y + 1;
        $range = "$y-$next";
        echo "<option value=\"$range\">$range</option>";
    }
    ?>
</select>

                <div class="invalid-feedback">
                    Please select a financial year.</div>
            </div>

            <div class="mb-3">
                <label for="opening_balance" class="form-label">Opening Balance</label>
                <input type="number" step="0.01" class="form-control" name="opening_balance" id="opening_balance" >
            </div>

            <div class="mb-3">
                <label for="bank_balance" class="form-label">Bank Balance</label>
                <input type="number" step="0.01" min="0" class="form-control" name="bank_balance" id="bank_balance" >
                
            </div>

                <div class="col-12 d-flex justify-content-between">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save</button>
            <a href="yearlyreport.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Report</a>
                </div>
        </form>
    </div>
</div>

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
<script>
function fetchYearData(year) {
    if (year === '') {
        document.getElementById('opening_balance').value = '';
        document.getElementById('bank_balance').value = '';
        return;
    }

    fetch('fetch_year_data.php?year=' + encodeURIComponent(year))
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                document.getElementById('opening_balance').value = data.opening_balance;
                document.getElementById('bank_balance').value = data.bank_balance;
            } else {
                document.getElementById('opening_balance').value = '';
                document.getElementById('bank_balance').value = '';
            }
        })
        .catch(error => console.error('Error:', error));
}
</script>

</body>
</html>
