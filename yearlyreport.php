<?php
$connect = new mysqli("localhost", "root", "", "sds");
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

$selected_year_range = $_POST['year_range'] ?? '';
$currentyear = 0;

if (!empty($selected_year_range)) {
    list($start_year, $end_year) = explode("-", $selected_year_range);
    $start_date = "$start_year-04-01";
    $end_date = "$end_year-03-31";

    // Queries
    $querycurrentyear = "SELECT SUM(ammount) AS currentyear FROM maintenance WHERE m_date BETWEEN '$start_date' AND '$end_date' AND paymenttype='Maintenance' AND ( (year = '$start_year' AND month IN ('April','May','June','July','August','September','October','November','December')) OR (year = '$end_year' AND month IN ('January','February','March')))";
    $querycurrentyeardev = "SELECT SUM(ammount) AS currentyeardev FROM maintenance WHERE m_date BETWEEN '$start_date' AND '$end_date' AND paymenttype='Development'";
    $queryooldmaintenance = "SELECT SUM(ammount) AS ooldmaintenance FROM maintenance WHERE m_date BETWEEN '$start_date' AND '$end_date' AND paymenttype='Maintenance' AND (year='$start_year' AND month IN ('January','February','March'))";
    $queryoooldmaintenance = "SELECT SUM(ammount) AS oooldmaintenance FROM maintenance WHERE m_date BETWEEN '$start_date' AND '$end_date' AND paymenttype='Maintenance' AND year<'$start_year'";
    $queryadvancemaintenance = "SELECT SUM(ammount) AS advancemaintenance FROM maintenance WHERE m_date BETWEEN '$start_date' AND '$end_date' AND paymenttype='Maintenance' AND (year>'$end_year' OR (year='$end_year' AND month IN ('April','May','June','July','August','September','October','November','December')))";
    $querytotalexpencecurrent = "SELECT SUM(ammount) AS totalexpencecurrent FROM maintenancetransactions WHERE year= '$start_year-$end_year'";
    $queryrebatecur = "SELECT SUM(ammount) AS totalrebatecur FROM maintenance WHERE m_date BETWEEN '$start_date' AND '$end_date' AND paymenttype='Rebate' AND ((year='$start_year' AND month IN ('April','May','June','July','August','September','October','November','December')) OR (year='$end_year' AND month IN ('January','February','March')))";
    $queryotherrent = "SELECT SUM(ammount) AS totalotherrent FROM maintenance WHERE m_date BETWEEN '$start_date' AND '$end_date' AND paymenttype='Otherrent' AND ((year='$start_year' AND month IN ('April','May','June','July','August','September','October','November','December')) OR (year='$end_year' AND month IN ('January','February','March')))";
    $querytransferfeecur = "SELECT SUM(ammount) AS totaltransferfeecur FROM maintenance WHERE m_date BETWEEN '$start_date' AND '$end_date' AND paymenttype='Transferfee' AND ((year='$start_year' AND month IN ('April','May','June','July','August','September','October','November','December')) OR (year='$end_year' AND month IN ('January','February','March')))";
    $queryOpeningBalance = "SELECT opening_balance FROM yearlyreport WHERE year = '$selected_year_range'";
    $queryBankBalance = "SELECT bankbalance FROM yearlyreport WHERE year = '$selected_year_range'";
    $querycoprent = "SELECT SUM(ammount) AS coprent FROM maintenance WHERE m_date BETWEEN '$start_date' AND '$end_date' AND paymenttype='Rentcop'";
    $queryIntrest = "SELECT SUM(ammount) AS totalinterest FROM bankdebit WHERE b_date BETWEEN '$start_date' AND '$end_date' AND b_detail='Interest'";

    // Results
    $currentyear = $connect->query($querycurrentyear)->fetch_assoc()['currentyear'] ?? 0;
    $currentdevelopment = $connect->query($querycurrentyeardev)->fetch_assoc()['currentyeardev'] ?? 0;
    $ooldmaintenence = $connect->query($queryooldmaintenance)->fetch_assoc()['ooldmaintenance'] ?? 0;
    $oooldmaintenence = $connect->query($queryoooldmaintenance)->fetch_assoc()['oooldmaintenance'] ?? 0;
    $totaloldmaintenance = $ooldmaintenence + $oooldmaintenence;
    $advancemaintenance = $connect->query($queryadvancemaintenance)->fetch_assoc()['advancemaintenance'] ?? 0;
    $totalexpencecurrent = $connect->query($querytotalexpencecurrent)->fetch_assoc()['totalexpencecurrent'] ?? 0;
    $totalrebatecur = $connect->query($queryrebatecur)->fetch_assoc()['totalrebatecur'] ?? 0;
    $totalotherrent = $connect->query($queryotherrent)->fetch_assoc()['totalotherrent'] ?? 0;
    $totaltransferfeecur = $connect->query($querytransferfeecur)->fetch_assoc()['totaltransferfeecur'] ?? 0;
    $opening_balance = $connect->query($queryOpeningBalance)->fetch_assoc()['opening_balance'] ?? 0;
    $bank_balance = $connect->query($queryBankBalance)->fetch_assoc()['bankbalance'] ?? 0;
    $coprent = $connect->query($querycoprent)->fetch_assoc()['coprent'] ?? 0;
    $totalinterest = $connect->query($queryIntrest)->fetch_assoc()['totalinterest'] ?? 0;

    $total_income = $opening_balance + $currentyear + $currentdevelopment + $totaloldmaintenance + $advancemaintenance + $totaltransferfeecur + $totalinterest + $totalotherrent + $coprent;
    $cash_on_hand = $total_income - $totalexpencecurrent - $totalrebatecur - $bank_balance;
    $total_expence = $totalexpencecurrent + $totalrebatecur;
    $closing_balance = $total_income - $total_expence;
}

