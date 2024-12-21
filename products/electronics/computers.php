<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computers</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <h1>Computers</h1>
</header>

<div class="container">

    <?php
    // Database connection
    include '../../config.php'; 

    // Fetch products from Electronics category and Computers sub-category
    $sql = "SELECT * FROM products WHERE category = 'electronics' AND subcategory = 'Computers'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output each product
        while ($row = $result->fetch_assoc()) {
            echo '<div class="product">';
            echo '<img src="../uploads/' . htmlspecialchars($row['images']) . '" alt="' . htmlspecialchars($row['name']) . '">';
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
    echo '<div style="text-align:center">';
    echo '<a href="../../products.php">Back</a>';
    echo '</div>';
    $conn->close();
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
</html>
