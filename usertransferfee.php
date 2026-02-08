<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: Login.php");
    exit();
}

$user_email = $_SESSION['email'];
$connect = mysqli_connect("localhost", "root", "", "sds");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch user's house number
$query = "SELECT house_no FROM registration WHERE email = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($house_no);

if (!$stmt->fetch()) {
    echo "User not found.";
    exit();
}
$stmt->close();

// Fetch transfer fee data
$query = "SELECT * FROM maintenance WHERE house_no = ? AND paymenttype = 'Transferfee'";
$stmt = $connect->prepare($query);
$stmt->bind_param("s", $house_no);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>SDS | Transfer Fee Record</title>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="home.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
    
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="SDS Logo.jpg" height="40" alt="Logo">
        </a>
        <div class="ms-auto">
            <a href="Userdashboard.php" class="btn btn-outline-light me-2">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="Index.php" class="btn btn-outline-light">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

    <div class="container mt-4">
        <h3 class="mb-3 text-center"><i class="bi bi-file-text"></i> Transfer Fee Record</h3>

        <?php
        if ($result->num_rows > 0) {
            $totalAmount = 0;
        ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th><i class="bi bi-receipt"></i> Receipt No.</th>
                        <th><i class="bi bi-calendar-event"></i> Date</th>
                        <th><i class="bi bi-house"></i> House No.</th>
                        <th><i class="bi bi-tag"></i> Amount Type</th>
                        <th><i class="bi bi-credit-card-2-front"></i> Payment Type</th>
                        <th><i class="bi bi-calendar-month"></i> Month</th>
                        <th><i class="bi bi-calendar"></i> Year</th>
                        <th><i class="bi bi-currency-rupee"></i> Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $cnt = 1;
                while ($row = $result->fetch_object()) {
                    echo "<tr>
                        <td>$cnt</td>
                        <td>" . htmlspecialchars($row->receipt_no) . "</td>
                        <td>" . htmlspecialchars($row->m_date) . "</td>
                        <td>" . htmlspecialchars($row->house_no) . "</td>
                        <td>" . htmlspecialchars($row->ammounttype) . "</td>
                        <td>" . htmlspecialchars($row->paymenttype) . "</td>
                        <td>" . htmlspecialchars($row->month) . "</td>
                        <td>" . htmlspecialchars($row->year) . "</td>
                        <td>" . number_format($row->ammount, 2) . "</td>
                    </tr>";
                    $totalAmount += $row->ammount;
                    $cnt++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr class="table-secondary">
                        <td colspan="8" class="text-end"><strong>Total Amount:</strong></td>
                        <td><strong>₹<?= number_format($totalAmount, 2); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php } else { ?>
            <div class="alert alert-warning text-center" role="alert">
                <i class="bi bi-exclamation-triangle"></i> No Transfer Fee Data Found!
            </div>
        <?php } ?>
    </div>

    <!-- Bootstrap JS (optional but recommended) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
