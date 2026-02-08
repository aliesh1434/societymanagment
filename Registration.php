<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") { 
    $connect = mysqli_connect("localhost", "root", "", "sds");

    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $username = $_POST['username'];
    $house_no = $_POST['house_no'];
    $gmail = $_POST['email'];
    $mob = $_POST['mob'];
    $pass = $_POST['password'];
    $cpass = $_POST['cpassword'];
    $role = 'User';

    $existSql = "SELECT * FROM registration WHERE email = ?";
    $stmt = mysqli_prepare($connect, $existSql);
    mysqli_stmt_bind_param($stmt, "s", $gmail);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $numExistRows = mysqli_num_rows($result);

    if ($numExistRows > 0) {
        echo "<script>alert('Email already exists!');</script>";
        header("Location: Login.php");
        exit();
    } else {
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SDS | Registration</title>
    <link rel="icon" type="image/png" href="home.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 fixed-top">
    <a class="navbar-brand d-flex align-items-center" href="Index.php">
        <img src="SDS Logo.png" height="40px" class="me-2" alt="SDS Logo">
    </a>
</nav>

<!-- Registration Form -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="text-center mb-4"><i class="bi bi-person-plus-fill me-1"> Registration</i></h2>
                    <form method="post">
                        <div class="mb-3">
                <label class="form-label"><i class="bi bi-person-circle"></i> Name</label>
                            <input type="text" name="username" class="form-control" placeholder="Enter Your Full Name" required>
                        </div>

                        <div class="mb-3">
                <label class="form-label"><i class="bi bi-house-door"></i> House No</label>
                            <input type="text" name="house_no" class="form-control" placeholder="Enter Your House No." required>
                        </div>

                         <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-at"></i></span>
                        <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email" required>
                    </div>
</div>

                        <div class="mb-3">
                <label class="form-label"><i class="bi bi-telephone"></i> Contact No</label>
                            <input type="text" name="mob" class="form-control" placeholder="Enter Your Mobile No." required>
                        </div>

                        <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
                        <span class="input-group-text" onclick="togglePassword()" style="cursor: pointer;">
                            <i class="bi bi-eye-slash" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                        <div class="mb-3">
                    <label for="password" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
                        </span>
                    </div>
                </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                        <div class="mt-3 text-center">
                            Already Registered? <a href="Login.php">Login Here!</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword() {
    const passwordField = document.getElementById("password");
    const toggleIcon = document.getElementById("toggleIcon");
    const isPassword = passwordField.type === "password";
    passwordField.type = isPassword ? "text" : "password";
    toggleIcon.classList.toggle("bi-eye");
    toggleIcon.classList.toggle("bi-eye-slash");
}
</script>
</body>
</html>
