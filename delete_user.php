<?php
session_start();
include('config.php');


if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: admin_login.html");
    exit();
}

// Check if 'id' is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];

    // Prepare to delete user
    $delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    if ($delete_stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters and execute
    $delete_stmt->bind_param("i", $user_id);
    if ($delete_stmt->execute()) {
        $delete_stmt->close();
        header('Location: all_users.php'); // Redirect to all users page after deletion
        exit();
    } else {
        die("Delete failed: " . $conn->error);
    }
} else {
    die("Invalid user ID.");
}
?>
