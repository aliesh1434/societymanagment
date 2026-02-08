<?php
// delete_notice.php
$connect = mysqli_connect("localhost", "root", "", "sds");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['notice_id'])) {
    $notice_id = intval($_POST['notice_id']);
    $stmt = mysqli_prepare($connect, "DELETE FROM notices WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $notice_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Redirect back
header("Location: dashboard.php");
exit;
