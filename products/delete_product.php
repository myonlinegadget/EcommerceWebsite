<?php
include '../config.php'; // Include your database connection configuration

session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: admin_login.html");
    exit();
}

// Check if 'id' is set in the URL
if (isset($_GET['id'])) {
    $productId = intval($_GET['id']); // Get product ID from URL

    // Prepare SQL query to delete product
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $productId);
    
    if ($stmt->execute()) {
        // Check if any rows were affected
        if ($stmt->affected_rows > 0) {
            // Successfully deleted
            echo "<script>alert('Product deleted successfully!'); window.location.href = 'manage_products.php';</script>";
        } else {
            // No rows affected, possibly invalid ID
            echo "<script>alert('Error: Product not found.'); window.location.href = 'manage_products.php';</script>";
        }
    } else {
        // Error executing query
        echo "<script>alert('Error: Could not delete product. Please try again.'); window.location.href = 'manage_products.php';</script>";
    }
    
    $stmt->close();
    $conn->close();
} else {
    // 'id' parameter is missing in the URL
    echo "<script>alert('Error: No product ID specified.'); window.location.href = 'manage_products.php';</script>";
}
?>
