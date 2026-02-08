<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: Login.php");
    exit();
}

$email = $_SESSION['email'];
$connect = mysqli_connect("localhost", "root", "", "sds");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch user house number
$query = "SELECT house_no FROM registration WHERE email = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$house_no = $user['house_no'] ?? '';
$stmt->close();

if (!$house_no) die("Error: No house number found for this user.");

// Fetch name and house_details
$stmt = $connect->prepare("SELECT name, house_details FROM house_plot WHERE house_no = ?");
$stmt->bind_param("s", $house_no);
$stmt->execute();
$result = $stmt->get_result();
$house_data = $result->fetch_assoc();
$stmt->close();

$name = $house_data['name'] ?? 'N/A';
$house_details_raw = strtolower($house_data['house_details'] ?? '');
$house_details = ($house_details_raw === 'plot') ? 'Plot' : 'House';

// Determine maintenance rate
$rate = ($house_details_raw === 'plot') ? 250 : 500;

// Get total amounts
function get_total($conn, $query) {
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    return $row[array_key_first($row)] ?? 0;
}

$totalReligious     = get_total($connect, "SELECT SUM(ammount) AS total_religious FROM religious WHERE house_no = '$house_no'");
$totalMaintenance   = get_total($connect, "SELECT SUM(ammount) AS total_maintenance FROM maintenance WHERE house_no = '$house_no' AND paymenttype = 'Maintenance'");
$totalDevelopment   = get_total($connect, "SELECT SUM(ammount) AS total_development FROM maintenance WHERE house_no = '$house_no' AND paymenttype = 'Development'");
$totalTransferFee   = get_total($connect, "SELECT SUM(ammount) AS total_transferfee FROM maintenance WHERE house_no = '$house_no' AND paymenttype = 'TransferFee'");
$totalRebate        = get_total($connect, "SELECT SUM(ammount) AS total_rebate FROM maintenance WHERE house_no = '$house_no' AND paymenttype = 'Rebate'");
$totalOtherDue      = get_total($connect, "SELECT (IFNULL(development,0)+ IFNULL(transferfee,0) + IFNULL(chairrent,0) + IFNULL(coprent,0) + IFNULL(mehsul,0)) AS total_otherdue FROM house_plot WHERE house_no = '$house_no'");

// Get last maintenance entry
$stmt = $connect->prepare("SELECT m_date, month, year FROM maintenance WHERE house_no = ? AND paymenttype='Maintenance' ORDER BY m_date DESC LIMIT 1");
$stmt->bind_param("s", $house_no);
$stmt->execute();
$result = $stmt->get_result();
$entry = $result->fetch_assoc();
$stmt->close();

$lastMonth = $entry['month'] ?? 'N/A';
$lastYear = $entry['year'] ?? 'N/A';
$lastDateFormatted = 'N/A';
$monthsGap = 0;
$maintenance_due = 0;

if (!empty($entry['month']) && !empty($entry['year'])) {
    try {
        $lastDate = new DateTime("{$entry['month']} {$entry['year']}");
        $now = new DateTime();
        $diff = $now->diff($lastDate);
        $monthsGap = ($diff->y * 12) + $diff->m;
        $lastDateFormatted = $lastDate->format('F Y');
        $maintenance_due = $monthsGap * $rate;
    } catch (Exception $e) {
        $maintenance_due = 0;
    }
}

$totalmaintenenceDue = $maintenance_due;
$totalDue = $totalmaintenenceDue + $totalOtherDue;

