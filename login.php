<?php
include 'config.php'; // Include your database connection configuration

session_start(); // Start a session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $captcha = $_POST['captcha']; // Implement CAPTCHA validation if needed

    // Prepare and execute SQL query to check user credentials
    $sql = "SELECT id, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        // No user found with this email
        echo "<script>alert('Email not found. Please check your email or register.'); window.location.href = 'signin.html';</script>";
        $stmt->close();
        $conn->close();
        exit();
    }

    // Fetch user data
    $stmt->bind_result($userId, $hashedPassword);
    $stmt->fetch();
    $stmt->close();

    // Verify password
    if (!password_verify($password, $hashedPassword)) {
        echo "<script>alert('Incorrect password. Please try again.'); window.location.href = 'signin.html';</script>";
        $conn->close();
        exit();
    }

    // Set session variables and redirect to user home page
    $_SESSION['user_id'] = $userId;
    $_SESSION['logged_in'] = true;

    // Close database connection
    $conn->close();

    // Redirect to user home page
    header("Location: user_home.php");
    exit();
}
?>
