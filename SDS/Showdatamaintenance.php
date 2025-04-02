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
    $house_no = htmlspecialchars($_POST['house_no']);  // Sanitize the festival input
    $year = filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT);  // Sanitize and validate the year input

    // Prepare the SQL query with placeholders
    $query = "SELECT * FROM maintenance WHERE house_no = ? AND year = ?";
    $stmt = $connect->prepare($query);

    // Bind parameters to the prepared statement (both festival and year are strings)
    $stmt->bind_param("ss", $house_no, $year);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title> Show Data Maintenance </title>
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
    <label for="house_no"><b>House No.</b>:</label>
    <select id="house_no" name="house_no">
        <option value="none">None</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
        <option value="13">13</option>
        <option value="14">14</option>
        <option value="15">15</option>
        <option value="16">16</option>
        <option value="17">17</option>
        <option value="18">18</option>
        <option value="19">19</option>
        <option value="20">20</option>
        <option value="21">21</option>
        <option value="22">22</option>
        <option value="23">23</option>
        <option value="24">24</option>
        <option value="25">25</option>
        <option value="26">26</option>
        <option value="27">27</option>
        <option value="28">28</option>
        <option value="29">29</option>
        <option value="30">30</option>
        <option value="31">31</option>
        <option value="32">32</option>
        <option value="33">33</option>
        <option value="34">34</option>
        <option value="35">35</option>
        <option value="36">36</option>
        <option value="37">37</option>
        <option value="38">38</option>
        <option value="39">39</option>
        <option value="40">40</option>
        <option value="41">41</option>
        <option value="42">42</option>
        <option value="43">43</option>
        <option value="44">44</option>
        <option value="45">45</option>
        <option value="46">46</option>
        <option value="47">47</option>
        </select><br><br>

    <label for="year"><b>Year</b>:</label>
    <select id="year" name="year">
        <option value="none">None</option>
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
                    <th>Receipt No.</th>
                    <th>Date</th>
                    <th>House No.</th>
                    <th>Amount Type</th>
                    <th>Payment Type</th>
                    <th>Month</th>
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
                <td>" . htmlspecialchars($row->receipt_no) . "</td>
                <td>" . htmlspecialchars($row->m_date) . "</td>
                <td>" . htmlspecialchars($row->house_no) . "</td>
                <td>" . htmlspecialchars($row->ammounttype) . "</td>
                <td>" . htmlspecialchars($row->paymenttype) . "</td>
                <td>" . htmlspecialchars($row->month) . "</td>
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
            <td colspan='8' align='center'><strong>Total Amount: </strong></td>
            <td><strong>" . number_format($totalAmount, 2) . "</strong></td>
        </tr>
    </tfoot>
</table>";

    } else {
        echo "<p>No data found for the selected House No. and Year.</p>";
    }

    // Close the statement
    $stmt->close();
    mysqli_close($connect);
}
?>

</body>
</html>