mysqli_close($connect);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SDS | Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="home.png">

    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        .nav-tile {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            border-radius: 1rem;
            height: 120px;
            text-decoration: none;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .nav-tile:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }
        .nav-tile i {
            font-size: 2rem;
            margin-bottom: 5px;
        }
        .nav-tile div {
            font-size: 0.95rem;
            font-weight: 500;
        }
        @media (max-width: 576px) {
            .nav-tile {
                height: 100px;
                padding: 15px;
            }
            .nav-tile i {
                font-size: 1.5rem;
            }
            .nav-tile div {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"><img src="SDS Logo.jpg" alt="Logo" height="40"></a>
        <div class="ms-auto">
            <a class="btn btn-outline-light" href="Index.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<!-- Main Container -->
<div class="container mt-5 pt-5">

    <!-- Welcome Section -->
    <div class="text-center mb-5">
        <h2 class="text-primary"><u>Shiv Drashti Row House</u></h2>
        <h4 class="text-secondary">Welcome, <?= htmlspecialchars($name); ?>!</h4>
    </div>

    <!-- Top Navigation Grid -->
    <div class="row row-cols-2 row-cols-md-4 g-3 mb-4 text-center">
        <div class="col"><a href="profile.php" class="nav-tile bg-info text-white"><i class="bi bi-person-circle"></i><div>Profile</div></a></div>
        <div class="col"><a href="Usernotice.php" class="nav-tile bg-primary text-white"><i class="bi bi-bell"></i><div>Notice</div></a></div>
        <div class="col"><a href="payment.php" class="nav-tile bg-dark text-white"><i class="bi bi-credit-card"></i><div>Payment</div></a></div>
        <div class="col"><a href="complain.php" class="nav-tile bg-danger text-white"><i class="bi bi-exclamation-octagon"></i><div>Complaint Box</div></a></div>
        <div class="col"><a href="userrental.php" class="nav-tile bg-success text-white"><i class="bi bi-house"></i><div>Rental Details</div></a></div>
    </div>

    <!-- Financial Summary Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow rounded-4 p-4 bg-light">
                <div class="card-body">
                    <h4 class="text-success mb-4 fw-bold"><i class="bi bi-bar-chart-line me-2"></i> Financial Summary</h4>
                    <p class="text-muted mb-4">Track your contributions and pending balances across various payment categories.</p>
                    <div class="row row-cols-1 row-cols-md-2 g-4">
                        <!-- Individual Cards -->
                        <?php $funds = [
                            ['Total Maintenance Paid', 'bi-building-check', 'text-success', $totalMaintenance, 'success'],
                            ['Total Development Paid', 'bi-hammer', 'text-warning', $totalDevelopment, 'warning'],
                            ['Total Transfer Fee', 'bi-arrow-left-right', 'text-info', $totalTransferFee, 'info'],
                            ['Religious Fund', 'bi-bank', 'text-secondary', $totalReligious, 'secondary'],
                            ['Rebate Received', 'bi-cash-coin', 'text-dark', $totalRebate, 'dark'],
                        ];
                        foreach ($funds as [$label, $icon, $textColor, $value, $border]) : ?>
                            <div class="col">
                                <div class="p-3 bg-white rounded shadow-sm h-100 border-start border-5 border-<?= $border; ?>">
                                    <p class="mb-1 text-muted"><i class="bi <?= $icon; ?> me-1 <?= $textColor; ?>"></i> <?= $label; ?></p>
                                    <h5 class="fw-bold <?= $textColor; ?>">₹ <span class="counter" data-target="<?= $value; ?>">0</span></h5>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="col">
                            <div class="p-3 bg-white rounded shadow-sm h-100 border-start border-5 border-primary">
                                <p class="mb-1 text-muted"><i class="bi bi-calendar-check me-1 text-primary"></i> Last Maintenance Entry</p>
                                <h5 class="text-primary fw-bold"><?= htmlspecialchars($lastDateFormatted); ?></h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-3 bg-white rounded shadow-sm h-100 border-start border-5 border-info">
                                <p class="mb-1 text-muted"><i class="bi bi-hourglass-split me-1 text-info"></i> Months Since Last Entry</p>
                                <h5 class="text-info fw-bold"><span class="counter" data-target="<?= $monthsGap; ?>">0</span> Months</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-3 bg-white rounded shadow-sm h-100 border-start border-5 border-danger">
                                <p class="mb-1 text-muted"><i class="bi bi-exclamation-triangle-fill me-1 text-danger"></i> Total Due</p>
                                <h5 class="text-danger fw-bold">₹ <span class="counter" data-target="<?= $totalDue; ?>">0</span></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Funds Payment History -->
    <div class="p-4 bg-white rounded-4 shadow d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-4">
        <div class="d-flex align-items-start gap-3">
            <i class="bi bi-clock-history fs-2 text-primary"></i>
            <div>
                <h4 class="fw-bold mb-1">Funds Payment History</h4>
                <p class="mb-0 text-muted">View your record of payments across all funds</p>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-3 justify-content-center">
            <a href="Usermaintenance.php" class="nav-tile bg-success text-white text-decoration-none" style="min-width: 120px;"><i class="bi bi-tools fs-3"></i><div class="mt-2 fw-medium">Maintenance</div></a>
            <a href="Userdevelopment.php" class="nav-tile bg-warning text-dark text-decoration-none" style="min-width: 120px;"><i class="bi bi-hammer fs-3"></i><div class="mt-2 fw-medium">Development</div></a>
            <a href="Usertransferfee.php" class="nav-tile bg-info text-dark text-decoration-none" style="min-width: 120px;"><i class="bi bi-currency-exchange fs-3"></i><div class="mt-2 fw-medium">Transfer Fee</div></a>
            <a href="Userreligious.php" class="nav-tile bg-secondary text-white text-decoration-none" style="min-width: 120px;"><i class="bi bi-bank2 fs-3"></i><div class="mt-2 fw-medium">Religious</div></a>
        </div>
    </div>

</div>

<!-- CountUp Script -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const updateCount = () => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;
            const increment = target / 100;
            if (count < target) {
                counter.innerText = Math.ceil(count + increment);
                setTimeout(updateCount, 15);
            } else {
                counter.innerText = target.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
        };
        updateCount();
    });
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>