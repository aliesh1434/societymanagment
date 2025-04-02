<?php

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Establish database connection
    $connect = mysqli_connect("localhost", "root", "", "sds");

    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Retrieve and sanitize form inputs
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $house_no = mysqli_real_escape_string($connect, $_POST['house_no']);
    $ammount = mysqli_real_escape_string($connect, $_POST['ammount']);
    $festival = mysqli_real_escape_string($connect, $_POST['festival']);
    $year = mysqli_real_escape_string($connect, $_POST['year']);

    // Check if inputs are valid
    if (!empty($name) && $house_no != "None" && is_numeric($ammount) && $festival != "None") {
        // Prepare and execute the query
        $query = "INSERT INTO religious (name, house_no, ammount, festival, year) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "sssss", $name, $house_no, $ammount, $festival, $year);
        
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
            <a href=\"Transaction.php\">Expenses</a> 
            <a href=\"Balance.php\">Balance</a>
            <a href=\"Receipt.php\">Receipts</a>
            <a href=\"Index.php\">Logout</a>
        </nav>
    </header>
    <form method=\"POST\" action=\"\">  
        <h2><u>Religious Fund</u></h2>

        <label for=\"festival\"><b>Festival:</b></label>
        <select id=\"festival\" name=\"festival\">
            <option value=\"None\">None</option>
            <option value=\"Holi\">Holi</option>
            <option value=\"Janmashtami\">Janmashtami</option>
            <option value=\"Ganeshotsav\">Ganeshotsav</option>
            <option value=\"Navratri\">Navratri</option>
            <option value=\"Shanti Havan\">Shanti Havan</option>
        </select><br><br>

        <label for=\"house_no\"><b>House No.:</b></label>
        <select id=\"house_no\" name=\"house_no\">
            <option value=\"None\">None</option>
            <option value=\"Broker\">Broker</option>
            <option value=\"Doner\">Doner</option>";

for ($i = 1; $i <= 47; $i++) {
    echo "<option value=\"$i\">$i</option>";
}

echo "
        </select><br><br>

        <label for=\"name\"><b>Name</b>:</label>
        <input type=\"text\" id=\"name\" name=\"name\" placeholder=\"Enter Name\" required><br><br>

        <label for=\"ammount\"><b>Amount</b>:</label>
        <input type=\"text\" id=\"ammount\" name=\"ammount\" placeholder=\"Enter Amount\" required><br><br>

        <label for=\"year\"><b>Year</b>:</label>
        <input type=\"text\" id=\"year\" name=\"year\" placeholder=\"Enter Year\" required><br><br>

                <button type=\"submit\">Add Data</button>
        <button type=\"button\" onclick=\"window.location.href='Showdatareligious.php'\">Show Data</button>

    </form>
    </body>
</html>";
?>