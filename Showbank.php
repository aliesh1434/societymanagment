<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    session_start();
    $connect = mysqli_connect("localhost", "root", "", "sds");

    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $selected_year_range = $_POST['year_range'] ?? '';
    $currentyear = 0;
    $result = null;

    if (!empty($selected_year_range)) {
        list($start_year, $end_year) = explode("-", $selected_year_range);
        $start_date = "$start_year-04-01";
        $end_date = "$end_year-03-31";
        $query = "SELECT * FROM bankdebit WHERE b_date BETWEEN '$start_date' AND '$end_date'";
        $result = mysqli_query($connect, $query);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SDS | Show Bank Transactions</title>
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
            <li class="nav-item"><a class="nav-link" href="adminrental.php"><i class="bi bi-house me-1"></i>Rental Details</a></li>
            <li class="nav-item"><a class="nav-link" href="yearlyreport.php"><i class="bi bi-calendar3 me-1"></i>Yearly Report</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_manage_complaints.php"><i class="bi bi-chat-dots me-1"></i>View Complaints</a></li>
            <li class="nav-item"><a class="nav-link" href="viewpayments.php"><i class="bi bi-credit-card-2-back me-1"></i>View Payments</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-box-arrow-left me-1"></i>Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-4">

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="POST" action="" class="row g-3 align-items-center">
                <h3 class="text-center text-primary mb-4"><i class="bi bi-bank2 me-2"></i><u>Show Bank Transactions</u></h3>
                <div class="col-auto">
                    <label for="year_range" class="col-form-label fw-bold">
                        <i class="bi bi-calendar-event"></i> Select Year:
                    </label>
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
                            $selected = (isset($selected_year_range) && $selected_year_range == $range) ? "selected" : "";
                            echo "<option value=\"$range\" $selected>$range</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-auto">
                    
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($result)) : ?>
        <?php if (mysqli_num_rows($result) > 0) : ?>
            <?php
            $totalAmount = 0;
            ?>
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <i class="bi bi-bank"></i> Bank Entries (<?= htmlspecialchars($selected_year_range) ?>)
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 table-striped">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Sr No.</th>
                                    <th><i class="bi bi-journal-text"></i> Detail</th>
                                    <th><i class="bi bi-calendar-date"></i> Date</th>
                                    <th><i class="bi bi-currency-rupee"></i> Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cnt = 1;
                                $cnt = 1;
while ($row = mysqli_fetch_object($result)) {
    $detail = htmlspecialchars($row->b_detail);
    $date = htmlspecialchars($row->b_date);
    $amount = number_format($row->ammount, 2);

    // Determine if it's a credit
    $lower_detail = strtolower($detail);
    $is_credit = strpos($lower_detail, 'credit') !== false || strpos($lower_detail, 'interest') !== false;

    $amount_class = $is_credit ? 'text-success fw-bold' : 'text-danger fw-bold';
    $icon = $is_credit
        ? "<i class='bi bi-arrow-up-circle me-1 text-success'></i>"
        : "<i class='bi bi-arrow-down-circle me-1 text-danger'></i>";

    echo "<tr>
            <td>$cnt</td>
            <td>$detail</td>
            <td>$date</td>
            <td class='$amount_class'>$icon ₹$amount</td>
          </tr>";
    $cnt++;
}


                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="alert alert-warning mt-3"><i class="bi bi-info-circle"></i> No data found for selected year.</div>
        <?php endif; ?>
        <?php mysqli_close($connect); ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
