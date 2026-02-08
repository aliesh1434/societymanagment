<?php
// Connect to database
$connect = mysqli_connect("localhost", "root", "", "sds");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_notice'])) {
    $title = mysqli_real_escape_string($connect, $_POST['title']);
    $content = mysqli_real_escape_string($connect, $_POST['content']);

    $query = "INSERT INTO notices (title, content) VALUES ('$title', '$content')";
    if (mysqli_query($connect, $query)) {
        header("Location: dashboard.php"); // Redirect after success
        exit;
    } else {
        $error = "Error: " . mysqli_error($connect);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SDS | Add Notice</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="home.png"> 

    <style>
        body {
            background-image: url('SDSBG.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .notice-card {
            max-width: 600px;
            margin: 20px auto;
            background-color: rgba(255,255,255,0.95);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .edit-form {
            display: none;
            margin-top: 15px;
        }
        .custom-add-btn {
            background: linear-gradient(135deg, #28a745, #218838);
            color: #fff;
            font-weight: bold;
            padding: 12px 25px;
            font-size: 1.1rem;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            text-transform: uppercase;
        }
        .custom-add-btn:hover {
            background: linear-gradient(135deg, #218838, #1e7e34);
            transform: translateY(-2px);
        }
        .nav-link:hover {
            font-weight: bold;
            color: #ffc107 !important;
        }
          body {
    padding-top: 90px; /* Extra space for desktop fixed navbar */
  }

  @media (max-width: 768px) {
    body {
      padding-top: 100px; /* Extra space for mobile expanded navbar */
    }
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

<!-- Main Section -->
<div class="container mt-5">
    
    <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" class="shadow p-4 rounded bg-white" style="max-width: 600px; margin: auto;">
    <h2 class="text-center text-primary mb-4">
        <i class="bi bi-plus-circle"></i> Add New Notice
    </h2>

    <div class="mb-3">
        <label class="form-label"><i class="bi bi-card-heading me-1"></i> Notice Title</label>
        <input type="text" name="title" class="form-control" required placeholder="Enter notice title">
    </div>

    <div class="mb-3">
        <label class="form-label"><i class="bi bi-body-text me-1"></i> Notice Content</label>
        <textarea name="content" class="form-control" rows="5" required placeholder="Enter full notice content"></textarea>
    </div>

    <div class="d-flex justify-content-between">
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left-circle me-1"></i> Back to Dashboard
        </a>
        <button type="submit" name="add_notice" class="btn btn-success">
            <i class="bi bi-send-check me-1"></i> Post Notice
        </button>
    </div>
</form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
