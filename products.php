<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
include 'config.php';

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
    <title>Products</title>
<style>

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


                /* Main styling for the page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #3498db;
            color: #fff;
            padding: 15px 20px;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 2em;
        }

        .container {
            width: 60%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .product {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-wrap: wrap;
        }

     .product img {
    width: 150px; /* Set a fixed width */
    height: 150px; /* Set a fixed height */
    object-fit: cover; /* Ensure the image covers the area without distortion */
    border-radius: 8px;
    flex: 1;
    margin-right: 20px;
}

        .product-details {
            flex: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background-color: #f8f9fa;
        }

        .product h3 {
            margin: 0;
            font-size: 1.5em;
        }

        .product p {
            font-size: 1em;
            font-weight: normal;
            margin: 10px 0;
        }

        .product .price {
            font-size: 1.2em;
            font-weight: bold;
        }

        .product button {
            padding: 10px 20px;
            font-size: 1em;
            color: #fff;
            background-color: #3498db;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width:40%;
        }

        .product button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
<header>
    <h1>Our Products</h1>
</header>

<br/>
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

<div class="container">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="product">';
            echo '<img src="products/uploads/' . htmlspecialchars($row['images']) . '" alt="' . htmlspecialchars($row['name']) . '">';
            echo '<div class="product-details">';
            echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
            echo '<p>' . htmlspecialchars($row['description']) . '</p>';
            echo '<p class="price">₹' . htmlspecialchars($row['price']) . '</p>';
            echo '<button onclick="addToCart(\'' . htmlspecialchars($row['name']) . '\', ' . htmlspecialchars($row['price']) . ')">Add to Cart</button>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>No products found in this category.</p>';
    }

    $conn->close();
    ?>
</div>
<div style="text-align:center">
    <a href="index.php">Back</a>
</div>
<script>
    function addToCart(productName, price) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const existingProduct = cart.find(item => item.name === productName);
        
        if (existingProduct) {
            existingProduct.quantity += 1;
        } else {
            cart.push({
                name: productName,
                price: price,
                quantity: 1
            });
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        alert(`${productName} has been added to your cart.`);
    }
</script>
</body>
</html>
