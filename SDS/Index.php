<?php
if (isset($_SESSION["LOGGED_IN"]) && $_SESSION["LOGGED_IN"] == "true") {
    $LOGGED_IN = true;
} else {
    $LOGGED_IN = false;
}

echo "
<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Home</title>
    <link rel=\"stylesheet\" href=\"sdscss.css\">
    <link rel=\"shortcut icon\" href=\"20241228_145206.jpg\" type=\"image/x-icon\">
</head>
<link rel=\"stylesheet\" href=\"login.css\">
<style>
    header{
        line-height: 1cm;
    }
    h2{
        text-align: center;
        font-size: 30px;
        font-style: underline;
    }
</style>
<body>
    <header class=\"header\">
        <div class=\"logo\" aria-setsize=\"10%\">
            <img src=\"SDS Logo.jpg\" height=\"40px\" alt=\"Society Dashboard\">
        </div>
        <div aria-setsize=\"80%\">
            <nav class=\"nav-links\">
                <a href=\"Index.php\">Home</a>
                <a href=\"Login.php\">Login</a>
            </nav>
        </div>
    </header>
    <h1><b><u>Society Managment</u></b></h1>
    <form>
        <h2><u>About Us</u></h2>
        <p>We provide an all-in-one solution for managing your society efficiently and effectively.</p>
        <p>&copy; 2025 Society Manager. All rights reserved.</p>
    </form>
</body>
</html>";
?>