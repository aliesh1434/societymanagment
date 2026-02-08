<?php
// Database connection
$connect = new mysqli("localhost", "root", "", "sds");
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

// Only proceed if export form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["export_data"]) && $_POST["export_data"] === "all") {

    // Query all house data (join with registration if needed)
    $query = "
        SELECT 
            hp.house_no, 
            hp.house_details, 
            hp.name AS owner_name,
            hp.development, 
            hp.chairrent, 
            hp.mehsul, 
            hp.coprent, 
            hp.transferfee,
            reg.mob AS mobile, 
            reg.email 
        FROM house_plot hp
        LEFT JOIN registration reg ON hp.house_no = reg.house_no
        ORDER BY hp.house_no ASC
    ";

    $result = $connect->query($query);

    if ($result->num_rows > 0) {
        // Excel headers
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=All_House_Records.xls");

        // Start Excel table
        echo "<table border='1'>";
        echo "<tr>
                <th>House No</th>
                <th>House Details</th>
                <th>Owner Name</th>
                <th>Development</th>
                <th>Chair Rent</th>
                <th>Mehsul</th>
                <th>COP Rent</th>
                <th>Transfer Fee</th>
                <th>Mobile</th>
                <th>Email</th>
              </tr>";

        // Output each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['house_no']}</td>
                    <td>{$row['house_details']}</td>
                    <td>{$row['owner_name']}</td>
                    <td>{$row['development']}</td>
                    <td>{$row['chairrent']}</td>
                    <td>{$row['mehsul']}</td>
                    <td>{$row['coprent']}</td>
                    <td>{$row['transferfee']}</td>
                    <td>{$row['mobile']}</td>
                    <td>{$row['email']}</td>
                  </tr>";
        }

        echo "</table>";
    } else {
        echo "No data found to export.";
    }

} else {
    echo "Invalid request.";
}

$connect->close();
?>
