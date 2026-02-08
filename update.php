<?php
$connect = new mysqli("localhost", "root", "", "sds");
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

$house_no = $_GET['house_no'] ?? $_POST['house_no'] ?? null;
$error = '';
$user = [];

if (!$house_no) {
    $error = "House number is missing.";
} else {
    // Fetch house and registration data
    $stmt = $connect->prepare("
        SELECT hp.house_no, hp.house_details, hp.name, hp.development, hp.chairrent, 
               hp.mehsul, hp.coprent, hp.transferfee,
               reg.mob, reg.email
        FROM house_plot hp
        LEFT JOIN registration reg ON hp.house_no = reg.house_no
        WHERE hp.house_no = ?
    ");
    $stmt->bind_param("s", $house_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Handle update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        $updateHouse = $connect->prepare("
            UPDATE house_plot 
            SET house_details = ?, name = ?, development = ?, chairrent = ?, mehsul = ?, coprent = ?, transferfee = ?
            WHERE house_no = ?
        ");
        $updateHouse->bind_param(
            "ssssssss",
            $_POST['house_details'],
            $_POST['name'],
            $_POST['development'],
            $_POST['chairrent'],
            $_POST['mehsul'],
            $_POST['coprent'],
            $_POST['transferfee'],
            $house_no
        );

        $checkReg = $connect->prepare("SELECT house_no FROM registration WHERE house_no = ?");
        $checkReg->bind_param("s", $house_no);
        $checkReg->execute();
        $regResult = $checkReg->get_result();

        if ($regResult->num_rows > 0) {
            $updateReg = $connect->prepare("UPDATE registration SET mob = ?, email = ? WHERE house_no = ?");
            $updateReg->bind_param("sss", $_POST['mob'], $_POST['email'], $house_no);
        } else {
            $updateReg = $connect->prepare("INSERT INTO registration (house_no, mob, email) VALUES (?, ?, ?)");
            $updateReg->bind_param("sss", $house_no, $_POST['mob'], $_POST['email']);
        }

        if ($updateHouse->execute() && $updateReg->execute()) {
    $showSuccess = true;
} else {
    $error = "Failed to update details.";
}

    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>SDS | Update Details</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="home.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
          body {
    padding-top: 90px; /* Extra space for desktop fixed navbar */
  }

  @media (max-width: 768px) {
    body {
      padding-top: 100px; /* Extra space for mobile expanded navbar */
    }
  }
        .tick-svg {
    stroke-dasharray: 1000;
    stroke-dashoffset: 1000;
    animation: draw 1s ease-out forwards;
}
@keyframes draw {
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

<!-- Form Section -->
<div class="container my-5">
    <div class="card shadow-sm p-4">
        <h2 class="mb-4 text-center"><i class="bi bi-pencil-square"></i> Update House Details</h2>
        <?php if (!empty($showSuccess)): ?>
    <div class='login-feedback text-center'>
        <div class='tick-box tick-success d-inline-block'>
            <svg class='tick-svg' viewBox='0 0 52 52' style="width: 80px; height: 80px;">
                <circle class='tick-circle' cx='26' cy='26' r='25' fill='none' stroke='#28a745' stroke-width='4'/>
                <path class='tick-check' d='M14,27 L22,35 L38,19' fill='none' stroke='#28a745' stroke-width='4' stroke-linecap='round' stroke-linejoin='round'/>
            </svg>
            <p class='tick-message text-success fw-bold mt-2'>Details Updated Successfully!</p>
        </div>
        <script>
            setTimeout(() => {
                window.location.href = 'housedetails.php';
            }, 1500);
        </script>
    </div>
<?php endif; ?>


        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="row g-3">
            <div class="col-md-6">
                <label class="form-label"><i class="bi bi-house-door"></i> House No</label>
                <input type="text" class="form-control" name="house_no" value="<?= htmlspecialchars($house_no) ?>" readonly>
                </div>

            <input type="hidden" name="house_no" value="<?= htmlspecialchars($house_no) ?>">

            <div class="col-md-6">
                <label class="form-label"><i class="bi bi-info-square"></i> House Details</label>
                <input type="text" class="form-control" name="house_details" value="<?= htmlspecialchars($user['house_details'] ?? '') ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label"><i class="bi bi-person-circle"></i> Name</label>
                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label"><i class="bi bi-telephone"></i> Contact No</label>
                <input type="text" class="form-control" name="mob" value="<?= htmlspecialchars($user['mob'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label"><i class="bi bi-envelope"></i> Email</label>
                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label"><i class="bi bi-building"></i> Development</label>
                <input type="text" class="form-control" name="development" value="<?= htmlspecialchars($user['development'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label"><i class="bi bi-chair"></i> Chair Rent</label>
                <input type="text" class="form-control" name="chairrent" value="<?= htmlspecialchars($user['chairrent'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label"><i class="bi bi-cash"></i> Mehsul</label>
                <input type="text" class="form-control" name="mehsul" value="<?= htmlspecialchars($user['mehsul'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label"><i class="bi bi-shop"></i> COP Rent</label>
                <input type="text" class="form-control" name="coprent" value="<?= htmlspecialchars($user['coprent'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label"><i class="bi bi-arrow-left-right"></i> Transfer Fee</label>
                <input type="text" class="form-control" name="transferfee" value="<?= htmlspecialchars($user['transferfee'] ?? '') ?>">
            </div>

            <div class="col-12 text-center">
                <button type="submit" name="update" class="btn btn-primary px-4"><i class="bi bi-check-circle"></i> Update</button>
                <a href="housedetails.php" class="btn btn-secondary px-4"><i class="bi bi-x-circle"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $connect->close(); ?>
