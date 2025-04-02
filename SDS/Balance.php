<?php
// Establish database connection
$connect = new mysqli("localhost", "root", "", "sds");

// Check for successful connection
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

// Fetch sums from the database
$queryReligious = "SELECT SUM(ammount) AS total_religious FROM religious";
$queryexpensereligious = "SELECT SUM(ammount) AS total_expensereligious FROM transactions";
$querymaintenance = "SELECT SUM(ammount) AS total_maintenance FROM maintenance";
$querymaintenanceexpenses = "SELECT SUM(ammount) AS total_maintenanceexpenses FROM maintenancetransactions";
$querybanktransfer = "SELECT SUM(ammount) AS total_banktransfer FROM maintenance WHERE ammounttype='Banktransfer'";
$querybank = "SELECT SUM(ammount) AS total_bank FROM bankdebit"; 

// Execute queries and check for errors
$resultReligious = $connect->query($queryReligious);
$resultexpensereligious = $connect->query($queryexpensereligious);
$resultmaintenance = $connect->query($querymaintenance);
$resultmaintenanceexpenses = $connect->query($querymaintenanceexpenses);
$resultbanktransfer = $connect->query($querybanktransfer);
$resultbank = $connect->query($querybank);

if (!$resultReligious || !$resultexpensereligious || !$resultmaintenance || !$resultmaintenanceexpenses || !$resultbanktransfer) {
    die("Query failed: " . $connect->error);
}

// Extract data
$totalReligious = $resultReligious->fetch_assoc()['total_religious'] ?? 0;
$totalexpensereligious = $resultexpensereligious->fetch_assoc()['total_expensereligious'] ?? 0;
$totalmaintenance = $resultmaintenance->fetch_assoc()['total_maintenance'] ?? 0;
$totalmaintenanceexpenses = $resultmaintenanceexpenses->fetch_assoc()['total_maintenanceexpenses'] ?? 0;
$totalbanktransfer = $resultbanktransfer->fetch_assoc()['total_banktransfer'] ?? 0;
$totalbank = $resultbank->fetch_assoc()['total_bank'] ?? 0;
// Calculate cash on hand
$cashOnHandReligious = $totalReligious - $totalexpensereligious;
$cashOnHandMaintenance = $totalmaintenance - $totalmaintenanceexpenses - $totalbanktransfer + $totalbank;
$total_bank_transfer = $totalbanktransfer - $totalbank;
// Close the connection
$connect->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance</title>
    <link rel="stylesheet" href="sdscss.css">
    <style>
    .row {
        display: flex;
        justify-content: space-between; /* Space between the two boxes */
        align-items: flex-start; /* Align items at the top */
        margin: 20px 0; /* Add some vertical spacing */
    }

    .col {
        flex: 1; /* Make both columns take equal space */
        margin: 0 10px; /* Add spacing between the columns */
        padding: 20px;
        background-color: rgba(255, 255, 255, 0.8); /* Light background for the boxes */
        border-radius: 10px; /* Rounded corners */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Add a subtle shadow */
    }
    .red-text {
        color: red;
    }
    
</style>
</head>
<body>
    <header class="header">
        <div class="logo">
            <img src="SDS Logo.jpg" height="40px" alt="Society Dashboard">
        </div>
        <nav class="nav-links">
            <a href="Dashboard.php">Dashboard</a>
            <a href="Maintenance.php">Maintenance</a>
            <a href="Religious.php">Religious Fund</a>
            <a href="Balance.php">Balance</a>
            <a href="Receipt.php">Receipts</a>
            <a href="Index.php">Logout</a>
        </nav>
    </header>
    <h1>Balance</h1>
    <main class="main-content">
        <div class="register-container">
        <div class="row">
    <div class="col">
        <h1>Maintenance</h1>
        <h3>Total Maintenance: <?php echo number_format($totalmaintenance, 2); ?> </h3>
        <h3>Total Expenses: <?php echo number_format($totalmaintenanceexpenses, 2); ?> </h3>
        <h3>Bank Balance: <?php echo number_format($total_bank_transfer, 2); ?> </h3>
        <h3>Bank Debit: <?php echo number_format($totalbank, 2); ?> </h3>
        <h3>Cash On Hand: <span class="red-text"><?php echo number_format($cashOnHandMaintenance, 2); ?></span></h3>
    </div>
    <div class="col">
        <h1>Religious Fund</h1>
        <h3>Total Religious Fund: <?php echo number_format($totalReligious, 2); ?> </h3>
        <h3>Total Expenses: <?php echo number_format($totalexpensereligious, 2); ?> </h3>
        <h3>Bank Balance: Cash Only</h3>
        <h3>Bank Debit: Cash Only </h3>
        <h3>Cash On Hand: <span class="red-text"><?php echo number_format($cashOnHandReligious, 2); ?></span></h3>
    </div>
</div>
    </div>
        </div>
    </main>
</body>
</html>
