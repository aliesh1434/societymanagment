<?php
$connect = mysqli_connect("localhost", "root", "", "sds");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "
    SELECT 
        hp.house_no,
        hp.name,
        hp.house_details,
        hp.development,
        hp.chairrent,
        hp.mehsul,
        hp.coprent,
        hp.transferfee
    FROM house_plot hp
";



$result = $connect->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>SDS | House/Plot Details</title>
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

        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }

        .table thead th {
            position: sticky;
            top: 0;
            background-color: #212529;
            color: white;
            z-index: 1;
            text-align: center;
        }

        .table td {
            text-align: center;
            vertical-align: middle;
        }

        .badge-soft-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .badge-soft-danger {
            background-color: #f8d7da;
            color: #842029;
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
            <li class="nav-item"><a class="nav-link" href="adminrental.php"><i class="bi bi-house me-1"></i>Rental Details</a></li>
            <li class="nav-item"><a class="nav-link" href="yearlyreport.php"><i class="bi bi-calendar3 me-1"></i>Yearly Report</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_manage_complaints.php"><i class="bi bi-chat-dots me-1"></i>View Complaints</a></li>
            <li class="nav-item"><a class="nav-link" href="viewpayments.php"><i class="bi bi-credit-card-2-back me-1"></i>View Payments</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-box-arrow-left me-1"></i>Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <div class="card shadow-sm p-4 mb-4">
        <h3 class="text-center text-primary mb-0">
            <i class="bi bi-house-door-fill me-2"></i>All House/Plot Data
        </h3>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th><i class="bi bi-house-door-fill"></i> House/Plot No</th>
                    <th><i class="bi bi-house"></i>Type</th>
                    <th><i class="bi bi-person-fill"></i> Owner Name</th>
                    <th><i class="bi bi-tools"></i> Development</th>
                    <th><i class="bi bi-person-workspace"></i> Chair Rent</th>
                    <th><i class="bi bi-geo-alt"></i> Mehsul</th>
                    <th><i class="bi bi-houses"></i> Cop Rent</th>
                    <th><i class="bi bi-arrow-left-right"></i> Transfer Fee</th>
                    <th><i class="bi bi-exclamation-circle"></i> Maintenance Due</th>
                    <th><i class="bi bi-wallet2"></i> Remaining</th>
                    <th><i class="bi bi-pencil"></i> Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            while ($row = $result->fetch_assoc()) {
                $house_no = $row['house_no'];

                $stmt = $connect->prepare("SELECT month, year FROM maintenance WHERE house_no = ? AND paymenttype='Maintenance' ORDER BY m_date DESC LIMIT 1");
                $stmt->bind_param("s", $house_no);
                $stmt->execute();
                $res = $stmt->get_result();
                $entry = $res->fetch_assoc();
                $stmt->close();

                $maintenance_due = 0;
$rate = 500; // default for house

if (isset($row['house_details']) && strtolower($row['house_details']) === 'plot') {
    $rate = 250;
}

if (!empty($entry['month']) && !empty($entry['year'])) {
    try {
        $lastDate = new DateTime("{$entry['month']} {$entry['year']}");
        $now = new DateTime();
        $diff = $now->diff($lastDate);
        $monthsGap = ($diff->y * 12) + $diff->m;
        $maintenance_due = $monthsGap * $rate;
    } catch (Exception $e) {
        $maintenance_due = 0;
    }
}


                $expected_total =
                    floatval($row['development']) +
                    floatval($row['chairrent']) +
                    floatval($row['mehsul']) +
                    floatval($row['coprent']) +
                    floatval($row['transferfee']) +
                    $maintenance_due;

                $remaining = $expected_total;
            ?>
                <tr>

                    <td><?= htmlspecialchars($house_no) ?></td>
                    <td><?= htmlspecialchars($row['house_details']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><span class="badge bg-secondary">₹<?= $row['development'] ?></span></td>
                    <td><span class="badge bg-secondary">₹<?= $row['chairrent'] ?></span></td>
                    <td><span class="badge bg-secondary">₹<?= $row['mehsul'] ?></span></td>
                    <td><span class="badge bg-secondary">₹<?= $row['coprent'] ?></span></td>
                    <td><span class="badge bg-secondary">₹<?= $row['transferfee'] ?></span></td>
                    <td><span class="badge bg-info">₹<?= $maintenance_due ?></span></td>
                    <td>
                        <span class="badge <?= $remaining > 0 ? 'badge-soft-danger' : 'badge-soft-success' ?>">
                            ₹<?= number_format($remaining, 2) ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="update.php" class="d-inline">
                            <input type="hidden" name="house_no" value="<?= htmlspecialchars($house_no) ?>">
                            <button type="submit" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

    </div>
    <form method="POST" action="export.php" class="mt-3" style="text-align: center;">
        <input type="hidden" name="export_data" value="all">
        <button type="submit" class="btn btn-success">
            <i class="bi bi-file-earmark-arrow-down"></i> Export All Data
        </button>
    </form>
    <?php else: ?>
        <div class="alert alert-warning text-center mt-4">
            <i class="bi bi-info-circle"></i> No records found.
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $connect->close(); ?>
