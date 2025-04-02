<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") { 
    // Establish database connection
    $connect = mysqli_connect("localhost", "root", "", "sds");

    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Retrieve form inputs
    $username = $_POST['username'];
    $house_no = $_POST['house_no'];
    $gmail = $_POST['email'];
    $mob = $_POST['mob'];
    $pass = $_POST['password'];
    $cpass = $_POST['cpassword'];
    $role = 'User';

    // Check if the email already exists
    $existSql = "SELECT * FROM registration WHERE email = ?";
    $stmt = mysqli_prepare($connect, $existSql);
    mysqli_stmt_bind_param($stmt, "s", $gmail);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $numExistRows = mysqli_num_rows($result);

    if ($numExistRows > 0) {
        echo "<script>alert('Username already exists!');</script>";
        header("Location: Login.php");
        exit();
    } else {
        // Check if passwords match
        if ($pass === $cpass) {
            $query = "INSERT INTO registration (username, house_no, email, mob, password, role) VALUES (?, ?, ?, ?, ?,?)";
            $stmt = mysqli_prepare($connect, $query);
            mysqli_stmt_bind_param($stmt, "ssssss", $username, $house_no, $gmail, $mob, $pass, $role);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                echo "<script>alert('Registration successful!');</script>";
                header("Location: Login.php");
                exit();
            } else {
                echo "<script>alert('Error during registration. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Password and Confirm Password do not match!');</script>";
            header("Location: Registration.php");
            exit();
        }
    }

    mysqli_close($connect);
}

// Display the registration form
echo "
<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <title>Register Page</title>
    <link rel=\"stylesheet\" href=\"sdscss.css\">
</head>
<body>
<header class=\"header\">
        <div class=\"logo\" aria-setsize=\"10%\">
            <img src=\"SDS Logo.jpg\" height=\"40px\" alt=\"Society Dashboard\">
        </div>
        <div aria-setsize=\"80%\">
            <nav class=\"nav-links\">
                <a href=\"Index.php\">Home</a>
            </nav>
        </div>
    </header>
    <div class=\"register-container\">
        <form method=\"post\">
            <h2><u>REGISTER PAGE</u></h2>
            <label for=\"username\"><b>Full Name:</b></label>
            <input type=\"text\" id=\"username\" name=\"username\" placeholder=\"Enter Your Full Name\" required><br><br>

            <label for=\"house_no\"><b>House No.:</b></label>
            <input type=\"text\" id=\"house_no\" name=\"house_no\" placeholder=\"Enter Your House No.\" required><br><br>

            <label for=\"email\"><b>Email ID:</b></label>
            <input type=\"text\" id=\"email\" name=\"email\" placeholder=\"Enter Your Email ID\" required><br><br>

            <label for=\"mob\"><b>Mobile No:</b></label>
            <input type=\"text\" id=\"mob\" name=\"mob\" placeholder=\"Enter Your Mobile No.\" required><br><br>

            <label for=\"password\"><b>Password:</b></label>
            <input type=\"password\" id=\"password\" name=\"password\" placeholder=\"Enter Password\" required><br><br>

            <label for=\"cpassword\"><b>Confirm Password:</b></label>
            <input type=\"password\" id=\"cpassword\" name=\"cpassword\" placeholder=\"Re-Enter Password\" required><br><br>

            <button type=\"submit\">Submit</button><br><br>
            <a href=\"Login.php\">Login</a>
        </form>
    </div>
</body>
</html>";
?>