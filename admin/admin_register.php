<?php
include '../config.php'; // Ensure this file contains your database connection settings

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if username or email already exists
    $sql = "SELECT id FROM admins WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Username or email already exists. Please try again.'); window.location.href = 'admin_register.html';</script>";
        $stmt->close();
        $conn->close();
        exit();
    }

    $stmt->close();

    // Insert admin into database
    $sql = "INSERT INTO admins (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Admin registered successfully!'); window.location.href = 'admin_success.html';</script>";
    } else {
        echo "<script>alert('Error: Could not register admin. Please try again.'); window.location.href = 'admin_register.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
