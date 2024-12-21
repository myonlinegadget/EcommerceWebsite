<?php
session_start();
include('config.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
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

// Store the user's address for display
$current_address = htmlspecialchars($user['street_address']) . ", " . htmlspecialchars($user['city']) . ", " . htmlspecialchars($user['state']) . " - " . htmlspecialchars($user['zip_code']) . ", " . htmlspecialchars($user['country']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract address details
    $addressType = $_POST['address'];
    $address = $addressType === 'new' ? [
        'name' => $_POST['name'],
        'address' => $_POST['address'],
        'city' => $_POST['city'],
        'zip' => $_POST['zip']
    ] : [
        'name' => $user['name'],
        'address' => $user['street_address'],
        'city' => $user['city'],
        'zip' => $user['zip_code']
    ];

    // Extract payment details
    $paymentMethod = $_POST['paymentMethod'];
    $paymentDetails = [];
    if ($paymentMethod === 'creditcard') {
        $paymentDetails = [
            'cardNumber' => $_POST['cardNumber'],
            'expiryDate' => $_POST['expiryDate'],
            'cvv' => $_POST['cvv']
        ];
    } elseif ($paymentMethod === 'netbanking') {
        $paymentDetails = [
            'bank' => $_POST['bank']
        ];
    }

    // Simulate order processing (you should replace this with actual order processing code)
    $orderStatus = 'success'; // Simulated response
    $orderMessage = 'Order placed successfully!';
    
    // Assuming the product ID and quantity are posted (you should update this part as per your form data)
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $purchase_date = date('Y-m-d H:i:s');

    // Insert purchased product into database
    $stmt = $conn->prepare("INSERT INTO purchased_products (user_id, product_id, purchase_date, quantity) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("iisi", $user_id, $product_id, $purchase_date, $quantity);
    $stmt->execute();
    $stmt->close();

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $orderStatus,
        'message' => $orderMessage
    ]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Basic styling */
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .section {
            margin-bottom: 20px;
            display: none; /* Hide all sections initially */
        }
        .section.active {
            display: block; /* Show active section */
        }
        .section h2 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #333;
        }
        .inputBox {
            margin-bottom: 1rem;
        }
        .inputBox label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: bold;
        }
        .inputBox input, .inputBox select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .inputBox input:focus {
            border-color: #007bff;
            outline: none;
        }
        .review-item {
            margin-bottom: 1rem;
        }
        .review-item img {
            max-width: 100px;
        }
        .total-amount {
            font-weight: bold;
        }
        .button {
            padding: 10px 20px;
            font-size: 1em;
            color: #fff;
            background-color: #e67e22;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #d35400;
        }
    </style>
