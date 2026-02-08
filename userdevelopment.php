<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: Login.php");
    exit();
}

$user_email = $_SESSION['email'];
$connect = mysqli_connect("localhost", "root", "", "sds");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

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

if (!$house_no) {
    die("Error: No house number found for this user.");
}

$query = "SELECT * FROM maintenance WHERE house_no = ? AND paymenttype = 'Development'";
$stmt = $connect->prepare($query);
$stmt->bind_param("s", $house_no);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SDS | Development Charges Record</title>
    <link rel="icon" type="image/png" href="home.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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

<!-- Page Heading -->
<div class="container mt-4">
    <h3 class="text-center mb-4">
        <i class="bi bi-hammer"></i> Development Charges Record
    </h3>

    <?php
    if ($result->num_rows > 0) {
        $totalAmount = 0;
        echo '
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Sr No.</th>
                        <th><i class="bi bi-receipt"></i> Receipt No.</th>
                        <th><i class="bi bi-calendar-date"></i> Date</th>
                        <th><i class="bi bi-house-door"></i> House No.</th>
                        <th><i class="bi bi-credit-card"></i> Payment Type</th>
                        <th><i class="bi bi-cash-stack"></i> Amount Type</th>
                        <th><i class="bi bi-calendar-event"></i> Month</th>
                        <th><i class="bi bi-calendar3"></i> Year</th>
                        <th><i class="bi bi-currency-rupee"></i> Amount</th>
                    </tr>
                </thead>
                <tbody>';
        
        $cnt = 1;
        while ($row = $result->fetch_object()) {
            echo '
                <tr>
                    <td>' . $cnt . '</td>
                    <td>' . htmlspecialchars($row->receipt_no) . '</td>
                    <td>' . htmlspecialchars($row->m_date) . '</td>
                    <td>' . htmlspecialchars($row->house_no) . '</td>
                    <td>' . htmlspecialchars($row->ammounttype) . '</td>
                    <td>' . htmlspecialchars($row->paymenttype) . '</td>
                    <td>' . htmlspecialchars($row->month) . '</td>
                    <td>' . htmlspecialchars($row->year) . '</td>
                    <td>₹' . number_format($row->ammount, 2) . '</td>
                </tr>';
            $totalAmount += $row->ammount;
            $cnt++;
        }

        echo '
                </tbody>
                <tfoot>
                    <tr class="table-secondary">
                        <td colspan="8" class="text-end"><strong>Total Amount:</strong></td>
                        <td><strong>₹' . number_format($totalAmount, 2) . '</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>';
    } else {
        echo '<div class="alert alert-warning text-center">No development charges found for your house.</div>';
    }

    mysqli_close($connect);
    ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
