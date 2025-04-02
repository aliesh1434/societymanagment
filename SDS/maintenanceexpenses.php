<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Establish database connection
    $connect = mysqli_connect("localhost", "root", "", "sds");

    if (!$connect) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Retrieve and sanitize form inputs
    $t_detail = isset($_POST['t_detail']) ? trim($_POST['t_detail']) : '';
    $ammount = isset($_POST['ammount']) ? trim($_POST['ammount']) : '';
    
    $year = isset($_POST['year']) ? trim($_POST['year']) : '';

    // Validate inputs
    $errors = [];
    if (empty($t_detail)) {
        $errors[] = "Transaction details are required.";
    }

    if (empty($errors)) {
        // Insert data into the database
        $query = "INSERT INTO maintenancetransactions (t_detail, ammount, year) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "sss", $t_detail, $ammount, $year);

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
    <title>Maintenance Expenses</title>
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
        <h2><u>Maintenance Expenses</u></h2>

        <label for="t_detail"><b>Transaction Details:</b></label>
        <input type="text" id="t_detail" name="t_detail" placeholder="Enter Details" required><br><br>

        <label for="ammount"><b>Transaction Amount:</b></label>
        <input type="text" id="ammount" name="ammount" step="0.01" placeholder="Enter Amount" required><br><br>

        <label for="year"><b>Year:</b></label>
        <input type="text" id="year" name="year" placeholder="Enter Year (e.g., 2013-2014)" required><br><br>

        <button type="submit">Submit</button>
        <button type="button" onclick="window.location.href='Showdatamaintenanceexpenses.php'">Show Data</button>
    </form>
</body>
</html>
