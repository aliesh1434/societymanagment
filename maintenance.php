<?php
$connect = mysqli_connect("localhost", "root", "", "sds");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get last receipt number
$lastrow_query = "SELECT receipt_no FROM maintenance ORDER BY CAST(SUBSTRING_INDEX(receipt_no, '/', -1) AS UNSIGNED) DESC LIMIT 1";
$result = mysqli_query($connect, $lastrow_query);
$lastrow = mysqli_fetch_assoc($result);
$last_receipt_no = isset($lastrow['receipt_no']) ? $lastrow['receipt_no'] : 'N/A';
mysqli_close($connect);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $connect = mysqli_connect("localhost", "root", "", "sds");

    $house_no = mysqli_real_escape_string($connect, $_POST['house_no']);
    $ammount = mysqli_real_escape_string($connect, $_POST['ammount']);
    $receipt_no = mysqli_real_escape_string($connect, $_POST['receipt_no']);
    $ammounttype = mysqli_real_escape_string($connect, $_POST['ammounttype']);
    $paymenttype = mysqli_real_escape_string($connect, $_POST['paymenttype']);
    $month = mysqli_real_escape_string($connect, $_POST['month']);
    $year = mysqli_real_escape_string($connect, $_POST['year']);
    $m_date = mysqli_real_escape_string($connect, $_POST['m_date']);
    $rebate = isset($_POST['rebate']);

    $redirectPage = "maintenance.php";

    if (!empty($house_no) && $house_no != "None" && is_numeric($ammount) && $receipt_no != "None") {
        $monthly_charge = 500;

        // Determine monthly charge based on house type
        $type_query = "SELECT house_details FROM house_plot WHERE house_no = ?";
        $type_stmt = mysqli_prepare($connect, $type_query);
        mysqli_stmt_bind_param($type_stmt, "s", $house_no);
        mysqli_stmt_execute($type_stmt);
        $type_result = mysqli_stmt_get_result($type_stmt);
        $type_row = mysqli_fetch_assoc($type_result);
        $house_details = isset($type_row['house_details']) ? strtolower($type_row['house_details']) : 'house';
        mysqli_stmt_close($type_stmt);

        if ($house_details === 'plot') {
            $monthly_charge = 250;
        }

        $success = true;
        $months = ["January", "February", "March", "April", "May", "June",
                   "July", "August", "September", "October", "November", "December"];
        $current_month_index = array_search($month, $months);
        $current_year = intval($year);

        // Handle Maintenance
        if ($paymenttype === "Maintenance") {
            $months_to_add = floor($ammount / $monthly_charge);
            for ($i = 0; $i < $months_to_add; $i++) {
                $new_month_index = ($current_month_index + $i) % 12;
                $new_month = $months[$new_month_index];
                $new_year = $current_year + floor(($current_month_index + $i) / 12);

                $query = "INSERT INTO maintenance (house_no, ammount, receipt_no, month, year, m_date, ammounttype, paymenttype)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($connect, $query);
                mysqli_stmt_bind_param($stmt, "ssssssss", $house_no, $monthly_charge, $receipt_no, $new_month, $new_year, $m_date, $ammounttype, $paymenttype);

                if (!mysqli_stmt_execute($stmt)) {
                    $success = false;
                    echo "<script>alert('Error inserting for $new_month $new_year: " . mysqli_error($connect) . "');</script>";
                    break;
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            // Handle Development / Parking / etc.
            $query = "INSERT INTO maintenance (house_no, ammount, receipt_no, month, year, m_date, ammounttype, paymenttype)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connect, $query);
            mysqli_stmt_bind_param($stmt, "ssssssss", $house_no, $ammount, $receipt_no, $month, $year, $m_date, $ammounttype, $paymenttype);

            if (!mysqli_stmt_execute($stmt)) {
                $success = false;
                echo "<div class='login-feedback'>
                    <div class='tick-box tick-error'>
                        <svg class='tick-svg' viewBox='0 0 52 52'>
                            <circle class='tick-circle error' cx='26' cy='26' r='25'/>
                            <path class='tick-check error' d='M16,16 L36,36 M36,16 L16,36'/>
                        </svg>
                        <p class='tick-message text-danger'>Error Inserting Record</p>
                    </div>
                    <script>
                        setTimeout(() => {
                            window.location.href = '$redirectPage';
                        }, 3000);
                    </script>
                </div>";
            }
            mysqli_stmt_close($stmt);
        }

        // Optional rebate
        if ($rebate && $success) {
            $rebate_amount = $monthly_charge;
            $rebate_paymenttype = "Rebate";
            $query = "INSERT INTO maintenance (house_no, ammount, receipt_no, month, year, m_date, ammounttype, paymenttype)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connect, $query);
            mysqli_stmt_bind_param($stmt, "ssssssss", $house_no, $rebate_amount, $receipt_no, $month, $year, $m_date, $ammounttype, $rebate_paymenttype);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // ✅ Show success message (if all inserts were successful)
        if ($success) {
            $displayType = ucfirst($paymenttype);
            if ($paymenttype === "Rebate") {
                $successMessage = "Rebate Applied Successfully!";
            } elseif ($paymenttype === "Development") {
                $successMessage = "Development Charges Added Successfully!";
            } elseif ($paymenttype === "Maintenance") {
                $successMessage = "Maintenance Added Successfully!";
            } elseif ($paymenttype === "Transferfee") {
                $successMessage = "Transfer Fee Added Successfully!";
            } elseif ($paymenttype === "Rentcop") {
                $successMessage = "C.O.P Rent Added Successfully!";
            } elseif ($paymenttype === "Otherrent") {
                $successMessage = "Other Rent Added Successfully!";
            } elseif ($paymenttype === "Mehsul") {
                $successMessage = "Mehsul Added Successfully!";
            } else {
                $successMessage = "$displayType Added Successfully!";
            }

            echo "<div class='login-feedback'>
                <div class='tick-box tick-success'>
                    <svg class='tick-svg' viewBox='0 0 52 52'>
                        <circle class='tick-circle' cx='26' cy='26' r='25'/>
                        <path class='tick-check' d='M14,27 L22,35 L38,19'/>
                    </svg>
                    <p class='tick-message text-success'>$successMessage</p>
                </div>
                <script>
                    setTimeout(() => {
                        window.location.href = '$redirectPage';
                    }, 2500);
                </script>
            </div>";
        }
    }

    mysqli_close($connect);
}

// API: fetch latest maintenance entry for house_no
if (isset($_GET['house_no'])) {
    header('Content-Type: application/json');
    $connect = new mysqli("localhost", "root", "", "sds");
    $house_no = $connect->real_escape_string($_GET['house_no']);

    $query = "SELECT m_date, month, year FROM maintenance 
              WHERE house_no = ? AND paymenttype = 'Maintenance'
              ORDER BY STR_TO_DATE(m_date, '%Y-%m-%d') DESC
              LIMIT 1";

    $stmt = $connect->prepare($query);
    $stmt->bind_param("s", $house_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $lastEntry = $result->fetch_assoc();

    echo json_encode([
        'month' => $lastEntry['month'] ?? 'N/A',
        'year' => $lastEntry['year'] ?? '',
        'm_date' => $lastEntry['m_date'] ?? ''
    ]);
    exit;
}

// Message alerts
if (isset($_GET['message'])) {
    $message = $_GET['message'];
    $alertType = $_GET['alertType'] ?? 'info';
} else {
    $message = '';
    $alertType = 'info';
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SDS | Maintenance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="home.png"> 
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
    <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
        <img src="SDS Logo.png" alt="Society Dashboard" height="40" class="me-2">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button> 
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav ms-auto d-flex gap-2">
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="maintenanceexpenses.php"><i class="bi bi-gear-wide-connected me-1"></i>Maintenance Expenses</a></li>
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
        <h3><i class="bi bi-tools me-2"></i>Maintenance Entry</h3>
        <div class="container text-center mt-3">
</div>

        <?php if (!empty($message)) : ?>
    <div class="alert alert-<?= $alertType ?> alert-dismissible fade show" role="alert">
      <?= $message ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>
  
        <div class="container mb-3">
  <div class="alert alert-warning shadow-sm">
    <i class="bi bi-info-circle-fill"></i> Fill the form to record maintenance charges collected for a house/plot.
  </div>
</div>

        <div class="alert alert-info"><strong>Last Receipt No:</strong> <?= $last_receipt_no ?></div>

<form method="POST" class="row g-3 needs-validation" novalidate>
    <div class="col-md-6">
        <label class="form-label"><i class="bi bi-receipt me-1"></i> Receipt No</label>
        <input type="text" name="receipt_no" class="form-control" required pattern="A/\d+" title="Format: A/450">
        <div class="invalid-feedback">Receipt No. must follow the format A/### (e.g., A/450).</div>
    </div>

    <div class="col-md-6">
        <label class="form-label"><i class="bi bi-calendar-date me-1"></i> Date</label>
        <input type="date" name="m_date" class="form-control" required>
        <div class="invalid-feedback">Please select a valid date.</div>
    </div>

    <div class="col-md-4">
        <label class="form-label"><i class="bi bi-house-door me-1"></i> House No.</label>
        <select id="house_no" name="house_no" class="form-select" required>
            <option value="">-- Select House No --</option>
            <option value="">None</option>
            <option value="Soc">Society</option>
            <?php for ($i = 1; $i <= 47; $i++) echo "<option value=\"$i\">$i</option>"; ?>
        </select>
        <div class="invalid-feedback">Please select a house number.</div>
    </div>

    <div class="col-md-4">
        <label for="name" class="form-label"><i class="bi bi-person-circle me-1"></i> Name (Auto-filled)</label>
        <input type="text" id="name" name="name" class="form-control" readonly required>
        <div class="invalid-feedback">Name will be auto-filled based on house number.</div>
    </div>

    <div class="col-md-4">
        <label class="form-label"><i class="bi bi-currency-rupee me-1"></i> Amount</label>
        <input type="text" name="ammount" class="form-control" required pattern="\d+(\.\d{1,2})?" title="Enter a valid positive number">
        <div class="invalid-feedback">Please enter a valid amount (e.g., 250 or 500.00).</div>
    </div>

    <div class="col-md-4">
        <label class="form-label"><i class="bi bi-wallet2 me-1"></i> Payment Type</label>
        <select name="ammounttype" class="form-select" required>
            <option value="">-- Select Amount Type --</option>
            <option value="Cash">Cash</option>
            <option value="Banktransfer">Bank Transfer</option>
        </select>
        <div class="invalid-feedback">Please select a payment type.</div>
    </div>

    <div class="col-md-6">
        <label class="form-label"><i class="bi bi-credit-card me-1"></i>Amount Type</label>
        <select name="paymenttype" class="form-select" required>
            <option value="">-- Select Amount Type --</option>
            <option value="Maintenance">Maintenance</option>
            <option value="Development">Development Charges</option>
            <option value="Transferfee">Transfer Charges</option>
            <option value="Rentcop">C.O.P Rent</option>
            <option value="Otherrent">Other Rent</option>
            <option value="Mehsul">Mehsul</option>
        </select>
        <div class="invalid-feedback">Please select an amount type.</div>
    </div>

    <div class="col-md-6">
        <label class="form-label"><i class="bi bi-patch-check me-1"></i> Rebate</label><br>
        <input type="checkbox" name="rebate" class="form-check-input">
        <label class="form-check-label">Apply Rebate</label>
    </div>

    <div class="alert alert-info">
        <strong><h4 id="lastPaidText">Last Maintenance Paid Month: </h4></strong>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const houseNoSelect = document.querySelector('select[name="house_no"]');
        const lastPaidText = document.getElementById('lastPaidText');

        houseNoSelect.addEventListener('change', function() {
            const houseNo = this.value;
            if (houseNo !== 'None') {
                fetch(`maintenance.php?house_no=${houseNo}`)
                    .then(response => response.json())
                    .then(data => {
                        lastPaidText.textContent = `Last Paid Month: ${data.month} ${data.year}`;
                    })
                    .catch(error => console.error('Error fetching last paid month:', error));
            } else {
                lastPaidText.textContent = 'Last Paid Month: N/A';
            }
        });
    });
    </script>

    <div class="col-md-6">
        <label class="form-label"><i class="bi bi-calendar-event me-1"></i> Month</label>
        <select name="month" class="form-select" required>
            <option value="">None</option>
            <?php
            $months = ["January", "February", "March", "April", "May", "June",
                       "July", "August", "September", "October", "November", "December"];
            foreach ($months as $m) echo "<option value=\"$m\">$m</option>";
            ?>
        </select>
        <div class="invalid-feedback">Please select a month.</div>
    </div>

    <div class="col-md-6">
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
        <a href="Showdatamaintenance.php" class="btn btn-info">
            <i class="bi bi-table me-1"></i> Show Data
        </a>
    </div>
</form>
    </div>
</div>

</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('house_no').addEventListener('change', function () {
    const houseNo = this.value;
    const nameField = document.getElementById('name');

    if (houseNo !== "None") {
        fetch(`get_name.php?house_no=${houseNo}`)
            .then(response => response.text())
            .then(data => {
                nameField.value = data || '';
            })
            .catch(error => {
                console.error("Error fetching name:", error);
                nameField.value = '';
            });
    } else {
        nameField.value = '';
    }
});
</script>
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
