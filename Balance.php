<?php
// Database connection
$connect = new mysqli("localhost", "root", "", "sds");
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

// Queries
$queryReligious = "SELECT SUM(ammount) AS total_religious FROM religious";
$queryexpensereligious = "SELECT SUM(ammount) AS total_expensereligious FROM transactions";
$querymaintenance = "SELECT SUM(ammount) AS total_maintenance FROM maintenance";
$querymaintenanceexpenses = "SELECT SUM(ammount) AS total_maintenanceexpenses FROM maintenancetransactions";
$querybanktransfer = "SELECT SUM(ammount) AS total_banktransfer FROM maintenance WHERE ammounttype='Banktransfer'";
$querycashrebate = "SELECT SUM(ammount) AS total_cashrebate FROM maintenance WHERE paymenttype='Rebate' AND ammounttype='Cash'";
$querybank = "SELECT SUM(ammount) AS total_bank FROM bankdebit WHERE b_detail='Debit'";
$querybankrebate = "SELECT SUM(ammount) AS total_bankrebate FROM maintenance WHERE paymenttype='Rebate' AND ammounttype='Banktransfer'";
$querybankcredit = "SELECT SUM(ammount) AS total_bankcredit FROM bankdebit WHERE b_detail='Credit'";
$querybankinterest = "SELECT SUM(ammount) AS totalbankinterest FROM bankdebit WHERE b_detail='Interest'";
$querydevelopment = "SELECT SUM(ammount) AS total_development FROM maintenance WHERE paymenttype='Development'";

// Results
$totalReligious = $connect->query($queryReligious)->fetch_assoc()['total_religious'] ?? 0;
$totalexpensereligious = $connect->query($queryexpensereligious)->fetch_assoc()['total_expensereligious'] ?? 0;
$totalmaintenance = $connect->query($querymaintenance)->fetch_assoc()['total_maintenance'] ?? 0;
$totalmaintenanceexpenses = $connect->query($querymaintenanceexpenses)->fetch_assoc()['total_maintenanceexpenses'] ?? 0;
$totalbanktransfer = $connect->query($querybanktransfer)->fetch_assoc()['total_banktransfer'] ?? 0;
$totalbank = $connect->query($querybank)->fetch_assoc()['total_bank'] ?? 0;
$totalcashrebate = $connect->query($querycashrebate)->fetch_assoc()['total_cashrebate'] ?? 0;
$totalbankrebate = $connect->query($querybankrebate)->fetch_assoc()['total_bankrebate'] ?? 0;
$totalbankcredit = $connect->query($querybankcredit)->fetch_assoc()['total_bankcredit'] ?? 0;
$totalbankinterest = $connect->query($querybankinterest)->fetch_assoc()['totalbankinterest'] ?? 0;
$totaldevelopment = $connect->query($querydevelopment)->fetch_assoc()['total_development'] ?? 0;

// Calculations
$totalmaintenancerebate = $totalmaintenance - $totalcashrebate - $totalbankrebate + $totalbankinterest;
$cashOnHandReligious = $totalReligious - $totalexpensereligious;
$totalrebate = $totalcashrebate + $totalbankrebate;
$cashOnHandMaintenance = $totalmaintenance - $totalmaintenanceexpenses - $totalbanktransfer - $totalbankcredit + $totalbank - $totalcashrebate - $totalcashrebate;
$total_bank_transfer = $totalbanktransfer - $totalbank - $totalbankrebate - $totalbankrebate + $totalbankcredit + $totalbankinterest;
$connect->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SDS | Balance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="home.png"> 
    <style>
        body {
            background-image: url('SDSBG.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-repeat: no-repeat;
            font-family: 'Segoe UI', sans-serif;
        }
        .red-text {
            color: red;
            font-weight: bold;
        }
        .navbar-brand img {
            height: 40px;
        }
        .nav-link:hover {
            font-weight: bold;
            color: #ffc107 !important;
        }
        @media (max-width: 768px) {
            h1 {
                font-size: 1.5rem;
                text-align: center;
            }
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
    <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="SDS Logo.png" alt="Society Dashboard" height="40" class="me-2">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button> 
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav ms-auto d-flex gap-2">
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
<div class="container mt-5">
    <h1 class="mb-4">Balance Summary</h1>
    <div class="row g-4">
        <!-- Left Section -->
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Maintenance</h5>
                    <p>Total Maintenance: <strong>₹<?= number_format($totalmaintenancerebate, 2); ?></strong></p>
                    <p>Total Expenses: ₹<?= number_format($totalmaintenanceexpenses, 2); ?></p>
                    <p>Bank Balance: ₹<?= number_format($total_bank_transfer, 2); ?></p>
                    <p>Bank Debit: ₹<?= number_format($totalbank, 2); ?></p>
                    <p>Bank Credit: ₹<?= number_format($totalbankcredit, 2); ?></p>
                    <p>Bank Interest: ₹<?= number_format($totalbankinterest, 2); ?></p>
                    <p>Rebate: ₹<?= number_format($totalrebate, 2); ?></p>
                    <p>Total Development: ₹<?= number_format($totaldevelopment, 2); ?></p>
                    <p>Cash On Hand: <span class="red-text">₹<?= number_format($cashOnHandMaintenance, 2); ?></span></p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Religious Fund</h5>
                    <p>Total Religious Fund: ₹<?= number_format($totalReligious, 2); ?></p>
                    <p>Total Expenses: ₹<?= number_format($totalexpensereligious, 2); ?></p>
                    <p>Cash On Hand: <span class="red-text">₹<?= number_format($cashOnHandReligious, 2); ?></span></p>
                </div>
            </div>
        </div>

        <!-- Right Section -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Cash on Hand Counter</h5>
                    <form method="POST">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Note</th>
                                        <th>X</th>
                                        <th>Count</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $denominations = [500, 200, 100, 50, 20, 10, 5, 2, 1];
                                $total = 0;
                                foreach ($denominations as $note) {
                                    $count = isset($_POST["count_$note"]) ? intval($_POST["count_$note"]) : 0;
                                    $amount = $count * $note;
                                    $total += $amount;
                                    echo "<tr>
                                            <td>₹$note</td>
                                            <td>X</td>
                                            <td><input type='number' name='count_$note' value='$count' class='form-control form-control-sm' min='0'></td>
                                            <td>₹$amount</td>
                                        </tr>";
                                }
                                $transfer = $_POST['transfer'] ?? $total;
                                $diff = $total - $cashOnHandMaintenance;
                                ?>
                                <tr>
                                    <td colspan="3">Transfer to religious fund</td>
                                    <td><input type="number" name="transfer" value="<?= $transfer ?>" class="form-control form-control-sm"></td>
                                </tr>
                                <tr class="table-success">
                                    <td colspan="3"><strong>TOTAL</strong></td>
                                    <td><strong>₹<?= $total ?></strong></td>
                                </tr>
                                <tr class="table-warning">
                                    <td colspan="3"><strong>DIFF</strong></td>
                                    <td><strong>₹<?= number_format($diff, 2) ?></strong></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary mt-2">Calculate</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
