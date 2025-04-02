<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Establish database connection
    $connect = mysqli_connect("localhost", "root", "", "sds");

    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Retrieve and sanitize form inputs
    $t_detail = mysqli_real_escape_string($connect, $_POST['t_detail']);
    $ammount = filter_var($_POST['ammount'], FILTER_VALIDATE_FLOAT);
    $festival = mysqli_real_escape_string($connect, $_POST['festival']);
    $year = mysqli_real_escape_string($connect, $_POST['year']);

    // Check if inputs are valid
    if (!empty($t_detail) && is_numeric($ammount) && $festival != "None" && !empty($year)) {
        // Prepare and execute the query
        $query = "INSERT INTO transactions (t_detail, ammount, festival, year) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "sdss", $t_detail, $ammount, $festival, $year);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Data added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding data.');</script>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Invalid input. Please fill in all fields correctly.');</script>";
    }
    mysqli_close($connect);
}

// Display the registration form
echo "
<!DOCTYPE html>
<html lang=\"en\">
    <head>
        <title>Religious Fund Expenses</title>
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
    <form method=\"POST\">
        <h2><u>Religious Fund Expenses</u></h2>

        <label for=\"festival\"><b>Festival:</b></label>
        <select id=\"festival\" name=\"festival\">
            <option value=\"None\">None</option>
            <option value=\"Holi\">Holi</option>
            <option value=\"Janmashtami\">Janmashtami</option>
            <option value=\"Ganeshotsav\">Ganeshotsav</option>
            <option value=\"Navratri\">Navratri</option>
            <option value=\"Shanti Havan\">Shanti Havan</option>
        </select><br><br>

        <label for=\"t_detail\"><b>Transaction Details</b>:</label>
        <input type=\"text\" id=\"t_detail\" name=\"t_detail\" placeholder=\"Enter Details\" required><br><br>
        
        <label for=\"ammount\"><b>Transaction ammount</b>:</label>
        <input type=\"text\" id=\"ammount\" name=\"ammount\" placeholder=\"Enter ammount\" required><br><br>
        
        <label for=\"year\"><b>Year</b>:</label>
        <input type=\"text\" id=\"year\" name=\"year\" placeholder=\"Enter Year\" required><br><br>

                        <button type=\"submit\">Add Data</button>
        <button type=\"button\" onclick=\"window.location.href='Showdatatransaction.php'\">Show Data</button>
    </form>
    </body>
</html>";
?>