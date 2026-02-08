<?php
if (isset($_GET['house_no'])) {
    $house_no = $_GET['house_no'];
    $connect = mysqli_connect("localhost", "root", "", "sds");

    if (!$connect) {
        echo "DB Error";
        exit();
    }

    $query = "SELECT name FROM house_plot WHERE house_no = ?";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "s", $house_no);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo $row['name'];
    } else {
        echo "";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connect);
}
?>
