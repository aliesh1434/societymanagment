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

// Get house number
$stmt = $connect->prepare("SELECT house_no FROM registration WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$house_no = $user['house_no'] ?? 'N/A';
$stmt->close();

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (!empty($subject) && !empty($message)) {
        $stmt = $connect->prepare("INSERT INTO complaints (house_no, subject, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $house_no, $subject, $message);

        if ($stmt->execute()) {
            $success = "Complaint submitted successfully!";
        } else {
            $error = "Error submitting complaint.";
        }

        $stmt->close();
    } else {
        $error = "All fields are required.";
    }
}

$connect->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>SDS | My Complaints History</title>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="home.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <style>
     
        .header {
            background: #343a40;
            padding: 15px;
            color: white;
        }
        .logo img {
            height: 40px;
        }
        .nav-links a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
        }
        .container {
            max-width: 600px;
            background: white;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="SDS Logo.jpg" height="40" alt="Logo">
        </a>
        <div class="ms-auto">
            <a href="view_my_complaints.php" class ="btn btn-outline-light">
                <i class="bi bi-chat-dots"></i> My Complaints
                </a>
            <a href="Index.php" class="btn btn-outline-light">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>


<div class="container">
    <h3 class="mb-4"><i class="bi bi-pencil-square"></i> Complaint Box</h3>

    <?php if ($success): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="house_no" class="form-label">House No.</label>
            <input type="text" class="form-control" name="house_no" id="house_no" value="<?= htmlspecialchars($house_no) ?>" readonly>
        </div>

        <div class="mb-3">
            <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="subject" id="subject" required>
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">Complaint <span class="text-danger">*</span></label>
            <textarea class="form-control" name="message" id="message" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send"></i> Submit Complaint</button><br><hr>
<div class="card-footer text-center">
        <a href="Userdashboard.php" class="btn btn-outline-primary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    </div>    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
