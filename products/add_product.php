<?php
include '../config.php'; // Include your database connection configuration

session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: ../admin/");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $subcategory = isset($_POST['subcategory']) ? $_POST['subcategory'] : null; // Default to null if not set

    // Handle file uploads
    $images = [];
    if (isset($_FILES['images']) && $_FILES['images']['error'][0] != 4) { // Check if files are uploaded
        $fileCount = count($_FILES['images']['name']);
        $uploadDir = 'uploads/';
        
        // Ensure upload directory exists and is writable
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
            die("Error: Unable to create upload directory.");
        }
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                echo "<script>alert('Failed to upload image: " . $_FILES['images']['name'][$i] . "'); window.location.href = 'add_product.php';</script>";
                exit();
            }
            
            $tmpName = $_FILES['images']['tmp_name'][$i];
            $fileName = basename($_FILES['images']['name'][$i]);
            $uploadFile = $uploadDir . $fileName;
            
            if (move_uploaded_file($tmpName, $uploadFile)) {
                $images[] = $fileName;
            } else {
                echo "<script>alert('Failed to move uploaded file: $fileName'); window.location.href = 'add_product.php';</script>";
                exit();
            }
        }
    }

    // Prepare SQL query to insert product details
    $sql = "INSERT INTO products (name, description, price, category, subcategory, images) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $imagesString = implode(',', $images); // Convert array to comma-separated string
    
    // Adjust binding based on the presence of subcategory
    if ($subcategory === null) {
        $stmt->bind_param("sssss", $name, $description, $price, $category, $imagesString);
    } else {
        $stmt->bind_param("ssssss", $name, $description, $price, $category, $subcategory, $imagesString);
    }

    if ($stmt->execute()) {
        // Create category and subcategory files if they don't exist
        createCategoryPage($category, $subcategory);

        echo "<script>alert('Product added successfully!'); window.location.href = '../admin/admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: Could not add product. Please try again.'); window.location.href = 'add_product.php';</script>";
    }

    $stmt->close();
    $conn->close();
}

function createCategoryPage($category, $subcategory) {
    // Define base directory relative to the current script's directory
    $baseDir = __DIR__ . '/' . $category . '/';

    // Create category directory if it doesn't exist
    if (!is_dir($baseDir)) {
        mkdir($baseDir, 0777, true);
    }

    $filePath = $baseDir . $subcategory . '.php';

    $fileName = $subcategory . '.php';
    $filePath = $baseDir . $fileName;

    if (!file_exists($filePath)) {
        $content = "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>" . htmlspecialchars(ucfirst($subcategory)) . "</title>
    <link rel=\"stylesheet\" href=\"styles.css\">
<styles>

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
    width: 150px; 
    height: 150px; 
    object-fit: cover;
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
</styles>
</head>
<body>
<header>
    <h1>" . htmlspecialchars(ucfirst($subcategory)) . "</h1>
</header>

<div class=\"container\">
    <?php
    // Database connection
    include '../../config.php'; 

    // Fetch products from " . htmlspecialchars($category) . " category and " . htmlspecialchars($subcategory) . " sub-category
    \$sql = \"SELECT * FROM products WHERE category = '" . htmlspecialchars($category) . "' AND subcategory = '" . htmlspecialchars($subcategory) . "'\";
    \$result = \$conn->query(\$sql);

    if (\$result->num_rows > 0) {
        // Output each product
        while (\$row = \$result->fetch_assoc()) {
            echo '<div class=\"product\">';
            echo '<img src=\"../uploads/' . htmlspecialchars(\$row['images']) . '\" alt=\"' . htmlspecialchars(\$row['name']) . '\">';
            echo '<div class=\"product-details\">';
            echo '<h3>' . htmlspecialchars(\$row['name']) . '</h3>';
            echo '<p>' . htmlspecialchars(\$row['description']) . '</p>';
            echo '<p class=\"price\">₹' . htmlspecialchars(\$row['price']) . '</p>';
            echo '<button onclick=\"addToCart(\'' . htmlspecialchars(\$row['name']) . '\', ' . htmlspecialchars(\$row['price']) . ')\">Add to Cart</button>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>No products found in this category.</p>';
    }

    echo '<div style="text-align:center">';
    echo '<a href="../../products.php">Back</a>';
    echo '</div>';

    \$conn->close();
    ?>

</div>

<script>
    // Function to add product to cart
    function addToCart(productName, price) {
        // Get the cart from localStorage or initialize an empty array
        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        // Check if the product is already in the cart
        const existingProduct = cart.find(item => item.name === productName);
        
        if (existingProduct) {
            // Increase quantity if the product is already in the cart
            existingProduct.quantity += 1;
        } else {
            // Add new product to the cart
            cart.push({
                name: productName,
                price: price,
                quantity: 1
            });
        }

        // Save the updated cart to localStorage
        localStorage.setItem('cart', JSON.stringify(cart));

        // Optionally, update UI or provide feedback
        alert(`${productName} has been added to your cart.`);
    }
</script>
</body>
</html>";

        file_put_contents($filePath, $content);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <style>
    /* General Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    /* Form Container */
    .form-container {
        max-width: 600px;
        margin: 30px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    /* Form Styles */
    form {
        display: flex;
        flex-direction: column;
    }

    label {
        margin-bottom: 5px;
        color: #555;
    }

    input[type="text"],
    input[type="number"],
    textarea,
    select {
        margin-bottom: 15px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    button {
        padding: 10px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background-color: #0056b3;
    }

    input[type="file"] {
        border: none;
        padding: 0;
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

    /* Responsive Design */
    @media (max-width: 600px) {
        .form-container {
            margin: 20px;
            padding: 15px;
        }
    }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add New Product</h2>
        <form action="add_product.php" method="post" enctype="multipart/form-data">
            <label for="name">Product Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required>
            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="fashion">Fashion</option>
                <option value="electronics">Electronics</option>
                <option value="kids">Kids</option>
                <option value="gaming">Gaming</option>
                <option value="sports">Sports</option>
                <option value="kitchen">Kitchen</option>
                <option value="books">Books</option>
                <option value="office">Office</option>
            </select>
            <label for="subcategory">Subcategory:</label>
            <select id="subcategory" name="subcategory">
                <!-- Subcategories will be populated based on the selected category using JavaScript -->
            </select>
            <label for="images">Product Images:</label>
            <input type="file" id="images" name="images[]" multiple required> <br/>
            <button type="submit">Add Product</button>
        </form>

<div style="text-align:center">
        <a href="../admin/admin_dashboard.php">Back</a>
</div>

    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category');
        const subcategorySelect = document.getElementById('subcategory');

        const subcategories = {
            'electronics': ['computers', 'watches', 'fans', 'television', 'mobiles', 'earphones', 'washing machines', 'camera'],
            'fashion': ['clothings', 'goggles', 'earrings', 'necklaces', 'shoes', 'caps', 'bangles', 'cosmetics']
            // Add more categories and subcategories here as needed
        };

        categorySelect.addEventListener('change', function() {
            const category = categorySelect.value;
            subcategorySelect.innerHTML = ''; // Clear existing options

            if (subcategories[category]) {
                subcategories[category].forEach(subcat => {
                    const option = document.createElement('option');
                    option.value = subcat;
                    option.textContent = subcat;
                    subcategorySelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Select a subcategory';
                subcategorySelect.appendChild(option);
            }
        });

        // Trigger the change event to populate subcategories based on the initial category (if any)
        categorySelect.dispatchEvent(new Event('change'));
    });
    </script>
</body>
</html>
