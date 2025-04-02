<?php

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Establish database connection
    $connect = mysqli_connect("localhost", "root", "", "sds");

    // Check for successful connection
    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Retrieve and sanitize form inputs using POST
    $receipt_no = htmlspecialchars($_POST['receipt_no']);  // Sanitize the festival input

    // Prepare the SQL query with placeholders
    $query = "SELECT * FROM receipt WHERE receipt_no = ?";
    $stmt = $connect->prepare($query);

    // Bind parameters to the prepared statement (both festival and year are strings)
    $stmt->bind_param("s", $receipt_no);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title> Print Receipt </title>
    <link rel="stylesheet" href="sdscss.css">
</head>
<body>

<style>
table {
    width: 100%;
    background-color: #ccc;
    border-collapse: collapse;
    border: 3px solid #000000;
}

th, td {
    border: 1px solid #000000;
    padding: 15px;
    text-align: center;
}

tfoot td {
    font-weight: bold;
}
</style>

<!-- Form to select Festival and Year -->
<form method="POST" action="">
    <label for="receipt_no">Enter Receipt No:</label>
    <input type="text" id="receipt_no" name="receipt_no" placeholder="Enter Receipt No" required>
    
    <button type="submit" value="Show Data">Show Data</button>
</form>

<?php
// Display the fetched data if the form is submitted
if (isset($result)) {
    if ($result->num_rows > 0) {
        // Initialize total amount variable
        $totalAmount = 0;

        echo "
        <table>
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Receipt No</th>
                    <th>Date</th>
                    <th>House No</th>
                    <th>Received From</th>
                    <th>Purpose</th>
                    <th>Payment Mode</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>";

        $cnt = 1;
        // Loop through each row of the result set
        while ($row = $result->fetch_object()) {
            echo "
            <tr>
                <td>" . $cnt . "</td>
                <td>" . $row->receipt_no . "</td>
                <td>" . $row->r_date . "</td>
                <td>" . $row->house_no . "</td>
                <td>" . $row->received_from . "</td>
                <td>" . $row->purpose . "</td>
                <td>" . $row->payment_mode . "</td>
                <td>" . number_format($row->amount, 2) . "</td>

                </tr>";

            // Add amount to total
            $totalAmount += $row->amount;

            $cnt++;
        }

        echo "</tbody>
    <tfoot>
        <tr>
            <td colspan='7' align='center'><strong>Total Amount: </strong></td>
            <td><strong>" . number_format($totalAmount, 2) . "</strong></td>
        </tr>
        </tfoot>
        </table>
        <button onclick=\"window.print()\">Print</button>";

    } else {
        echo "<p>No data found for the selected Receipt No.</p>";
    }

    // Close the statement
    $stmt->close();
    mysqli_close($connect);
}
?>

</body>
</html>
