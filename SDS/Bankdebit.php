<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Establish database connection
    $connect = mysqli_connect("localhost", "root", "", "sds");

    if (!$connect) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Retrieve and sanitize form inputs
    $b_detail = 'BankDebit';
    $ammount = isset($_POST['ammount']) ? trim($_POST['ammount']) : '';
    $b_date = isset($_POST['b_date']) ? trim($_POST['b_date']) : '';

    // Validate inputs
    $errors = [];
    if (empty($b_detail)) {
        $errors[] = "Transaction details are required.";
    }

    if (empty($errors)) {
        // Insert data into the database
        $query = "INSERT INTO bankdebit (b_detail, ammount, b_date) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "sss", $b_detail, $ammount, $b_date);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Data added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding data: " . mysqli_error($connect) . "');</script>";
        }

        mysqli_stmt_close($stmt);
    } else {
        // Display errors
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    }

    mysqli_close($connect);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Debit</title>
    <link rel="stylesheet" href="sdscss.css">
</head>
<body>
    <header class="header">
        <div class="logo">
            <img src="SDS Logo.jpg" height="40px" alt="Society Dashboard">
        </div>
        <nav class="nav-links">
            <a href="Dashboard.php">Dashboard</a>
            <a href="maintenance.php">Maintenance</a>
            <a href="Religious.php">Religious Fund</a>
            <a href="Balance.php">Balance</a>
            <a href="Receipt.php">Receipts</a>
            <a href="Index.php">Logout</a>
        </nav>
    </header>

    <form method="POST" action="">
        <h2><u>Bank Debit</u></h2>

        <!-- <label for="b_detail"><b>Details:</b></label>
        <input type="text" id="b_detail" name="b_detail" placeholder="Enter Details" required><br><br> -->

        <label for="ammount"><b>Debit Amount:</b></label>
        <input type="text" id="ammount" name="ammount" step="0.01" placeholder="Enter Amount" required><br><br>

        <label for="b_date"><b>b_date:</b></label>
        <input type="date" id="b_date" name="b_date" placeholder="Enter b_date" required><br><br>

        <button type="submit">Submit</button>
        <button type="button" onclick="window.location.href='Showbankdebit.php'">Show Data</button>
    </form>
</body>
</html>
