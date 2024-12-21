<?php
session_start();
include('config.php');

// Check if user or admin is logged in
if (!isset($_SESSION['user_id']) && (!isset($_SESSION['admin_id']) || !$_SESSION['logged_in'])) {
    header('Location: signin.html'); // Redirect to user login if not authenticated
    exit();
}

if (isset($_SESSION['admin_id']) && !$_SESSION['logged_in']) {
    header("Location: ../admin/"); // Redirect to admin login if not authenticated
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch user details for pre-filling the form
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();
$user_stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize variables for form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $mobile_number = $_POST['mobile_number'];
    $street_address = $_POST['street_address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip_code = $_POST['zip_code'];
    $country = $_POST['country'];
    $password = $_POST['password'];
    $photo = $_FILES['photo']['name'];
    $photo_tmp = $_FILES['photo']['tmp_name'];

    // Prepare to update user details
    $update_stmt = $conn->prepare("UPDATE users SET email = ?, mobile_number = ?, street_address = ?, city = ?, state = ?, zip_code = ?, country = ?" . ($password ? ", password = ?" : "") . ($photo ? ", photo = ?" : "") . " WHERE id = ?");
    if ($update_stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $params = [$email, $mobile_number, $street_address, $city, $state, $zip_code, $country];
    if ($password) {
        $params[] = password_hash($password, PASSWORD_BCRYPT); // Hash password
    }
    if ($photo) {
        $photo_path = "users/" . basename($photo);
        move_uploaded_file($photo_tmp, $photo_path);
        $params[] = $photo_path;
    }
    $params[] = $user_id;

    $types = str_repeat('s', count($params) - 2) . ($password ? 's' : '') . ($photo ? 's' : '') . 'i';
    $update_stmt->bind_param($types, ...$params);

    if ($update_stmt->execute()) {
        header('Location: user_profile.php'); // Redirect to profile page
    } else {
        die("Update failed: " . $conn->error);
    }
    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
            width:10%
            
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
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
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
        .profile-photo label {
            display: block;
            margin-top: 10px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-align: center;
        }
        input[type="file"] {
            display: none;
        }
        label, input {
            width: 100%;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <form action="update_profile.php" method="post" enctype="multipart/form-data">
            <div class="profile-photo">
                <img src="<?= htmlspecialchars($user['photo']) ?>" alt="User Photo" id="user-photo" width="150">
                <label for="photo">Change Photo</label>
                <input type="file" name="photo" id="photo">
            </div>

            <label for="firstname">First Name:</label>
            <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
  
            <label for="lastname">Last Name:</label>
            <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>

            <label for="username">Username:</label>
            <input type="text" name="username" id="email" value="<?= htmlspecialchars($user['username']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            
            <label for="mobile">Mobile Number:</label>
            <input type="text" name="mobile_number" id="mobile" value="<?= htmlspecialchars($user['mobile_number']) ?>">
            
            <label for="street_address">Street:</label>
            <input type="text" name="street_address" id="street_address" value="<?= htmlspecialchars($user['street_address']) ?>" required>
            
            <label for="city">City:</label>
            <input type="text" name="city" id="city" value="<?= htmlspecialchars($user['city']) ?>" required>
            
            <label for="state">State:</label>
            <input type="text" name="state" id="state" value="<?= htmlspecialchars($user['state']) ?>" required>
            
            <label for="zip_code">Zip Code:</label>
            <input type="text" name="zip_code" id="zip_code" value="<?= htmlspecialchars($user['zip_code']) ?>" required>
            
            <label for="country">Country:</label>
            <input type="text" name="country" id="country" value="<?= htmlspecialchars($user['country']) ?>" required>
            
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="Leave blank to keep current password">
            
            <button type="submit">Update Profile</button>

            <br/>
            <div style="text-align:center">
            <a href="user_profile.php">Back</a>
            </div>
        </form>
    </div>
</body>
</html>