$connect->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SDS | Yearly Balance Report</title>
    <link rel="icon" href="home.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS CDN -->
     
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
  .cash {
    background-color: #d4edda; /* Light green background */
    color: #155724; /* Dark green text */
    font-weight: bold;
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
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="maintenance.php"><i class="bi bi-tools me-1"></i>Maintenance</a></li>
            <li class="nav-item"><a class="nav-link" href="Religious.php"><i class="bi bi-flower1 me-1"></i>Religious Fund</a></li>
            <li class="nav-item"><a class="nav-link" href="balance.php"><i class="bi bi-cash-stack me-1"></i>Balance</a></li>
            <li class="nav-item"><a class="nav-link" href="Receipt.php"><i class="bi bi-receipt-cutoff me-1"></i>Receipts</a></li>
            <li class="nav-item"><a class="nav-link" href="Bankdebit.php"><i class="bi bi-bank me-1"></i>Bank</a></li>
            <li class="nav-item"><a class="nav-link" href="housedetails.php"><i class="bi bi-building me-1"></i>House Details</a></li>
            <li class="nav-item"><a class="nav-link" href="yearlyreport_add.php"><i class="bi bi-calendar3 me-1"></i>Add  Budgets
            <li class="nav-item"><a class="nav-link" href="admin_manage_complaints.php"><i class="bi bi-chat-dots me-1"></i>View Complaints</a></li>
            <li class="nav-item"><a class="nav-link" href="viewpayments.php"><i class="bi bi-credit-card-2-back me-1"></i>View Payments</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-box-arrow-left me-1"></i>Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container my-4">
    <div class="card shadow-sm p-4">
        <div class="container text-center mt-3 align-items-center">
              
    <h2 class="mb-4">Yearly Balance Report</h2>

    <form method="post" class="row g-3 align-items-center mb-4">
    <div class="col-auto">
        <label for="year_range" class="col-form-label"><strong>Select Financial Year:</strong></label>
    </div>
    <div class="col-auto">
        <select name="year_range" id="year_range" class="form-select" onchange="this.form.submit()" required>
            <option value="">-- Select --</option>
            <?php
            $startYear = 2013;
            $currentYear = date('Y');
            for ($y = $startYear; $y <= $currentYear + 3; $y++) {
                $next = $y + 1;
                $range = "$y-$next";
                $selected = ($selected_year_range == $range) ? "selected" : "";
                echo "<option value=\"$range\" $selected>$range</option>";
            }
            ?>
        </select>
    </div>
</div>

</form>


    <?php if (!empty($selected_year_range)): ?>
        <div class="report mt-5 align-items-center">

            <div class="alert alert-secondary">
                <h5><i class="bi bi-calendar-check"></i> Financial Year : <strong><?= htmlspecialchars($selected_year_range) ?></strong></h5>
            </div>
            <ul class="list-group align-flex column gap-2">
                <li class="list-group-item cash">Opening Balance: ₹<?= number_format($opening_balance, 2) ?></li>
                <li class="list-group-item">Total Maintenance: ₹<?= number_format($currentyear, 2) ?></li>
                <li class="list-group-item">Total Development: ₹<?= number_format($currentdevelopment, 2) ?></li>
                <li class="list-group-item">Old Maintenance: ₹<?= number_format($totaloldmaintenance, 2) ?></li>
                <li class="list-group-item">Advance Maintenance: ₹<?= number_format($advancemaintenance, 2) ?></li>
                <li class="list-group-item">Transfer Fee: ₹<?= number_format($totaltransferfeecur, 2) ?></li>
                <li class="list-group-item">Other Rent: ₹<?= number_format($totalotherrent, 2) ?></li>
                <li class="list-group-item">Cop Rent: ₹<?= number_format($coprent, 2) ?></li>
                <li class="list-group-item">Total Interest: ₹<?= number_format($totalinterest, 2) ?></li>
                <li class="list-group-item cash">Total Receipt: ₹<?= number_format($total_income, 2) ?></li>
                <li class="list-group-item">Expenses: ₹<?= number_format($totalexpencecurrent, 2) ?></li>
                <li class="list-group-item">Rebate: ₹<?= number_format($totalrebatecur, 2) ?></li>
                <li class="list-group-item cash">Total Expense: ₹<?= number_format($total_expence, 2) ?></li>
                <li class="list-group-item cash">Cash on Hand: ₹<?= number_format($cash_on_hand, 2) ?></li>
                <li class="list-group-item cash">Bank Balance: ₹<?= number_format($bank_balance, 2) ?></li>
                <li class="list-group-item">Closing Balance: ₹<?= number_format($closing_balance, 2) ?></li>
            </ul>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>