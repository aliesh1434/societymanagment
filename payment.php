<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: Login.php");
    exit();
}

$email = $_SESSION['email'];

$connect = new mysqli("localhost", "root", "", "sds");
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

// Get house_no
$stmt = $connect->prepare("SELECT house_no FROM registration WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$house_no = $user['house_no'];
$stmt->close();

$uploadSuccess = $uploadError = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['payment_screenshot'])) {
    $targetDir = "payment_uploads/";
    $fileName = basename($_FILES["payment_screenshot"]["name"]);
    $targetFile = $targetDir . time() . "_" . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($imageFileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["payment_screenshot"]["tmp_name"], $targetFile)) {
            $filename = basename($targetFile);
            $stmt = $connect->prepare("INSERT INTO payment_confirmation (house_no, filename) VALUES (?, ?)");
            $stmt->bind_param("ss", $house_no, $filename);
            if ($stmt->execute()) {
                $uploadSuccess = "Screenshot uploaded successfully!";
            } else {
                $uploadError = "Failed to save to database.";
            }
            $stmt->close();
        } else {
            $uploadError = "Error uploading file.";
        }
    } else {
        $uploadError = "Invalid file type. Only JPG, JPEG, PNG, GIF allowed.";
    }
}

$connect->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment - SDS</title>
    <link rel="icon" href="home.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 fixed-top">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="SDS Logo.png" alt="Logo" height="40" class="me-2">
            
        </a>
        <div class="ms-auto">
            <a href="viewmypayments.php" class="btn btn-outline-light me-2"><i class="bi bi-receipt"></i> My Payments</a>
            <a href="Index.php" class="btn btn-outline-light"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container mt-5">
        <h2 class="mb-4 text-center"><i class="bi bi-currency-rupee"></i> Scan & Pay Your Bills Online</h2>

        <div class="row justify-content-center mb-4">
            <div class="col-md-4 text-center">
                <img src="sampleqr.png" class="img-fluid" alt="Scan to Pay">
                <p class="mt-2">Scan this QR code using any UPI app to make your payment.</p>
            </div>
        </div>


        <!-- Upload Form -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="post" enctype="multipart/form-data" class="p-4 border rounded bg-light shadow-sm">
                    <h5><i class="bi bi-upload"></i> Upload Screenshot of Payment</h5>
                    <div class="mb-3 mt-3">
                        <input type="file" class="form-control" name="payment_screenshot" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-cloud-arrow-up"></i> Upload</button><br><hr>

                    <div class="card-footer text-center">
                            <a href="Userdashboard.php" class="btn btn-outline-primary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
                    </div>
                </form>
                

                <?php if ($uploadSuccess): ?>
                    <div class="alert alert-success mt-3"><i class="bi bi-check-circle"></i> <?= $uploadSuccess ?></div>
                <?php elseif ($uploadError): ?>
                    <div class="alert alert-danger mt-3"><i class="bi bi-x-circle"></i> <?= $uploadError ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
