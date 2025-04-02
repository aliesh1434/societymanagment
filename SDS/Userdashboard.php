<?php
session_start();  // Start the session

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: Login.php");  // Redirect to login page if not logged in
    exit();
}

// Get the email of the logged-in user
$email = $_SESSION['email'];

// Connect to the database
$connect = mysqli_connect("localhost", "root", "", "sds");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch user details (including house number)
$query = "SELECT house_no FROM registration WHERE email = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$house_no = $user['house_no'];

// Calculate
$queryReligious = "SELECT SUM(ammount) AS total_religious FROM religious WHERE house_no = $house_no";
$resultReligious = $connect->query($queryReligious);
$totalReligious = $resultReligious->fetch_assoc()['total_religious'] ?? 0;

$queryMaintenance = "SELECT SUM(ammount) AS total_maintenance FROM maintenance WHERE house_no = $house_no and paymenttype = 'Maintenance'";
$resultMaintenance = $connect->query($queryMaintenance);
$totalMaintenance = $resultMaintenance->fetch_assoc()['total_maintenance']?? 0;

$queryDevelopment = "SELECT SUM(ammount) AS total_development FROM maintenance WHERE house_no = $house_no and paymenttype = 'Development'";
$resultDevelopment = $connect->query($queryDevelopment);
$totalDevelopment = $resultDevelopment->fetch_assoc()['total_development']?? 0;

$queryTransferFee = "SELECT SUM(ammount) AS total_transferfee FROM maintenance WHERE house_no = $house_no and paymenttype='TransferFee'";
$resultTransferFee = $connect->query($queryTransferFee);
$totalTransferFee = $resultTransferFee->fetch_assoc()['total_transferfee']?? 0;


$stmt->close();
mysqli_close($connect);
?>

<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="sdscss.css">
</head>

<body>
    <header class="header">
        <div class="logo">
            <img src="SDS Logo.jpg" height="40px" alt="Society Dashboard">
        </div>
        <nav class="nav-links">
            <a href="About.php">About Us</a>
            <a href="Index.php">Logout</a>
        </nav>
    </header>
    
    <h1><u>Shiv Drashti Row House</u></h1>
    <h1>Welcome, <?php echo htmlspecialchars($email); ?> !</h1><br>

    <div class="main-container">
        <div class="left-buttons">
            <button>
                <img src="notice.jpg" alt="Notice" width="100" onclick="window.location.href='Usernotice.php'">Notice
            </button>
            <button>
                <img src="maintenance.jpg" alt="" width="100" onclick="window.location.href='Usermaintenance.php'">Maintenance
            </button>
            <button>
                <img src="development.jpg" alt="Development" width="100" onclick="window.location.href='Userdevelopment.php'">Development
            </button>
            <button>
                <img src="transfer.jpg" alt="Transfer Fee" width="100" onclick="window.location.href='Usertransferfee.php'">Transfer Fee
            </button>
            <button>
                <img src="religious.jpg" alt="Religious" width="100" onclick="window.location.href='Userreligious.php'">Religious
            </button>
            <button>
                <img src="receipt.jpg" alt="" width="100" onclick="window.location.href='Userreceipt.php'">Receipt
            </button>
        </div>
    </div>
    
    <div class="dashboard-container">
        <div class="register-container">
            <div class="row">
                <div class="col">
                    <h1>Funds From House No.: <?php echo htmlspecialchars($house_no); ?></h1>
                    <h3>Total Maintenance: <?php echo number_format($totalMaintenance, 2); ?></h3>
                    <h3>Total Development: <?php echo number_format($totalDevelopment, 2);?> </h3>
                    <h3>Total Transfer Fee: <?php echo number_format($totalTransferFee, 2);?> </h3>
                    <h3>Total Religious Fund: <?php echo number_format($totalReligious, 2); ?> </h3>
                </div>
            </div>
        </div>
    </div>
    

    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #000000;
            padding: 30px 20px;
            color: white;
            border-radius: 15px;
        }
        .logo {
            display: flex;
            align-items: center;
        }
        .logo-name {
            margin-left: 10px;
            font-size: 1.2em;
            color: white;
        }
        .main-container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            margin-top: 20px;
        }
        .left-buttons, .right-buttons {
            display: flex;
            gap: 10px;
        }
        
        .button-container button,
        .left-buttons button,
        .right-buttons button {
            background-color: #000000;
            color: white;
            border: none;
            padding: 20px 40px;
            cursor: pointer;
            border-radius: 15px;
        }
        .button-container button img,
        .left-buttons button img,
        .right-buttons button img {
            display: block;
            margin: 0 auto 5px;
        }
        .dashboard-container {
            margin: 0 500px;
        }
    .row {
        display: flex;
        justify-content: space-between; /* Space between the two boxes */
        align-items: flex-start; /* Align items at the top */
        margin: 20px 0; /* Add some vertical spacing */
    }
    .col {
        flex: 1; /* Make both columns take equal space */
        margin:  1px; /* Add spacing between the columns */
        padding: 20px;
        background-color: rgba(255, 255, 255, 0.8); /* Light background for the boxes */
        border-radius: 10px; /* Rounded corners */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Add a subtle shadow */
    }
    </style>
</body>
</html>