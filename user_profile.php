<?php
session_start();
include('config.php');

// Check if connection is successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.html'); // Redirect to login if not authenticated
  exit();
}

// Verify table existence
$tables = $conn->query("SHOW TABLES");
$table_names = [];
while ($row = $tables->fetch_array()) {
    $table_names[] = $row[0];
}

$required_tables = ['users', 'purchased_products', 'cart', 'reviews'];
foreach ($required_tables as $table) {
    if (!in_array($table, $table_names)) {
        die("Error: Table '$table' does not exist.");
    }
}

// Fetch user details securely using prepared statements
$user_id = $_SESSION['user_id'];
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
if ($user_stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();
$user_stmt->close();

// Fetch purchased products
$purchased_products_stmt = $conn->prepare("SELECT * FROM purchased_products WHERE user_id = ?");
if ($purchased_products_stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$purchased_products_stmt->bind_param("i", $user_id);
$purchased_products_stmt->execute();
$purchased_products = $purchased_products_stmt->get_result();
$purchased_products_stmt->close();

// Fetch cart items
$cart_items_stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
if ($cart_items_stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$cart_items_stmt->bind_param("i", $user_id);
$cart_items_stmt->execute();
$cart_items = $cart_items_stmt->get_result();
$cart_items_stmt->close();

// Fetch user reviews
$reviews_stmt = $conn->prepare("SELECT * FROM reviews WHERE user_id = ?");
if ($reviews_stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$reviews_stmt->bind_param("i", $user_id);
$reviews_stmt->execute();
$reviews = $reviews_stmt->get_result();
$reviews_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
<style>

a {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #0056b3;
            color: #fff;
        }
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.profile-container {
    max-width: 800px;
    margin: 50px auto;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1, h2 {
    text-align: center;
}

.profile-photo {
    text-align: center;
    margin-bottom: 20px;
}

.profile-photo img {
    border-radius: 50%;
    border: 2px solid #ddd;
    padding: 10px;
}

.profile-photo form {
    margin-top: 20px;
}

.profile-photo input[type="file"] {
    display: none;
}

.profile-photo label {
    background-color: #007bff;
    color: white;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
}

.profile-photo button {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
}

.user-details {
    margin-bottom: 20px;
}

.user-details p {
    margin: 10px 0;
    font-size: 1.1em;
}

ul {
    list-style-type: none;
    padding: 0;
}

ul li {
    background: #f9f9f9;
    margin: 10px 0;
    padding: 10px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

</style>
</head>
<body>
    <div class="profile-container">
        <h1>User Profile</h1>
        <div class="profile-photo">
            <img src="<?= htmlspecialchars($user['photo']) ?>" alt="User Photo" id="user-photo" width="150">
                <input type="file" name="photo" id="photo">
        </div>
     <div class="user-details">
    <p><strong>Name:</strong> <?= htmlspecialchars($user['first_name']) . " " . htmlspecialchars($user['last_name']) ?></p>
    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Mobile Number:</strong> <?= htmlspecialchars($user['mobile_number']) ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($user['street_address']) . ", " . htmlspecialchars($user['city']) . ", " . htmlspecialchars($user['state']) . " - " . htmlspecialchars($user['zip_code']) . ", " . htmlspecialchars($user['country']) ?></p>
</div>

        <h2>Purchased Products</h2>
        <ul>
            <?php while($product = $purchased_products->fetch_assoc()): ?>
                <li><?= htmlspecialchars($product['name']) ?> - Purchased on <?= htmlspecialchars($product['purchase_date']) ?></li>
            <?php endwhile; ?>
        </ul>

        <h2>Cart Items</h2>
        <ul>
            <?php while($item = $cart_items->fetch_assoc()): ?>
                <li><?= htmlspecialchars($item['name']) ?> - Quantity: <?= htmlspecialchars($item['quantity']) ?></li>
            <?php endwhile; ?>
        </ul>

        <h2>Reviews</h2>
        <ul>
            <?php while($review = $reviews->fetch_assoc()): ?>
                <li><?= htmlspecialchars($review['name']) ?> - <?= htmlspecialchars($review['rating']) ?>/5 - <?= htmlspecialchars($review['review_text']) ?> (Reviewed on <?= htmlspecialchars($review['review_date']) ?>)</li>
            <?php endwhile; ?>
        </ul>
         <!-- Edit Profile Form -->
<div style="text-align:center">
        <a href="update_profile.php">Edit Profile</a><br/>
        <a href="user_home.php">Back</a>
</div>
        </div>
</body>
</html>
