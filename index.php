<?php
session_start();
include('config.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to user_home.php if the user is logged in
    header("Location: user_home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Website</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://www.paypal.com/sdk/js?client-id=YOUR_PAYPAL_CLIENT_ID"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>

<section class="top-bar">
        <div class="container">
            <div class="branding">
                <img src="path/to/logo.png" alt="Brand Logo" class="brand-logo">
            </div>
            <div class="search-bar">
                <select class="category-select" id="categorySelect">
                    <option value="all">All Categories</option>
                    <option value="fashion">Fashion</option>
                    <option value="electronics">Electronics</option>
                    <option value="kids">Kids</option>
                    <option value="gaming">Gaming</option>
                    <option value="sports">Sports</option>
                    <option value="kitchen">Kitchen</option>
                    <option value="books">Books</option>
                    <option value="office">Office</option>
                </select>
                <form id="searchForm" onsubmit="return performSearch()">
                    <input type="text" id="searchInput" placeholder="Search...">
                    <button type="submit">Search</button>
                </form>
            </div>
            <div class="top-options">
                <select class="language-select">
                    <option value="en">English</option>
                    <option value="es">Español</option>
                    <!-- Add more languages as needed -->
                </select>
                <a href="signin.html" class="sign-in">Sign In</a>&nbsp&nbsp&nbsp&nbsp&nbsp

<a href="cart.html" class="cart">
    <img src="images/Cart.JPG" alt="Cart Icon" class="cart-icon">
    <span id="cart-count" class="cart-count">0</span>
</a>
             </div>
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
  <nav><ul><li><a href="products.php" class="button1"><span>Products</span></a></li>
        <!--<li><a href="#reviews" class="button1"><span>Reviews</span></a></li>-->
        <li><a href="#about" class="button1"><span>About</span></a></li>
        <li><a href="contact.html" class="button1"><span>Contact</span></a></li>
        <li><a href="register.html" class="button1"><span>Register</span></a></li>
        </ul>
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
    <li><a href="signin.html">Your Account</a></li>
    <li><a href="customerservice.html">Customer Service</a></li>
    <li><a href="signin.html">Sign In</a></li>
  </ul>
</div>
</header>
<div class="container2">
   <div class="mySlides"><img src="images/image1.jpg"></div>
  <div class="mySlides"><img src="images/image2.jpg"></div>
  <div class="mySlides"><img src="images/image3.jpg"></div>
  <div class="mySlides"><img src="images/image4.jpg"></div>
  <!-- Next and previous buttons -->
  <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
  <a class="next" onclick="plusSlides(1)">&#10095;</a>
</div>

<br/>

           <section id="products" class="products-section">
                   <div class="product-row1">
                       <div class="product">
                       <img src="images/fashion.jpg" alt="Fashion">
                        <label>Fashion</label>
                        <a href="products/fashion.html">Shop Now</a></div>
                        <div class="product">
                        <img src="images/electronics.jpg" alt="Electronics">
                        <label>Electronics</label>
                        <a href="products/electronics.html">Shop Now</a></div>
                        <div class="product">
                        <img src="images/kids.jpg" alt="Kids">
                        <label>Kids</label>
                        <a href="products/kids.html">Shop Now</a></div>
                        <div class="product">
                        <img src="images/gaming.jpg" alt="Gaming">
                        <label>Gaming</label>
                        <a href="products/gaming.html">Shop Now</a></div>
                       </div>
                    </div>
                    <div class="product-row2">
                        <div class="product">
                        <img src="images/sports.jpg" alt="Sports">
                        <label>Sports</label>
                        <a href="products/sports.html">Shop Now</a></div>
                        <div class="product">
                        <img src="images/kitchen.jpg" alt="Kitchen">
                        <label>Kitchen</label>
                        <a href="products/kitchen.html">Shop Now</a></div>
                        <div class="product">
                        <img src="images/books.jpg" alt="Books">
                        <label>Books</label>
                        <a href="products/books.html">Shop Now</a></div>
                        <div class="product">
                        <img src="images/office.jpg" alt="Office">
                        <label>Office</label>
                        <a href="products/office.html">Shop Now</a></div>
                        </div>
                   </div>
          </section>

<!--
<section id="delivery">
    <h2>Delivery Options</h2>
    <div class="delivery-options">
        <div class="delivery-option">
            <input type="radio" id="express" name="delivery" value="express">
            <label for="express">Express Delivery - $15</label>
        </div>
        <div class="delivery-option">
            <input type="radio" id="normal" name="delivery" value="normal">
            <label for="normal">Normal Delivery - $5</label>
        </div>
    </div>
</section>

  <section id="reviews">
        <h2>Reviews</h2>
        <form id="reviewForm">
            <label for="review">Leave a review:</label>
            <textarea id="review" name="review" required></textarea>
            <button type="submit">Submit</button>
        </form>
        <div id="reviewsContainer"></div>
    </section>

 -->

<section id = "about" class="about-section">
    <h2>About Us</h2>
    <p>Welcome to My Shopping Site, your one-stop destination for a diverse range of high-quality products. Whether you're looking for the latest trends in fashion, cutting-edge electronics, kids' toys, sports equipment, office supplies, or books, we have something for everyone.
Our mission is to provide an exceptional shopping experience by offering a wide variety of products at competitive prices, backed by excellent customer service. At My Shopping Site, we are committed to sustainability and ethical practices. We work closely with suppliers who share our values to ensure that the products you buy are responsibly sourced and environmentally friendly.<br/><br/>Thank you for choosing My Shopping Site. We look forward to serving you and making your shopping experience enjoyable and rewarding.</p>
</section>


<!-- Section Above Footer -->
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

<script>

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
<script src="script.js"></script>

</body>
</html>
