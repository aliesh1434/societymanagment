<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $connect = mysqli_connect("localhost", "root", "", "sds");

    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $year = trim($_POST['year']);

    $query = "SELECT * FROM maintenancetransactions WHERE year = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("s", $year);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>SDS | Show Maintenance Expenses</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="home.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            padding-top: 90px;
        }

        @media (max-width: 768px) {
            body {
                padding-top: 100px;
            }
        }

        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }

        .table thead th {
            position: sticky;
            top: 0;
           background-color: #343a40;
            color: #fff;
            z-index: 1;
        }
    
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 fixed-top">
    <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
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

<!-- Form -->
<div class="container">
    <div class="card shadow-sm p-4">
        <h3 class="mb-3"><i class="bi bi-table"></i> Show Data Maintenance Expenses</h3>

<form method="POST" class="row g-3 needs-validation" novalidate>
            <label for="year" class="form-label">
                <i class="bi bi-calendar2-week"></i> <b>Year</b>
            </label>
            <select class="form-select" id="year" name="year" required>
                <option value="">Select Year</option>
                <?php
                $startYear = 2013;
                $endYear = date('Y') + 1 + 10;

                for ($j = $startYear; $j <= $endYear; $j++) {
                    $nextYear = $j + 1;
                    $value = "$j-$nextYear";
                    echo "<option value=\"$value\">$value</option>";
                }
                ?>
            </select>
            <div class="invalid-feedback">Please select a year.</div>
        

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-eye-fill"></i> Show Data</button>
            </div>
        </form>
    </div>

    <!-- Results -->
    <?php if (isset($result)) {
        if ($result->num_rows > 0) {
            $totalAmount = 0;
            echo '
            <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped table-hover mt-3">
                <thead class="table-dark">
                    <tr>
                        <th><i class="bi bi-hash"></i> Sr No.</th>
                        <th><i class="bi bi-card-text"></i> Transaction Detail</th>
                        <th><i class="bi bi-calendar3"></i> Year</th>
                        <th><i class="bi bi-currency-rupee"></i> Amount</th>
                    </tr>
                </thead>
                <tbody>';
            $cnt = 1;
            while ($row = $result->fetch_object()) {
                echo '<tr>
                        <td>' . $cnt . '</td>
                        <td>' . htmlspecialchars($row->t_detail) . '</td>
                        <td>' . htmlspecialchars($row->year) . '</td>
                        <td>' . number_format($row->ammount, 2) . '</td>
                    </tr>';
                $totalAmount += $row->ammount;
                $cnt++;
            }
            echo '</tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Total Amount:</td>
                        <td class="fw-bold">₹ ' . number_format($totalAmount, 2) . '</td>
                    </tr>
                </tfoot>
            </table>
            </div>';
        } 

        $stmt->close();
        mysqli_close($connect);
    } ?>
</div>

<!-- Bootstrap JS -->
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
