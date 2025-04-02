<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sds");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];  // User input

    // Secure query using prepared statements
    $stmt = $conn->prepare("SELECT * FROM registration WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        // Verify hashed password
        if ($user['password'] == $password) {
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] == 'Admin') {
                header("Location: Dashboard.php");
                exit();
            } else {
                header("Location: Userdashboard.php");
                exit();
            }
        } else {
            echo "<script>alert('Invalid login credentials');</script>";
        }
    } else {
        echo "<script>alert('Invalid login credentials');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login Page</title>
    <link rel="stylesheet" href="sdscss.css">
    <link rel="shortcut icon" href="20241228_145206.jpg" type="image/x-icon">
</head>
<body>
    <header class="header">
        <div class="logo">
            <img src="SDS Logo.jpg" height="40px" alt="Society Dashboard">
        </div>
        <nav class="nav-links">
            <a href="Index.php">Home</a>
        </nav>
    </header>
    <form action="" method="post">
        <h2>Login</h2>

        Email: <input type="text" name="email" placeholder="Email/Mobile No." required><br><br>
        Password: <input type="password" name="password" placeholder="Password" required><br><br>
        <input type="submit" value="Login"><br><br>
        <a href="forgotpassword.php">Forgot Password</a>
        <p>New User? <a href="Registration.php">Register Here</a></p>
    </form>
</body>
</html>
