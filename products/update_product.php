<?php
// Include the database configuration file
include '../config.php';

// Get the product ID from the URL
$id = $_GET['id'];

// Fetch product details from the database
$query = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the updated product details from the form
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $subcategory = $_POST['subcategory'];

    // Handle image upload
    if (!empty($_FILES['images']['name'])) {
        $image_name = $_FILES['images']['name'];
        $image_tmp_name = $_FILES['images']['tmp_name'];
        $image_folder = 'uploads/' . $image_name;
        move_uploaded_file($image_tmp_name, $image_folder);
    } else {
        $image_name = $product['images'];
    }

    // Update product details in the database
    $update_query = "UPDATE products SET name = ?, description = ?, price = ?, category = ?, subcategory = ?, images = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssdsssi", $name, $description, $price, $category, $subcategory, $image_name, $id);
    
    if ($update_stmt->execute()) {
        echo "Product updated successfully.";
    } else {
        echo "Error updating product: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
    <style>

.container {
    background-color: #fff;
    padding: 35px;
    border-radius: 10px;
    border: 1px solid blue;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 400px;
    margin: auto; /* Center the container horizontally */
}


h1 {
    text-align: center;
    color: #444;
    margin-bottom: 20px;
    font-size: 24px;
}

form {
    display: flex;
    flex-direction: column;
}

label {
    margin-bottom: 8px;
    font-weight: bold;
    color: #333;
}

input[type="text"],
input[type="number"],
textarea,
input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 3px solid grey;
    border-radius: 10px;
    font-size: 14px;
}

textarea {
    resize: vertical;
    height: 120px;
}

input[type="submit"] {
    background-color: #007bff;
    color: #fff;
    padding: 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    width: 100%;
}

input[type="submit"]:hover {
    background-color: #0056b3;
}

img {
    display: block;
    margin-top: 10px;
    margin-left: 140px;
    max-width: 100px;
    border-radius: 5px;
}
</style>
</head>
<body>
<div class="container">
    <h1>Update Product</h1>
    <form action="update_product.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $product['name']; ?>" required><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?php echo $product['description']; ?></textarea><br>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" value="<?php echo $product['price']; ?>" required><br>

        <label for="category">Category:</label>
        <input type="text" id="category" name="category" value="<?php echo $product['category']; ?>" required><br>

        <label for="subcategory">Subcategory:</label>
        <input type="text" id="subcategory" name="subcategory" value="<?php echo $product['subcategory']; ?>" required><br>

        <label for="images">Images:</label>
        <input type="file" id="images" name="images"><br>
        <img src="uploads/<?php echo $product['images']; ?>" alt="<?php echo $product['name']; ?>" width="100"><br>

        <input type="submit" value="Update Product">
    </form>
</div>
</body>
</html>
