<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sds");

$loginMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM registration WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if ($user['password'] == $password) {
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            $redirectPage = ($user['role'] == 'Admin') ? 'Dashboard.php' : 'Userdashboard.php';

            $loginMessage = "
            <div class='login-feedback'>
                <div class='tick-box tick-success'>
                    <svg class='tick-svg' viewBox='0 0 52 52'>
                        <circle class='tick-circle' cx='26' cy='26' r='25' fill='none'/>
                        <path class='tick-check' fill='none' d='M14,27 L22,35 L38,19'/>
                    </svg>
                    <p class='tick-message text-success'>Login Successful!</p>
                </div>
                <script>
                    setTimeout(() => {
                        window.location.href = '$redirectPage';
                    }, 1500);
                </script>
            </div>";
        } else {
            $redirectPage = 'login.php';
            $loginMessage = "
            <div class='login-feedback'>
                <div class='tick-box tick-error'>
                    <svg class='tick-svg' viewBox='0 0 52 52'>
                        <circle class='tick-circle error' cx='26' cy='26' r='25' fill='none'/>
                        <path class='tick-check error' fill='none' d='M16,16 L36,36 M36,16 L16,36'/>
                    </svg>
                    <p class='tick-message text-danger'>Invalid Password</p>
                </div>
                <script>
                    setTimeout(() => {
                        window.location.href = '$redirectPage';
                    }, 1500);
                </script>
            </div>";
        }
    } else {
        $redirectPage = 'login.php';
        $loginMessage = "
        <div class='login-feedback'>
            <div class='tick-box tick-error'>
                <svg class='tick-svg' viewBox='0 0 52 52'>
                    <circle class='tick-circle error' cx='26' cy='26' r='25' fill='none'/>
                    <path class='tick-check error' fill='none' d='M16,16 L36,36 M36,16 L16,36'/>
                </svg>
                <p class='tick-message text-danger'>Email not registered</p>
            </div>
            <script>
                setTimeout(() => {
                    window.location.href = '$redirectPage';
                }, 1500);
            </script>
        </div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SDS | Login</title>
    <link rel="icon" type="image/png" href="home.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-image: url('SDSBG.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-repeat: no-repeat;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 1rem;
            position: relative;
            overflow: visible;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        /* Message Feedback Styles */
        .login-feedback {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(255, 255, 255, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1055;
            flex-direction: column;
        }

        .tick-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            animation: pop-in 0.4s ease-out;
        }

        .tick-svg {
            width: 100px;
            height: 100px;
        }

        .tick-circle {
            stroke: #28a745;
            stroke-width: 4;
            stroke-dasharray: 157;
            stroke-dashoffset: 157;
            animation: dash 0.6s forwards ease-in-out;
        }

        .tick-check {
            stroke: #28a745;
            stroke-width: 4;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: draw 0.4s 0.6s forwards ease-in-out;
        }

        .tick-box.tick-error .tick-circle,
        .tick-check.error {
            stroke: #dc3545;
        }

        .tick-box.tick-error .tick-circle {
            stroke-dasharray: 157;
            stroke-dashoffset: 157;
            animation: dash 0.6s forwards ease-in-out;
        }

        .tick-box.tick-error .tick-check {
            stroke-dasharray: 60;
            stroke-dashoffset: 60;
            animation: drawX 0.4s 0.6s forwards ease-in-out;
        }

        .tick-message {
            margin-top: 1rem;
            font-size: 1.25rem;
            font-weight: 600;
        }

        @keyframes dash {
            to {
                stroke-dashoffset: 0;
            }
        }

        @keyframes draw {
            to {
                stroke-dashoffset: 0;
            }
        }

        @keyframes drawX {
            to {
                stroke-dashoffset: 0;
            }
        }

        @keyframes pop-in {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        @media (max-width: 768px) {
    body {
        background-position: center;
    }

    .card {
        padding: 1.5rem !important;
    }

    .tick-box {
        padding: 1.2rem;
        max-width: 90%;
    }

    .tick-svg {
        width: 80px;
        height: 80px;
    }

    .tick-message {
        font-size: 1rem;
        text-align: center;
    }

    .navbar-brand img {
        height: 32px !important;
    }

    h2.text-center {
        font-size: 1.5rem;
    }

    .form-label {
        font-size: 0.9rem;
    }

    .form-control {
        font-size: 0.95rem;
    }

    .btn {
        font-size: 1rem;
        padding: 0.6rem 1rem;
    }

    .d-grid {
        gap: 0.8rem;
    }
}

    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand d-flex align-items-center" href="Index.php">
        <img src="SDS Logo.png" height="40px" class="me-2" alt="SDS Logo">
    </a>
</nav>

<!-- Login Form -->
<div class="container d-flex align-items-center justify-content-center" style="min-height: 90vh;">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
        <div class="card shadow-sm p-4">

            <!-- Feedback Message Area -->
            <?= $loginMessage ?>

            <h2 class="text-center mb-4 text-primary">
                <i class="bi bi-person-circle me-2"></i>Login
            </h2>
            <form method="post" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-at"></i></span>
                        <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email" required>
                    </div>
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
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember Me</label>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </button>
                </div>
                <div class="mt-3 text-center">
                    <a href="forgotpassword.php"><i class="bi bi-question-circle me-1"></i>Forgot Password?</a>
                    <p class="mt-2">New User? <a href="Registration.php"><i class="bi bi-person-plus-fill me-1"></i>Register Here</a></p>
                </div>
            </form>
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