<?php
$connect = new mysqli("localhost", "root", "", "sds");

if ($connect->connect_error) {
    die(json_encode(["exists" => false]));
}

if (isset($_GET['year'])) {
    $year = $_GET['year'];
    $stmt = $connect->prepare("SELECT opening_balance, bankbalance FROM yearlyreport WHERE year = ?");
    $stmt->bind_param("s", $year);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            "exists" => true,
            "opening_balance" => $row['opening_balance'],
            "bank_balance" => $row['bankbalance']
        ]);
    } else {
        echo json_encode(["exists" => false]);
    }

    $stmt->close();
}

$connect->close();
?>
