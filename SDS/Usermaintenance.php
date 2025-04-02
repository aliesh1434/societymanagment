<?php
session_start();

// Check if the user is logged in (assuming email is stored in session)
if (!isset($_SESSION['email'])) {
    header("Location: Login.php");
    exit();
}

// Get the logged-in user's email
$user_email = $_SESSION['email'];

// Establish database connection
$connect = mysqli_connect("localhost", "root", "", "sds");

// Check for successful connection
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch the house number associated with the logged-in user's email
$query = "SELECT house_no FROM registration WHERE email = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($house_no);

if (!$stmt->fetch()) {
    echo "User not found.";
    exit();
}

// Close the first statement
$stmt->close();

// Debugging: Check if house_no is retrieved
if (!$house_no) {
    die("Error: No house number found for this user.");
}

// Fetch maintenance records for this house_no
$query = "SELECT * FROM maintenance WHERE house_no = ? AND paymenttype = 'Maintenance'";
$stmt = $connect->prepare($query);
$stmt->bind_param("s", $house_no);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Show Data Maintenance</title>
    <link rel="stylesheet" href="sdscss.css">
</head>
<body>
<header class="header">
    <div class="logo">
        <img src="SDS Logo.jpg" height="40px" alt="Society Dashboard">
    </div>
    <nav class="nav-links">
        <a href="Userdashboard.php">Dashboard</a>
        <a href="Index.php">Logout</a>
    </nav>
</header><br>
<h1>Your Maintenance Record: </h1>

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
    header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #000000;
            padding: 30px 20px;
            color: white;
            border-radius: 15px;
        }
</style>

<?php
// Display the fetched data
if ($result->num_rows > 0) {
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
    echo "<p>No data found for the selected House No.</p>";
}

// Close database connection
mysqli_close($connect);
?>

</body>
</html>