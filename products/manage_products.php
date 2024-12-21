<?php
include '../config.php'; // Include your database connection configuration

session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: ../admin/");
    exit();
}

// Fetch categories from the database
$categories = [];
$categorySql = "SELECT DISTINCT category FROM products";
$categoryResult = $conn->query($categorySql);
while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row['category'];
}

// Fetch subcategories based on the selected category
$subcategories = [];
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
if ($selectedCategory) {
    $subcategorySql = "SELECT DISTINCT subcategory FROM products WHERE category = ?";
    $stmt = $conn->prepare($subcategorySql);
    $stmt->bind_param('s', $selectedCategory);
    $stmt->execute();
    $subcategoryResult = $stmt->get_result();
    while ($row = $subcategoryResult->fetch_assoc()) {
        $subcategories[] = $row['subcategory'];
    }
}

// Fetch products based on the selected category and subcategory
$productSql = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = '';
if ($selectedCategory) {
    $productSql .= " AND category = ?";
    $params[] = $selectedCategory;
    $types .= 's';
}
if (isset($_GET['subcategory']) && !empty($_GET['subcategory'])) {
    $subcategory = $_GET['subcategory'];
    $productSql .= " AND subcategory = ?";
    $params[] = $subcategory;
    $types .= 's';
}
$stmt = $conn->prepare($productSql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

.container {
    width: 80%;
    margin: auto;
    overflow: hidden;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

/* Filter Container */
.filter-container {
    margin-bottom: 20px;
    text-align: center;
}

.filter-container form {
    display: inline-block;
    margin: 0 auto;
}

.filter-container select {
    padding: 10px;
    font-size: 16px;
    margin-right: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #fff;
}

.filter-container input[type="submit"] {
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

.filter-container input[type="submit"]:hover {
    background-color: #0056b3;
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    background-color: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #007bff;
    color: white;
    font-weight: bold;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

td img {
    border-radius: 4px;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

.button {
    padding: 6px 10px;
    color: #fff;
    background-color: #007bff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-align: center;
    display: inline-block;
    text-decoration: none;
}

.button:hover {
    background-color: #0056b3;
}

.actions {
    text-align: center;
}

.actions a {
    margin: 0 5px;
}

.actions a.delete {
    color: red;
}

.actions a.delete:hover {
    text-decoration: underline;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .container {
        width: 95%;
    }

    .filter-container select, 
    .filter-container input[type="submit"] {
        display: block;
        margin: 10px auto;
    }
}

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

    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Products</h1>
        <div class="filter-container">
            <form method="GET" action="">
                <select name="category" onchange="this.form.submit()">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>" <?php echo ($category === $selectedCategory) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if ($selectedCategory): ?>
                    <select name="subcategory" onchange="this.form.submit()">
                        <option value="">Select Subcategory</option>
                        <?php foreach ($subcategories as $subcategory): ?>
                            <option value="<?php echo htmlspecialchars($subcategory); ?>" <?php echo (isset($_GET['subcategory']) && $_GET['subcategory'] === $subcategory) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($subcategory); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
                
                <input type="submit" value="Filter">
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>Images</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['price']); ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td><?php echo htmlspecialchars($row['subcategory']); ?></td>
                            <td>
                                <?php 
                                $images = explode(',', $row['images']);
                                foreach ($images as $image) {
                                    echo '<img src="uploads/' . htmlspecialchars($image) . '" alt="Product Image" width="100">';
                                }
                                ?>
                            </td>
                            <td class="actions">
                                <a href="update_product.php?id=<?php echo $row['id']; ?>" class="button">Edit</a><br/><br/>
                                <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="button delete">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No products found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
<p style="text-align:center">
<a href="http://localhost/shopping/admin/admin_dashboard.php">back to dashboard</a></p>
    </div>
</body>
</html>

<?php
$conn->close();
?>
