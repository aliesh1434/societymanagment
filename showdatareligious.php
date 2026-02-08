<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $connect = mysqli_connect("localhost", "root", "", "sds");
    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $festival = htmlspecialchars($_POST['festival']);
    $year = filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT);

    $query = "SELECT * FROM religious WHERE festival = ? AND year = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("ss", $festival, $year);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>SDS | Show Religious Fund</title>
    <link rel="icon" href="home.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
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
            <li class="nav-item"><a class="nav-link" href="religious.php"><i class="bi bi-flower1 me-1"></i>Religious Fund</a></li>
            <li class="nav-item"><a class="nav-link" href="balance.php"><i class="bi bi-cash-stack me-1"></i>Balance</a></li>
            <li class="nav-item"><a class="nav-link" href="Receipt.php"><i class="bi bi-receipt-cutoff me-1"></i>Receipts</a></li>
            <li class="nav-item"><a class="nav-link" href="Bankdebit.php"><i class="bi bi-bank me-1"></i>Bank </a></li>
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
    <div class="card shadow-sm">
        <div class="card-body">
<h3 class="card-title text-center mb-4">
    <i class="bi bi-flower1 me-2"></i><u>Show Religious Fund</u>
</h3>
<form method="POST" class="row g-3 needs-validation" novalidate>
                <div class="row g-3">
                    <div class="col-md-6">
    <label class="form-label">
        <i class="bi bi-stars me-1"></i>Festival:
    </label>
                        <select id="festival" name="festival" class="form-select" required>
                            <option value="">None</option>
                            <option value="Holi">Holi</option>
                            <option value="Janmashtami">Janmashtami</option>
                            <option value="Ganeshotsav">Ganeshotsav</option>
                            <option value="Navratri">Navratri</option>
                            <option value="Shanti Havan">Shanti Havan</option>
                        </select>
                        <div class="invalid-feedback">Please select a festival.</div>
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

                </div>
                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="bi bi-eye me-1"></i>Show Data</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($result)): ?>
        <div class="mt-5">
            <?php if ($result->num_rows > 0): ?>
                <?php $totalAmount = 0; ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover mt-3">
                        <thead class="table-dark">
    <tr>
        <th><i class="bi bi-hash me-1"></i>Sr No.</th>
        <th><i class="bi bi-person-circle me-1"></i>Name</th>
        <th><i class="bi bi-house-door me-1"></i>House No.</th>
        <th><i class="bi bi-calendar-event me-1"></i>Festival</th>
        <th><i class="bi bi-calendar me-1"></i>Year</th>
        <th><i class="bi bi-cash-coin me-1"></i>Amount</th>
    </tr>
</thead>

                        <tbody>
                            <?php $cnt = 1; ?>
                            <?php while ($row = $result->fetch_object()): ?>
                                <tr>
                                    <td><?= $cnt ?></td>
                                    <td><?= htmlspecialchars($row->name) ?></td>
                                    <td><?= htmlspecialchars($row->house_no) ?></td>
                                    <td><?= htmlspecialchars($row->festival) ?></td>
                                    <td><?= htmlspecialchars($row->year) ?></td>
                                    <td><?= number_format($row->ammount, 2) ?></td>
                                </tr>
                                <?php
                                    $totalAmount += $row->ammount;
                                    $cnt++;
                                ?>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-end"><strong>Total Amount:</strong></td>
                            <td><strong>₹ <?= number_format($totalAmount, 2) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php else: ?>
                    <div class="alert alert-warning mt-4 text-center">
                        <i class="bi bi-exclamation-triangle"></i> No data found for the selected festival and year.
                    </div>
            
            <?php endif; ?>
                    
            <?php
                $stmt->close();
                mysqli_close($connect);
            ?>
        </div>
    <?php endif; ?>
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
