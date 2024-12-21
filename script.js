
function openNav() {
  document.getElementById("sideMenu").style.width = "250px";
}

function closeNav() {
  document.getElementById("sideMenu").style.width = "0";
}

function toggleMenu() {
  var menu = document.getElementById("sidebar-menu");
  if (menu.style.width === "250px") {
    menu.style.width = "0";
  } else {
    menu.style.width = "250px";
  }
}

function toggleSubMenu(subMenuId) {
  var subMenu = document.getElementById(subMenuId);
  if (subMenu.style.display === "block") {
    subMenu.style.display = "none";
  } else {
    subMenu.style.display = "block";
  }
}

let slideIndex = 1;

// Initial display of slides
showSlides(slideIndex);

// Function to go to the next or previous slide
function plusSlides(n) {
  showSlides(slideIndex += n);
}

// Function to set the current slide
function currentSlide(n) {
  showSlides(slideIndex = n);
}

// Function to show the slides
function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  
  // Hide all slides
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  
  // Show the current slide
  slides[slideIndex-1].style.display = "block";
}

// Function to go to the next image (for auto-change)
function nextImage() {
  showSlides(slideIndex += 1);
}

// Set auto-change interval for images
const autoSlideInterval = setInterval(nextImage, 2000);

// Optional: If you want to stop auto-slide on manual navigation, you can do this:
document.querySelector('.nav-button.next').addEventListener('click', function() {
  nextImage();
  clearInterval(autoSlideInterval);
});


document.getElementById('reviewForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const reviewText = document.getElementById('review').value;
    const reviewContainer = document.getElementById('reviewsContainer');

    const reviewDiv = document.createElement('div');
    reviewDiv.textContent = reviewText;
    reviewContainer.appendChild(reviewDiv);

    document.getElementById('review').value = '';
});

document.getElementById('registrationForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    alert(`User registered with Username: ${username}, Email: ${email}`);
    
    document.getElementById('username').value = '';
    document.getElementById('email').value = '';
    document.getElementById('password').value = '';
});

    let selectedPrice = 0;

    function openPaymentModal(paymentMethod) {
        selectedPrice = parseFloat(document.querySelector('.product.active').dataset.price);
        const modalContent = document.getElementById('modalContent');

        // Clear previous content
        modalContent.innerHTML = '<button onclick="closeModal()">Close</button>';

        if (paymentMethod === 'paypal') {
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: selectedPrice.toFixed(2),
                                currency_code: 'INR'
                            }
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        alert(`Transaction completed by ${details.payer.name.given_name}`);
                    });
                },
                onError: function(err) {
                    console.error(err);
                    alert('Payment failed. Please try again.');
                }
            }).render('#modalContent');
        } else if (paymentMethod === 'razorpay') {
            var options = {
                "key": "YOUR_RAZORPAY_KEY",
                "amount": (selectedPrice * 100).toFixed(0),
                "currency": "INR",
                "name": "Product Purchase",
                "description": "Purchase of product",
                "image": "https://example.com/your_logo.png",
                "handler": function (response) {
                    alert(`Payment successful! Payment ID: ${response.razorpay_payment_id}`);
                },
                "prefill": {
                    "name": "Your Name",
                    "email": "email@example.com",
                    "contact": "9999999999"
                },
                "notes": {
                    "address": "Your address"
                },
                "theme": {
                    "color": "#F37254"
                }
            };
            var rzp1 = new Razorpay(options);
            rzp1.on('payment.failed', function (response) {
                alert(`Payment failed. Reason: ${response.error.reason}`);
            });
            rzp1.open();
        } else if (paymentMethod === 'paytm') {
            fetch('/paytm/transaction', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ amount: selectedPrice, currency: 'INR' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.paymentUrl; // Redirect to PayTM payment URL
                } else {
                    alert('Payment failed. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Payment failed. Please try again.');
            });
        } else if (paymentMethod === 'phonepe') {
            fetch('/phonepe/transaction', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ amount: selectedPrice, currency: 'INR' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.paymentUrl; // Redirect to PhonePe payment URL
                } else {
                    alert('Payment failed. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Payment failed. Please try again.');
            });
        }
        
        document.getElementById('paymentModal').style.display = 'flex';
    }

 function performSearch() {
            const categorySelect = document.getElementById('categorySelect');
            const searchInput = document.getElementById('searchInput').value.trim().toLowerCase();
            const category = categorySelect.value.toLowerCase();

            let url = 'products/';

            const validUrls = [
                'products/fashion.html',
                'products/electronics.html',
                'products/kids.html',
                'products/gaming.html',
                'products/sports.html',
                'products/kitchen.html',
                'products/books.html',
                'products/office.html',
            ];

       const fallbackUrls = {
                'mobiles': 'products/electronics.html',
                'computers': 'products/electronics.html',
                'fans': 'products/electronics.html',
                'laptops': 'products/electronics.html',
                'camera': 'products/electronics.html',
                'fans': 'products/electronics.html',
                'watches': 'products/electronics.html',
                'earphones': 'products/electronics.html',
                'television': 'products/electronics.html',
                'washingmaching': 'products/electronics.html',
                'goggles': 'products/fashion.html',
                'clothing': 'products/electronics.html',
                'earrings': 'products/electronics.html',
                'necklaces': 'products/electronics.html',
                'shoes': 'products/electronics.html',
                'caps': 'products/electronics.html',
                'bangles': 'products/electronics.html',
                'cosmetics': 'products/electronics.html',
               };

            if (category === 'all') {
                if (searchInput) {
                    if (fallbackUrls[searchInput]) {
                        url = `${fallbackUrls[searchInput]}#${searchInput}`;
                    } else {
                        url += `${searchInput}.html`;
                    }
                } else {
                    url += 'all.html';
                }
            } else {
                if (searchInput) {
                    url += `${category}/${searchInput}.html`;
                } else {
                    url += `${category}.html`;
                }
            }

   // Handle fallback for specific searches within selected category
    if (category !== 'all' && searchInput && !validUrls.includes(url)) {
        if (fallbackUrls[searchInput]) {
            url = `${fallbackUrls[searchInput]}#${searchInput}`;
        } else {
            url = `${category}.html`;
        }
    }

            if (validUrls.includes(url.split('#')[0])) {
                window.location.href = url;
            } else if (category === 'all' && searchInput && fallbackUrls[searchInput]) {
                window.location.href = `${fallbackUrls[searchInput]}#${searchInput}`;
            } else {
                alert('Not Found');
            }

            return false;
        }

