<?php
include '../config.php'; // Include your database connection configuration

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute SQL query to check admin credentials
    $sql = "SELECT id, password FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo "<script>alert('Username not found. Please check your username.'); window.location.href = 'admin_login.html';</script>";
        $stmt->close();
        $conn->close();
        exit();
    }

    $stmt->bind_result($adminId, $hashedPassword);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($password, $hashedPassword)) {
        echo "<script>alert('Incorrect password. Please try again.'); window.location.href = 'admin_login.html';</script>";
        $conn->close();
        exit();
    }

    $_SESSION['admin_id'] = $adminId;
    $_SESSION['logged_in'] = true;

    $conn->close();
    header("Location: admin_dashboard.php");
    exit();
}
?>