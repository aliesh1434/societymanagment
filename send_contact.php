<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars(trim($_POST["name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $message = htmlspecialchars(trim($_POST["message"]));

    // Database connection
    $conn = new mysqli("localhost", "root", "", "sds"); // Change DB name if needed

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        echo "<script>alert('Message saved successfully.'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Failed to save message.'); window.location.href='index.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
