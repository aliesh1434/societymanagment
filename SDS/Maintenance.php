<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $connect = mysqli_connect("localhost", "root", "", "sds");
    // Retrieve and sanitize form inputs
    $house_no = (mysqli_real_escape_string($connect, $_POST['house_no']));
    $ammount = (mysqli_real_escape_string($connect, $_POST['ammount']));
    $receipt_no = (mysqli_real_escape_string($connect, $_POST['receipt_no']));
    $ammounttype = (mysqli_real_escape_string($connect, $_POST['ammounttype']));
    $paymenttype = (mysqli_real_escape_string($connect, $_POST['paymenttype']));
    $month = (mysqli_real_escape_string($connect, $_POST['month']));
    $year = (mysqli_real_escape_string($connect, $_POST['year']));
    $m_date = (mysqli_real_escape_string($connect, $_POST['m_date']));

    // Check if inputs are valid
    if (!empty($house_no) && $house_no != "None" && is_numeric($ammount) && $receipt_no != "None") {
        $query = "INSERT INTO maintenance (house_no, ammount, receipt_no, month, year, m_date, ammounttype, paymenttype) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "ssssssss", $house_no, $ammount, $receipt_no, $month, $year, $m_date, $ammounttype, $paymenttype);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Data added successfully!');</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($connect) . "');</script>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Invalid input. Please fill in all fields correctly.');</script>";
    }

    mysqli_close($connect);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title> Maintenance </title>
    <link rel="stylesheet" href="sdscss.css">
</head>
<body>
<header class="header">
    <div class="logo">
        <img src="SDS Logo.jpg" height="40px" alt="Society Dashboard">
    </div>
    <nav class="nav-links">
        <a href="Dashboard.php">Dashboard</a>
        <a href="Maintenance.php">Maintenance</a>
        <a href="maintenanceexpenses.php">Expenses</a>
        <a href="Religious.php">Religious</a>
        <a href="Balance.php">Balance</a>
        <a href="Receipt.php">Receipts</a>
        <a href="Index.php">Logout</a>
    </nav>
</header>
<form method="POST">
    <h2><u>Maintenance</u></h2>
    <label for="receipt_no"><b>Receipt No:</b></label>
    <input type="text" id="receipt_no" name="receipt_no" placeholder="Enter Receipt No" required><br><br>

    <label for="m_date"><b>Date:</b></label>
    <input type="date" id="m_date" name="m_date" required><br><br>

    <label for="house_no"><b>House No.:</b></label>
    <select id="house_no" name="house_no" required>
        <option value="None">None</option>
        <?php for ($i = 1; $i <= 47; $i++) echo "<option value=\"$i\">$i</option>"; ?>
    </select><br><br>

    <label for="ammount"><b>Amount</b>:</label>
    <input type="text" id="ammount" name="ammount" placeholder="Enter ammount" required><br><br>

    <label for="ammounttype"><b>Amount Type:</b></label>
    <select id="ammounttype" name="ammounttype">
        <option value="Cash">Cash</option>
        <option value="Cheque">Cheque</option>
        <option value="Banktransfer">Bank Transfer</option>
    </select><br><br>   

    <label for="paymenttype"><b>Payment Type:</b></label>
    <select id="paymenttype" name="paymenttype">
        <option value="Maintenance">Maintenance</option>
        <option value="Development">Development Charges</option>
        <option value="Transferfee">Transfer Charges</option>
        </select><br><br>

    <label for="month"><b>Month:</b></label>
        <select id="month" name="month">
        <option value="none">None</option>
            <option value="January">January</option>
            <option value="February">February</option>
            <option value="March">March</option>
            <option value="April">April</option>
            <option value="May">May</option>
            <option value="June">June</option>
            <option value="July">July</option>
            <option value="August">August</option>
            <option value="September">September</option>
            <option value="October">October</option>
            <option value="November">November</option>
            <option value="December">December</option>
        </select><br><br>

    <label for="year"><b>Year</b>:</label>
    <select id="year" name="year">
        <option value="none">None</option>
        <option value="2013">2013</option>
    <option value="2014">2014</option>
    <option value="2015">2015</option>
    <option value="2016">2016</option>
    <option value="2017">2017</option>
    <option value="2018">2018</option>
    <option value="2019">2019</option>
    <option value="2020">2020</option>
    <option value="2021">2021</option>
    <option value="2022">2022</option>
    <option value="2023">2023</option>
    <option value="2024">2024</option>
    <option value="2025">2025</option>
    <option value="2026">2026</option>
    <option value="2027">2027</option>
    <option value="2028">2028</option>
    <option value="2029">2029</option>
    <option value="2030">2030</option>
    </select><br><br>

    <button type="submit" value="Submit">Submit</button>
    <button onclick="window.location.href='Showdatamaintenance.php'">Show Data</button>
</form>
</body>
</html>