<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") { 
    // Establish database connection
    $connect = mysqli_connect("localhost", "root", "", "sds");

    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }
}
    // Start the HTML output
    echo "<!doctype html>
    <html lang=\"en\">
    <head>
        <title> Dashboard </title>
        <link rel=\"stylesheet\" href=\"sdscss.css\">
    </head>
    <style>
        .notice-container {
    background-color: #fff;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 80%;
    max-width: 600px;
    margin: 20px auto;
}
.notice-header {
    text-align: center;
    margin-bottom: 20px;
}
.notice-header h1 {
    margin: 0;
    font-size: 24px;
    color: #333;
}
.notice {
    margin-bottom: 10px;
}
.notice h2 {
    margin: 0;
    font-size: 20px;
    color: #ff0000;
}
.notice p {
    margin: 5px 0 0;
    font-size: 16px;
    color: #666;
}
.notice-date {
    text-align: right;
    font-size: 14px;
    color: #999;
}
    </style>
    <body>
        <header class=\"header\">
        <div class=\"logo\">
        <img src=\"SDS Logo.jpg\" height=\"40px\" alt=\"Society Dashboard\">
        </div>
        <nav class=\"nav-links\">
        <a href=\"Maintenance.php\">Maintenance</a>
        <a href=\"Religious.php\">Religious Fund</a>
        <a href=\"Balance.php\">Balance</a>
        <a href=\"Receipt.php\">Receipts</a>
        <a href=\"Bankdebit.php\">Bank Debit</a>
        <a href=\"Index.php\">Logout</a>
        </nav>
        </header>
        <h1><u>Shiv Drashti Row House</u></h1>
        <main class=\"main-content\">
            <div class=\"register-container\">
                <link rel=\"stylesheet\" href=\"sdscss.css\">
                <div class=\"notice-container\">
            <div class=\"notice-header\">
                <h1>Notice Board</h1>
                <hr>
            </div>
        <div class=\"notice\">
            <h2><b>Annual General Meeting</b></h2>
            <p>We are pleased to announce that the Annual General Meeting (AGM) will be held on 25th December 2023 at 10:00 AM in the society clubhouse. All members are requested to attend.</p>
            <div class=\"notice-date\">Posted on: 1st December 2023</div>
        </div>
        <div class=\"notice\">
            <h2><b>Maintenance Fee Due</b></h2>
            <p>Please be reminded that the maintenance fee for the month of December is due by 10th December 2023. Kindly make the payment at the society office or through the online portal.</p>
            <div class=\"notice-date\">Posted on: 30th November 2023</div>
        </div>
        <!-- Add more notices as needed -->
    </div>

    <section class=\"about\">
    
        <h2>About Us</h2>
        <p>We provide an all-in-one solution for managing your society efficiently and effectively.</p>
        <p>&copy; 2025 Society Manager. All rights reserved.</p>
    </section>
            </div>  
        </main>
    </body>
    </html>";
?>
