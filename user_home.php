<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to signin.html if the user is not logged in
    header("Location: signin.html");
    exit();
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

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome, User</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://www.paypal.com/sdk/js?client-id=YOUR_PAYPAL_CLIENT_ID"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<style>

.profile-photo {
    text-align: center;
    margin-bottom: 20px;
}

.profile-photo img {
    width: 25px; /* Adjust the width as needed */
    height: 25px; /* Ensure the height matches the width for a circle */
    border-radius: 50%;
    border: 2px solid #ddd;
    padding: 4px; /* Adjust padding for a smaller border */
    object-fit: cover; /* Ensure the image covers the area properly */
}

.profile-photo form {
    margin-top: 20px;
}

.profile-photo input[type="file"] {
    display: none;
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

.profile-photo label {
    background-color: #007bff;
    color: white;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
}

/* User menu styling */
.user-menu {
    position: relative;
    display: inline-block;
}

.user-photo {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
}

.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    background-color: white;
    min-width: 110px;
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

.dropdown-content a:hover {
    background-color: #f1f1f1;
}

.user-menu:hover .dropdown-content {
    display: block;
}
</style>
</head>
<body>

<section class="top-bar">
  <div class="container">
    <div class="branding">
      <img src="path_to_your_branding_image" alt="Brand Logo" class="brand-logo">
    </div>
    <div class="search-bar">
      <select class="category-select">
        <option value="all">All Categories</option>
        <option value="electronics">Electronics</option>
        <option value="music">Music</option>
        <option value="clothing">Clothing</option>
        <!-- Add more categories as needed -->
      </select>
      <input type="text" placeholder="Search...">
      <button type="button">Search</button>
    </div>
    <div class="top-options">
      <select class="language-select">
        <option value="en">English</option>
        <option value="es">Español</option>
        <!-- Add more languages as needed -->
      </select>

<div class="user-menu">
          <div class="profile-photo">
          <img src="<?= htmlspecialchars($user['photo']) ?>" alt="User Photo" id="user-photo" width="150">
          <input type="file" name="photo" id="photo">
          </div>
        <div class="dropdown-content">
            <a href="user_profile.php">My Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>  
      
      <a href="cart.html" class="cart"><img src="images/Cart.JPG" alt="Cart Icon" class="cart-icon"> 
      <span id="cart-count" class="cart-count">0</span></a>
    </div>
  </div>
</section>

<header>
   <h1>My Shopping Site</h1> <br/>
<!-- Main Menu -->
<section class="main-menu">
  <div class="menu-container">
    <div class="menu-icon" onclick="openNav()">&#9776; All</div>
   <div class="centered-menus">
  <nav><ul><li><a href="#products" class="button1"><span>Products</span></a></li>
        <li><a href="#reviews" class="button1"><span>Reviews</span></a></li>
        <li><a href="#delivery" class="button1"><span>Delivery</span></a></li>
        <li><a href="register.html" class="button1"><span>Register</span></a></li></ul>
  </nav>
  </div>
 </div>
</section>
<!-- Sidebar Menu -->
<div id="sideMenu" class="side-menu">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <h3>Programs & Features</h3>
  <ul>
    <li><a href="#">Become an Affiliate</a></li>
    <li><a href="#">Advertise Your Products</a></li>
  </ul>
  <h3>Shop by Categories</h3>
  <ul>
    <li><a href="javascript:void(0)" onclick="toggleSubMenu('electronicsSubMenu')">Electronics</a>
      <ul id="electronicsSubMenu" class="submenu">
        <li><a href="#">Camera</a></li>
        <li><a href="#">Cell Phones</a></li>
        <li><a href="#">Computers</a></li>
        <li><a href="#">Headphones</a></li>
        <li><a href="#">Television</a></li>
      </ul>
    </li>
    <li><a href="javascript:void(0)" onclick="toggleSubMenu('clothingSubMenu')">Clothing</a>
      <ul id="clothingSubMenu" class="submenu">
        <li><a href="#">Men's Wear</a></li>
        <li><a href="#">Women's Wear</a></li>
      </ul>
    </li>
    <li><a href="javascript:void(0)" onclick="toggleSubMenu('musicSubMenu')">Music</a>
      <ul id="musicSubMenu" class="submenu">
        <li><a href="#">CDs</a></li>
        <li><a href="#">Cassettes</a></li>
      </ul>
    </li>
  </ul>
  <h3>Help & Settings</h3>
  <ul>
    <li><a href="#">Your Account</a></li>
    <li><a href="customerservice.html">Customer Service</a></li>
    <li><a href="signin.html">Sign In</a></li>
  </ul>
</div>
</header>

<!-- User Activity Section -->
<section class="user-activity">
  <h2>Welcome Back, <span id="user-name"></span>!</h2>
  <div class="activity-list">
    <!-- This will be dynamically updated with user activity -->
     <!--<ul id="activity-items">
      Example items 
      <li></li>
      <li></li>
      <li></li>
    </ul>-->
  </div>
  <a href="purchase-history.html">View History</a>
</section>

<!-- Slider Section -->
<div class="container2">
  <div class="mySlides"><img src="images/image1.jpg" alt="Slide 1"></div>
  <div class="mySlides"><img src="images/image2.jpg" alt="Slide 2"></div>
  <div class="mySlides"><img src="images/image3.jpg" alt="Slide 3"></div>
  <div class="mySlides"><img src="images/image4.jpg" alt="Slide 4"></div>
  <!-- Next and previous buttons -->
  <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
  <a class="next" onclick="plusSlides(1)">&#10095;</a>
</div>

<!-- Products Section -->
<section id="products" class="products-section">
  <div class="product-row1">
    <div class="product">
      <img src="images/fashion.jpg" alt="Fashion">
      <label>Fashion</label>
      <a href="products/fashion.html">Shop Now</a>
    </div>
    <div class="product">
      <img src="images/electronics.jpg" alt="Electronics">
      <label>Electronics</label>
      <a href="products/electronics.html">Shop Now</a>
    </div>
    <div class="product">
      <img src="images/kids.jpg" alt="Kids">
      <label>Kids</label>
      <a href="kids.html">Shop Now</a>
    </div>
    <div class="product">
      <img src="images/gaming.jpg" alt="Gaming">
      <label>Gaming</label>
      <a href="products/gaming.html">Shop Now</a>
    </div>
  </div>
  <div class="product-row2">
    <div class="product">
      <img src="images/sports.jpg" alt="Sports">
      <label>Sports</label>
      <a href="products/sports.html">Shop Now</a>
    </div>
    <div class="product">
      <img src="images/kitchen.jpg" alt="Kitchen">
      <label>Kitchen</label>
      <a href="products/kitchen.html">Shop Now</a>
    </div>
    <div class="product">
      <img src="images/books.jpg" alt="Books">
      <label>Books</label>
      <a href="products/books.html">Shop Now</a>
    </div>
    <div class="product">
      <img src="images/office.jpg" alt="Office">
      <label>Office</label>
      <a href="products/office.html">Shop Now</a>
    </div>
  </div>
</section>

<!-- Footer Section -->
<section class="footer-links">
  <div class="container3">
    <div class="column">
      <h2>Get to Know Us</h2>
      <ul>
        <li><a href="#">Careers</a></li>
        <li><a href="#">Blog</a></li>
        <li><a href="#">About Us</a></li>
        <li><a href="#">Investor Relations</a></li>
      </ul>
    </div>
    <div class="column">
      <h2>Make Money with Us</h2>
      <ul>
        <li><a href="#">Sell Products</a></li>
        <li><a href="#">Become an Affiliate</a></li>
        <li><a href="#">Advertise Your Products</a></li>
      </ul>
    </div>
    <div class="column">
      <h2>Let Us Help You</h2>
      <ul>
        <li><a href="#">Your Account</a></li>
        <li><a href="#">Your Orders</a></li>
        <li><a href="#">Shipping Rates & Policies</a></li>
        <li><a href="#">Returns & Replacements</a></li>
        <li><a href="#">Manage Your Content and Devices</a></li>
        <li><a href="#">Help</a></li>
      </ul>
    </div>
  </div>
</section>

<div class="back-to-top">
    <a href="#top">Back to Top</a>
</div>

<footer>
  <a href="conditions.html">Conditions of Use</a> <a href="privacy.html">Privacy Notice</a> <a href="help.html">Help</a>
  <p>&copy; 2024 My Shopping Site</p>
</footer>

<script src="script.js"></script>
<script>
  // JavaScript to populate user name and activity
  document.addEventListener('DOMContentLoaded', function() {
    // Example user data
    const userName = 'User'; // Replace with dynamic data
    document.getElementById('user-name').textContent = userName;

    // Example user activity
    const activityItems = [
      'Purchased Product A',
      'Purchased Product B',
      'Viewed Product C'
    ];

    const activityList = document.getElementById('activity-items');
    activityItems.forEach(item => {
      const li = document.createElement('li');
      li.textContent = item;
      activityList.appendChild(li);
    });
  });


// Function to update the cart count
function updateCartCount() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let totalItems = cart.reduce((total, item) => total + item.quantity, 0);
    document.getElementById('cart-count').textContent = totalItems;
}

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

    // Update the cart count
    updateCartCount();

    // Optionally, update UI or provide feedback
    alert(`${productName} has been added to your cart.`);
}

// Update cart count on page load
document.addEventListener('DOMContentLoaded', updateCartCount);


</script>

</body>
</html>
