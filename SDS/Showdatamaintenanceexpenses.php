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
    $year = filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT);  // Sanitize and validate the year input

    // Prepare the SQL query with placeholders
    $query = "SELECT * FROM maintenancetransactions WHERE year = ?";
    $stmt = $connect->prepare($query);

    // Bind parameters to the prepared statement (both festival and year are strings)
    $stmt->bind_param("s", $year);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title> Show Data Maintenance Expenses </title>
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
            <a href="Religious.php">Religious</a> 
            <a href="Balance.php">Balance</a>
            <a href="Receipt.php">Receipts</a>
            <a href="Index.php">Logout</a>
        </nav>
    </header>
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

    <label for="year"><b>Year:</b></label>
    <input type="text" id="year" name="year" placeholder="Enter Year" required><br><br>

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
                    <th>Transaction_Detail</th>
                    <th>Year</th>
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
                <td>" . htmlspecialchars($row->t_detail) . "</td>
                <td>" . htmlspecialchars($row->year) . "</td>
                <td>" . htmlspecialchars($row->ammount) . "</td>
            </tr>";

            // Add amount to total
            $totalAmount += $row->ammount;

            $cnt++;
        }

        echo "</tbody>
            <tfoot>
                <tr>
                    <td colspan='3' align='center'><strong>Total Amount: </strong></td>
                    <td> ". number_format($totalAmount, 2) . "</td>
                </tr>
            </tfoot>
        </table>";

    } else {
        echo "<p>No data found for the selected festival and year.</p>";
    }

    // Close the statement
    $stmt->close();
    mysqli_close($connect);
}
?>

</body>
</html>
