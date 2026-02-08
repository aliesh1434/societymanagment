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

$query = "SELECT * FROM religious WHERE house_no = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("s", $house_no);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>SDS | Religious Fund Record</title>
    <meta charset="UTF-8">
    <link rel="icon" href="home.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
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

    <div class="container mt-5">
        <h2 class="text-center mb-4"><i class="bi bi-journal-text"></i> Religious Fund Record</h2>

        <?php
        if (isset($result)) {
            if ($result->num_rows > 0) {
                $totalAmount = 0;
                echo "
                <div class='table-responsive'>
                    <table class='table table-bordered table-hover align-middle text-center'>
                        <thead class='table-dark'>
                            <tr>
                                <th>Sr No.</th>
                                <th><i class='bi bi-person-circle'></i> Name</th>
                                <th><i class='bi bi-house-door'></i> House No.</th>
                                <th><i class='bi bi-stars'></i> Festival</th>
                                <th><i class='bi bi-calendar-event'></i> Year</th>
                                <th><i class='bi bi-cash-coin'></i> Amount</th>
                            </tr>
                        </thead>
                        <tbody>";
                $cnt = 1;
                while ($row = $result->fetch_object()) {
                    echo "
                    <tr>
                        <td>{$cnt}</td>
                        <td>" . htmlspecialchars($row->name) . "</td>
                        <td>" . htmlspecialchars($row->house_no) . "</td>
                        <td>" . htmlspecialchars($row->festival) . "</td>
                        <td>" . htmlspecialchars($row->year) . "</td>
                        <td>₹" . number_format($row->ammount, 2) . "</td>
                    </tr>";
                    $totalAmount += $row->ammount;
                    $cnt++;
                }
                echo "</tbody>
                        <tfoot class='table-light fw-bold'>
                            <tr>
                                <td colspan='5' class='text-end'>Total Amount:</td>
                                <td>₹" . number_format($totalAmount, 2) . "</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>";
            } else {
                echo "<div class='alert alert-info text-center'>No records found for your house.</div>";
            }
            mysqli_close($connect);
        }
        ?>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
