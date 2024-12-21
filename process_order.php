<?php
session_start();
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['address'], $data['paymentMethod'], $data['cart'], $data['totalAmount'])) {
    try {
        // Insert order into database
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, address, payment_method, total_amount) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'], // Assumes user ID is stored in session
            json_encode($data['address']),
            $data['paymentMethod'],
            $data['totalAmount']
        ]);

        // Get the last inserted order ID
        $orderId = $pdo->lastInsertId();

        // Insert cart items into order_items table
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($data['cart'] as $item) {
            $stmt->execute([
                $orderId,
                $item['id'],
                $item['quantity'],
                $item['price']
            ]);
        }

        // Respond with success
        $response = ['success' => true];
        if ($data['paymentMethod'] === 'cod') {
            $response['message'] = 'Order placed successfully! You will pay cash on delivery.';
        } else {
            // Set payment URL for other methods
            $response['paymentUrl'] = 'payment_gateway_url'; // Replace with actual URL
        }

        echo json_encode($response);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid order data.']);
}
?>
<?php
session_start();

// Database connection
require_once 'config.php'; // Assuming this file contains database connection settings

$data = [
    'address' => $_POST['address'],
    'paymentMethod' => $_POST['paymentMethod'],
    'cart' => json_decode($_POST['cart'], true),
    'totalAmount' => $_POST['totalAmount']
];

if (isset($data['address'], $data['paymentMethod'], $data['cart'], $data['totalAmount'])) {
    try {
        // Insert order into database
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, address, payment_method, total_amount) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'], // Assumes user ID is stored in session
            json_encode($data['address']),
            $data['paymentMethod'],
            $data['totalAmount']
        ]);

        // Get the last inserted order ID
        $orderId = $pdo->lastInsertId();

        // Insert cart items into order_items table
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($data['cart'] as $item) {
            $stmt->execute([
                $orderId,
                $item['id'],
                $item['quantity'],
                $item['price']
            ]);
        }

        // Handle payment redirection if needed
        $response = ['success' => true];
        if ($data['paymentMethod'] === 'cod') {
            $response['message'] = 'Order placed successfully! You will pay cash on delivery.';
        } else {
            // Set payment URL for other methods
            $response['paymentUrl'] = 'payment_gateway_url'; // Replace with actual URL
        }

        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error processing order: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid order data.']);
}
?>