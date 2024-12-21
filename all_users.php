<?php
session_start();
include('config.php');

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: admin/");
    exit();
}

// Fetch all users
$users_stmt = $conn->prepare("SELECT * FROM users");
$users_stmt->execute();
$users_result = $users_stmt->get_result();
$users_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users</title>
    <style>

a {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            color:red;
            background-color: #007bff;
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
        .container {
            max-width: 1200px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .actions a {
            margin-right: 10px;
            color: white;
            text-decoration: none;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        .back-link {
            display: block;
            margin: 20px 0;
            text-align: center;
        }
        .back-link a {
            color: white;
            text-decoration: none;
            font-size: 16px;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>All Users</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Mobile Number</th>
                    <th>Address</th>
                  
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['first_name']) ?></td>
                        <td><?= htmlspecialchars($user['last_name']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['mobile_number']) ?></td>
                        <td><?= htmlspecialchars($user['street_address']) . ", " . htmlspecialchars($user['city']) . ", " . htmlspecialchars($user['state']) . " - " . htmlspecialchars($user['zip_code']) . ", " . htmlspecialchars($user['country']) ?></td>
                        <td class="actions">
                            <a href="update_profile.php?id=<?= htmlspecialchars($user['id']) ?>">Edit</a>
                            <a href="delete_user.php?id=<?= htmlspecialchars($user['id']) ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="back-link">
            <a href="admin/admin_dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