</head>
<body>
    <header>
        <h1>Checkout</h1>
    </header>
    <div class="container">
        <div class="section active" id="address-section">
            <h2>Select Delivery Address</h2>
            <form id="addressForm">
                <div class="inputBox">
                    <label><input type="radio" name="address" value="current" checked> Use current address</label>
                    <div id="currentAddress">
                        <?= $current_address ?>
                    </div>
                </div>
                <div class="inputBox">
                    <label><input type="radio" name="address" value="new"> Deliver to a different address</label>
                </div>
                <div id="newAddressFields" style="display:none;">
                    <div class="inputBox"><label for="name">Full Name:</label><input type="text" id="name" name="name" placeholder="Enter your full name"></div>
                    <div class="inputBox"><label for="address">Address:</label><input type="text" id="address" name="address" placeholder="Enter address"></div>
                    <div class="inputBox"><label for="city">City:</label><input type="text" id="city" name="city" placeholder="Enter city"></div>
                    <div class="inputBox"><label for="zip">Zip Code:</label><input type="text" id="zip" name="zip" placeholder="Enter zip code"></div>
                </div>
                <button type="button" class="button" onclick="showPaymentSection()">Use This Address</button>
            </form>
        </div>

        <div class="section" id="payment-section">
            <h2>Select Payment Method</h2>
            <form id="paymentForm">
                <label><input type="radio" name="paymentMethod" value="creditcard"> Credit/Debit Card</label><br/>
                <label><input type="radio" name="paymentMethod" value="netbanking"> Netbanking</label><br/>
                <div id="bankSelection" class="inputBox" style="display:none;">
                    <label for="bank">Select Bank:</label>
                    <select id="bank" name="bank">
                        <option value="sbi">State Bank of India</option>
                        <option value="canara">Canara Bank</option>
                        <option value="union">Union Bank of India</option>
                        <option value="hdfc">HDFC Bank</option>
                        <option value="icici">ICICI Bank</option>
                    </select>
                </div>
                <label><input type="radio" name="paymentMethod" value="upi"> UPI</label><br/>
                <label><input type="radio" name="paymentMethod" value="cod"> Cash on Delivery</label><br/>
                <div id="cardDetails" style="display:none;">
                    <div class="inputBox"><label for="cardNumber">Card Number:</label><input type="text" id="cardNumber" name="cardNumber" placeholder="Enter card number"></div>
                    <div class="inputBox"><label for="expiryDate">Expiry Date:</label><input type="text" id="expiryDate" name="expiryDate" placeholder="MM/YY"></div>
                    <div class="inputBox"><label for="cvv">CVV:</label><input type="text" id="cvv" name="cvv" placeholder="Enter CVV"></div>
                </div>
            </form>
            <button type="button" class="button" onclick="showReviewSection()">Proceed to Review</button>
        </div>

        <div class="section" id="review-section">
            <h2>Review Order</h2>
            <div id="reviewItems"></div>
            <div class="total-amount">Total: ₹<span id="totalAmount"></span></div>
            <button type="button" class="button" onclick="submitOrder()">Place Order</button>
        </div>
    </div>

    <script>
        document.querySelectorAll('input[name="address"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('newAddressFields').style.display = this.value === 'new' ? 'block' : 'none';
            });
        });

        document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('cardDetails').style.display = this.value === 'creditcard' ? 'block' : 'none';
                document.getElementById('bankSelection').style.display = this.value === 'netbanking' ? 'block' : 'none';
            });
        });

        function showPaymentSection() {
            document.getElementById('address-section').classList.remove('active');
            document.getElementById('payment-section').classList.add('active');
        }

        function showReviewSection() {
            const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
            const reviewItemsContainer = document.getElementById('reviewItems');

            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            reviewItemsContainer.innerHTML = '';

            if (cart.length > 0) {
                cart.forEach(product => {
                    const item = document.createElement('div');
                    item.classList.add('review-item');
                    item.innerHTML = `<img src="${product.image}" alt="${product.name}"><br>
                                      ${product.name} - ₹${product.price} x ${product.quantity}<br>
                                      Total: ₹${product.price * product.quantity}`;
                    reviewItemsContainer.appendChild(item);
                });
            }

            document.getElementById('totalAmount').innerText = calculateTotalAmount();
            document.getElementById('payment-section').classList.remove('active');
            document.getElementById('review-section').classList.add('active');
        }

        function calculateTotalAmount() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            let totalAmount = 0;
            if (cart.length > 0) {
                cart.forEach(product => {
                    totalAmount += product.price * product.quantity;
                });
            }
            return totalAmount;
        }

        function submitOrder() {
            const addressForm = document.getElementById('addressForm');
            const paymentForm = document.getElementById('paymentForm');

            const formData = new FormData(addressForm);
            formData.append('paymentMethod', paymentForm.paymentMethod.value);

            // Collect additional payment details if available
            if (paymentForm.paymentMethod.value === 'creditcard') {
                formData.append('cardNumber', paymentForm.cardNumber.value);
                formData.append('expiryDate', paymentForm.expiryDate.value);
                formData.append('cvv', paymentForm.cvv.value);
            } else if (paymentForm.paymentMethod.value === 'netbanking') {
                formData.append('bank', paymentForm.bank.value);
            }

            // Assuming you have product ID and quantity in localStorage cart
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            if (cart.length > 0) {
                const product = cart[0];  // Example: get the first product from the cart
                formData.append('product_id', product.id);
                formData.append('quantity', product.quantity);
            }

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    document.getElementById('payment-section').classList.remove('active');
                    document.getElementById('review-section').classList.add('active');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while placing the order.');
            });
        }
    </script>
</body>
</html>
