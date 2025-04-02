<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance</title>
    <link rel="stylesheet" href="sdscss.css">
    <style>
        h3 { 
            text-align: left; 
        }
        h1 {
            text-align: center;
            text-decoration: underline;
        }
        .red-text {
            color: red;
        }
    </style>
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
<body>
    <div class="container">
        <form action="/reset-password" method="post">
        <h1>Forgot Password</h1>
            <p><b>Enter your email address, and we'll send you a link to reset your password.</b></p>
            <input type="email" name="email" placeholder="Enter your email address" required><br><br>
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>