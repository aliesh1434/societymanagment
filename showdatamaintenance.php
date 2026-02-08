<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $connect = mysqli_connect("localhost", "root", "", "sds");

    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $house_no = htmlspecialchars($_POST['house_no']);
    $year = filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT);

    $query = "SELECT * FROM maintenance WHERE house_no = ? AND year = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("ss", $house_no, $year);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SDS | Show Data Maintenance</title>
    <link rel="icon" type="image/png" href="home.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons CDN -->
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


<!-- Main Content -->
<div class="container my-4">
  <div class="card shadow-sm p-4">
      <h3 class="mb-3"><u></i>Show Data Maintenance</u></h3>
<form method="POST" class="row g-3 needs-validation" novalidate>
        <div class="col-md-4">
            <label class="form-label"><i class="bi bi-house-door me-1"></i> House No.</label>
        <select id="house_no" name="house_no" class="form-select" required>
            <option value="">-- Select House No --</option>
            <option value="None">None</option>
            <option value="Soc">Society</option>
            <?php for ($i = 1; $i <= 47; $i++) echo "<option value=\"$i\">$i</option>"; ?>
        </select>
        <div class="invalid-feedback">Please select a house number.</div>
    </div>

        <div class="col-md-6">
    <label class="form-label">
        <i class="bi bi-calendar2-week"></i> Year
    </label>
    <select class="form-select" id="year" name="year" class="form-select" required>
        <option value="">Select Year</option>
                <?php
            $currentYear = date("Y");
            for ($y = 2013; $y <= $currentYear + 3; $y++) echo "<option value=\"$y\">$y</option>";
            ?>
            </select>
        <div class="invalid-feedback">Please select a year.</div>
        </div>
        
        <div class="col-12">
            <button type="submit" class="btn btn-primary"><i class="bi bi-eye-fill"></i> Show Data</button>
        </div>
      </form>
    </div>

    <!-- Display Results -->
    <?php if (isset($result)): ?>
        <?php if ($result->num_rows > 0): ?>
            <?php $totalAmount = 0; ?>
            <div class="table-responsive">
               <table class="table table-bordered table-striped table-hover mt-3">
                    <thead class="table-dark">
  <tr>
    <th><i class="bi bi-123"></i> Sr No.</th>
    <th><i class="bi bi-receipt-cutoff"></i> Receipt No.</th>
    <th><i class="bi bi-calendar-event"></i> Date</th>
    <th><i class="bi bi-house-door"></i> House No.</th>
    <th><i class="bi bi-credit-card-2-back"></i> Payment Type</th>
    <th><i class="bi bi-tag"></i> Amount Type</th>
    <th><i class="bi bi-calendar3"></i> Month</th>
    <th><i class="bi bi-calendar2-week"></i> Year</th>
    <th><i class="bi bi-currency-rupee"></i> Amount (₹)</th>
  </tr>
</thead>

                    <tbody>
                        <?php $cnt = 1; ?>
                        <?php while ($row = $result->fetch_object()): ?>
                            <tr>
                                <td><?= $cnt++ ?></td>
                                <td><?= htmlspecialchars($row->receipt_no) ?></td>
                                <td><?= htmlspecialchars($row->m_date) ?></td>
                                <td><?= htmlspecialchars($row->house_no) ?></td>
                                <td><?= htmlspecialchars($row->ammounttype) ?></td>
                                <td><?= htmlspecialchars($row->paymenttype) ?></td>
                                <td><?= htmlspecialchars($row->month) ?></td>
                                <td><?= htmlspecialchars($row->year) ?></td>
                                <td><?= number_format((float)$row->ammount, 2) ?></td>
                            </tr>
                            <?php $totalAmount += (float)$row->ammount; ?>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <td colspan="8" class="text-end"><strong>Total Amount:</strong></td>
                            <td><strong>₹ <?= number_format($totalAmount, 2) ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning mt-3" role="alert">
                <i class="bi bi-exclamation-circle"></i> No data found for selected House No. and Year.
            </div>
        <?php endif; ?>
        <?php $stmt->close(); mysqli_close($connect); ?>
    <?php endif; ?>
</div>

<!-- Bootstrap JS (optional for dropdowns etc.) -->
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
<?php

