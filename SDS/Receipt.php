<?php

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Establish database connection
    $connect = mysqli_connect("localhost", "root", "", "sds");

    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Retrieve and sanitize form inputs
    $receipt_no = mysqli_real_escape_string($connect, $_POST['receipt_no']);
    $r_date = mysqli_real_escape_string($connect, $_POST['r_date']);
    $received_from = mysqli_real_escape_string($connect, $_POST['received_from']);
    $amount = mysqli_real_escape_string($connect, $_POST['amount']);
    $purpose = mysqli_real_escape_string($connect, $_POST['purpose']);
    $payment_mode = mysqli_real_escape_string($connect, $_POST['payment_mode']);
    $house_no = mysqli_real_escape_string($connect, $_POST['house_no']);


    // Check if inputs are valid
    if (!empty($receipt_no) && $house_no != "None") {
        // Prepare and execute the query
        $query = "INSERT INTO receipt (receipt_no, r_date, received_from, amount, purpose, payment_mode, house_no) VALUES (?, ?, ?, ?, ?, ?, ?)"; 
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "sssssss", $receipt_no, $r_date, $received_from, $amount, $purpose, $payment_mode, $house_no);
        
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
$connect = mysqli_connect("localhost", "root", "", "sds");
$lastrow_query = "SELECT receipt_no FROM receipt ORDER BY CAST(SUBSTRING_INDEX(receipt_no, '/', -1) AS UNSIGNED) DESC LIMIT 1";
$result = mysqli_query($connect, $lastrow_query);
$lastrow = mysqli_fetch_assoc($result);
$last_receipt_no = isset($lastrow['receipt_no']) ? $lastrow['receipt_no'] : 'N/A';
mysqli_close($connect);

// Display the registration form
echo "
<!DOCTYPE html>
<html lang=\"en\">
    <head>
        <title> Religious Fund </title>
        <link rel=\"stylesheet\" href=\"sdscss.css\">
    </head>
    <body>
    <header class=\"header\">
        <div class=\"logo\">
            <img src=\"SDS Logo.jpg\" height=\"40px\" alt=\"Society Dashboard\">
        </div>
        <nav class=\"nav-links\">
            <a href=\"Dashboard.php\">Dashboard</a>
            <a href=\"Maintenance.php\">Maintenance</a>
            <a href=\"Religious.php\">Religious</a> 
            <a href=\"Balance.php\">Balance</a>
            <a href=\"Receipt.php\">Receipts</a>
            <a href=\"Index.php\">Logout</a>
        </nav>
    </header>
    <form method=\"POST\" action=\"\">
    <h2><u>Receipt</u></h2>
    <H4> LAST RECEIPT NO. : $last_receipt_no </H4>
    <label for=\"receipt_no\"><b>Receipt No:</b></label>
    <input type=\"text\" placeholder=\"Enter Receipt No\" name=\"receipt_no\" required><br><br>
    <label for=\"r_date\"><b>Date:</b></label>
    <input type=\"date\" placeholder=\"Enter Date\" name=\"r_date\" required><br><br>
    <label for=\"received_from\"><b>Received From:</b></label>
    <input type=\"text\" placeholder=\"Enter Received From\" name=\"received_from\" required><br><br>
    <label for=\"amount\"><b>Amount:</b></label>
    <input type=\"text\" placeholder=\"Enter Amount\" name=\"amount\" required><br><br>
    <label for=\"purpose\"><b>Purpose:</b></label>
    <select id=\"purpose\" name=\"purpose\">
        <option value=\"None\">None</option>
        <option value=\"Maintenance\">Maintenance</option>
        <option value=\"Development\">Development Charges</option>
        <option value=\"Transfer\">Transfer Fee</option>
        <option value=\"Rentcop\">C.O.P Rent</option>
        <option value=\"Otherrent\">Other Rent</option>
        </select><br><br>
    <label for=\"payment_mode\"><b>Payment Mode:</b></label>
    <select id=\"payment_mode\" name=\"payment_mode\">
        <option value=\"None\">None</option>
        <option value=\"Cash\">Cash</option>
        <option value=\"Cheque\">Cheque</option>
        <option value=\"Banktransfer\">Bank Transfer</option>
        </select><br><br>
    <label for=\"house_no\"><b>House No.:</b></label>
    <select id=\"house_no\" name=\"house_no\">
        <option value=\"None\">None</option>";
for ($i = 1; $i <= 47; $i++) {
    echo "<option value=\"$i\">$i</option>";
}
echo "
    </select><br><br>
    <button type=\"submit\">Submit</button>
    <button onclick=\"window.location.href='printreceipt.php'\" type=\"button\">Print</button>
        
    </form>
    </body>
</html>";
?>