document.addEventListener('DOMContentLoaded', function() {
    // Mapping of captcha images to their corresponding text
    const captchaMapping = {
        'images/captcha1.JPG': '24048B',
        'images/captcha2.JPG': 'ic8',
        'images/captcha3.JPG': 'smwm'
    };

    // List of captcha image URLs
    const captchaImages = Object.keys(captchaMapping);

    // Function to generate a random integer
    function getRandomInt(max) {
        return Math.floor(Math.random() * max);
    }

    // Function to set a random captcha image
    function setRandomCaptcha() {
        const randomIndex = getRandomInt(captchaImages.length);
        const selectedCaptcha = captchaImages[randomIndex];
        document.getElementById('captchaImage').src = selectedCaptcha;
        // Store the captcha text in a hidden field for validation
        document.getElementById('captchaImage').dataset.captchaText = captchaMapping[selectedCaptcha];
    }

    // Call setRandomCaptcha when the page loads
    setRandomCaptcha();

    // Function to validate the captcha input
    document.getElementById('registrationForm').addEventListener('submit', function(event) {
        const userCaptcha = document.getElementById('captcha').value.trim();
        const captchaText = document.getElementById('captchaImage').dataset.captchaText;

        if (userCaptcha !== captchaText) {
            event.preventDefault(); // Prevent form submission
            alert('Captcha does not match. Please try again.');
            setRandomCaptcha(); // Reload captcha image
        }
    });
});

const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#id_password');

togglePassword.addEventListener('click', function () {
    // Toggle the type attribute
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);

    // Toggle the eye icon
    if (password.getAttribute('type') === 'password') {
        togglePassword.src = 'images/eye.png'; // Eye icon for hidden password
    } else {
        togglePassword.src = 'images/eyeslash.png'; // Eye slash icon for visible password
    }
});