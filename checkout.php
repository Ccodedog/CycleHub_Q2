<?php 
session_start();
// Check if user is logged in
$logged_in = isset($_SESSION['id']);

// Initialize variables with default values
$name = $email = $address = $city = $country = '';
$error = '';
$shipping = 10.00; // Default shipping cost

// Retrieve cart items from session
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Calculate subtotal
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

// Calculate total
$total = $subtotal + $shipping;

// If user is logged in, pre-fill form with user data
if ($logged_in) {
    // Connect to database
    $conn = new mysqli("sql309.infinityfree.com", "if0_38757269", "C20040326cc", "if0_38757269_cyclehub_db");
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Get user data
    $user_id = $_SESSION['id'];
    $stmt = $conn->prepare("SELECT name, email, address, city, country FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $name = $user['name'];
        $email = $user['email'];
        $address = $user['address'];
        $city = $user['city'];
        $country = $user['country'];
    }
    
    $stmt->close();
    $conn->close();
}

// Handle order placement
if (isset($_POST['place_order'])) {
    // Validate form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $country = trim($_POST['country']);
    $payment_method = $_POST['payment_method'];
    
    // Basic validation
    if (empty($name) || empty($email) || empty($address) || empty($city) || empty($country)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (empty($cart_items)) {
        $error = "Your cart is empty";
    } else {
        // Connect to database
        $conn = new mysqli("sql309.infinityfree.com", "if0_38757269", "C20040326cc", "if0_38757269_cyclehub_db");
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Create order
            $stmt = $conn->prepare("INSERT INTO orders (user_id, name, email, address, city, country, payment_method, subtotal, shipping, total, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $user_id = $logged_in ? $_SESSION['id'] : NULL;
            $stmt->bind_param("issssssddd", $user_id, $name, $email, $address, $city, $country, $payment_method, $subtotal, $shipping, $total);
            $stmt->execute();
            
            $order_id = $conn->insert_id;
            
            // Add order items
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($cart_items as $item) {
                $stmt->bind_param("iisdi", $order_id, $item['id'], $item['name'], $item['price'], $item['quantity']);
                $stmt->execute();
                
                // Update product inventory
                $update_stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $update_stmt->bind_param("ii", $item['quantity'], $item['id']);
                $update_stmt->execute();
                $update_stmt->close();
            }
            
            $stmt->close();
            
            // Commit transaction
            $conn->commit();
            
            // Store order ID in session for access on confirmation page
            $_SESSION['order_success'] = true;
            $_SESSION['last_order_id'] = $order_id;

            // Clear cart
            unset($_SESSION['cart']);

            header("Location: checkout.php?success=true");
            exit;        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $_SESSION['error_message'] = "An error occurred while processing your order. Please try again.";
            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-FEX1LN11BZ"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-FEX1LN11BZ');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - CycleHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 600px;
        border-radius: 5px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
    }

    .preview-items {
        margin: 15px 0;
    }

    .preview-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }

    .preview-totals {
        margin-top: 20px;
    }    
    /* Success Modal styles */
    .success-modal-container {
        z-index: 1001; /* Higher than the preview modal */
    }

    .success-modal {
        text-align: center;
        padding: 30px;
    }

    .success-icon {
        font-size: 60px;
        color: #4CAF50;
        margin-bottom: 20px;
    }

    .redirect-message {
        margin-top: 20px;
        font-style: italic;
        color: #666;
    }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">Cycle<span>Hub</span></div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="bikes.php">Bikes</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if($logged_in): ?>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <section class="checkout-section">
        <div class="container">
            <?php if(isset($error) && !empty($error)): ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="checkout-container">
                <div class="checkout-form">
                    <h2>Checkout</h2>
                    <form method="post" action="checkout.php">
                        <div class="form-section">
                            <h3>Customer Information</h3>
                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="name">Full Name</label>
                                    <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($name); ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h3>Delivery Address</h3>
                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="address">Street Address</label>
                                    <input type="text" id="address" name="address" required value="<?php echo htmlspecialchars($address); ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" id="city" name="city" required value="<?php echo htmlspecialchars($city); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="country">Country</label>
                                    <input type="text" id="country" name="country" required value="<?php echo htmlspecialchars($country); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h3>Payment Method</h3>
                            <div class="payment-methods">
                                <label class="payment-method">
                                    <input type="radio" name="payment_method" value="credit_card" checked>
                                    <div class="payment-method-info">
                                        <h4>Credit Card</h4>
                                        <p>Pay securely with your credit card</p>
                                    </div>
                                    <div class="payment-method-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                </label>
                                
                                <label class="payment-method">
                                    <input type="radio" name="payment_method" value="paypal">
                                    <div class="payment-method-info">
                                        <h4>PayPal</h4>
                                        <p>Pay via PayPal; you can pay with your credit card if you don't have a PayPal account</p>
                                    </div>
                                    <div class="payment-method-icon">
                                        <i class="fab fa-paypal"></i>
                                    </div>
                                </label>
                                
                                <label class="payment-method">
                                    <input type="radio" name="payment_method" value="cash_on_delivery">
                                    <div class="payment-method-info">
                                        <h4>Cash on Delivery</h4>
                                        <p>Pay with cash upon delivery</p>
                                    </div>
                                    <div class="payment-method-icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <?php if(!$logged_in): ?>
                        <div class="form-section">
                            <div class="form-group full-width">
                                <p class="login-suggestion">
                                    <i class="fas fa-info-circle"></i> 
                                    Already have an account? <a href="login.php">Login</a> to save your shipping information for future orders.
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="form-section">
                            <button type="submit" name="place_order" class="btn btn-primary">Place Order</button>
                            <button type="button" class="btn btn-secondary" id="preview-order">Preview Order</button>
                        </div>
                    </form>
                </div>
                
                <div class="checkout-summary">
                    <div class="order-summary">
                        <h3>Order Summary</h3>
                        <div class="order-items">
                            <?php foreach($cart_items as $item): ?>
                            <div class="order-item">
                                <div class="item-details">
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p class="item-price">$<?php echo number_format($item['price'], 2); ?></p>
                                    <p class="item-quantity">Quantity: <?php echo $item['quantity']; ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="order-totals">
                            <div class="total-row">
                                <span>Subtotal</span>
                                <span>$<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <div class="total-row">
                                <span>Shipping</span>
                                <span>$<?php echo number_format($shipping, 2); ?></span>
                            </div>
                            <div class="total-row grand-total">
                                <span>Total</span>
                                <span>$<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="secure-checkout">
                        <p><i class="fas fa-lock"></i> Secure Checkout</p>
                        <div class="payment-icons">
                            <i class="fab fa-cc-visa"></i>
                            <i class="fab fa-cc-mastercard"></i>
                            <i class="fab fa-cc-amex"></i>
                            <i class="fab fa-cc-paypal"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                                    <h3>About CycleHub</h3>
                                    <p>Your premier destination for high-quality bicycles and accessories.</p>
                                </div>
                                <div class="footer-section">
                                    <h3>Quick Links</h3>
                                    <ul>
                                        <li><a href="index.php">Home</a></li>
                                        <li><a href="bikes.php">Bikes</a></li>
                                        <li><a href="about.php">About</a></li>
                                        <li><a href="contact.php">Contact</a></li>
                                    </ul>
                                </div>
                                <div class="footer-section">
                                    <h3>Connect With Us</h3>
                                    <div class="social-icons">
                                        <a href="#"><i class="fab fa-facebook"></i></a>
                                        <a href="#"><i class="fab fa-twitter"></i></a>
                                        <a href="#"><i class="fab fa-instagram"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="footer-bottom">
                                <p>© 2025 CycleHub. All rights reserved.</p>
                            </div>
                        </div>
                    </footer>

                    <!-- Order Success Modal -->
                    <div id="order-success-modal" class="modal success-modal-container">
                        <div class="modal-content success-modal">
                            <div class="success-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h2>Order Successful!</h2>
                            <p>Thank you for your purchase. Your order has been placed successfully.</p>
                            <p>Order ID: <?php echo isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : ''; ?></p>
                            <p class="redirect-message">Redirecting to homepage in <span id="countdown">5</span> seconds...</p>
                        </div>
                    </div>

                    <!-- Order Preview Modal -->
                    <div id="order-preview-modal" class="modal">
                        <div class="modal-content">
                            <span class="close">×</span>
                            <h2>Order Preview</h2>
                            <div class="order-preview-details">
                                <h3>Order Items</h3>
                                <div class="preview-items">
                                    <?php foreach($cart_items as $item): ?>
                                    <div class="preview-item">
                                        <span><?php echo htmlspecialchars($item['name']); ?></span>
                                        <span>$<?php echo number_format($item['price'], 2); ?> × <?php echo $item['quantity']; ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="preview-totals">
                                    <div class="total-row">
                                        <span>Subtotal</span>
                                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                                    </div>
                                    <div class="total-row">
                                        <span>Shipping</span>
                                        <span>$<?php echo number_format($shipping, 2); ?></span>
                                    </div>
                                    <div class="total-row grand-total">
                                        <span>Total</span>
                                        <span>$<?php echo number_format($total, 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                    // Show success modal if order was just placed
                    document.addEventListener('DOMContentLoaded', function() {
                        <?php if(isset($_SESSION['order_success']) && $_SESSION['order_success']): ?>
                            // Show the success modal
                            document.getElementById("order-success-modal").style.display = "block";
            
                            // Set up countdown
                            let seconds = 5;
                            const countdownElement = document.getElementById("countdown");
            
                            const countdownInterval = setInterval(function() {
                                seconds--;
                                countdownElement.textContent = seconds;
                
                                if (seconds <= 0) {
                                    clearInterval(countdownInterval);
                                    // Redirect to homepage
                                    window.location.href = "index.php";
                                }
                            }, 1000);
            
                            <?php 
                            // Clear the success flag so it doesn't show again on refresh
                            unset($_SESSION['order_success']);
                            ?>
                        <?php endif; ?>
                    });
                    // Order preview modal functionality
                    document.addEventListener('DOMContentLoaded', function() {
                        const previewBtn = document.getElementById('preview-order');
                        const previewModal = document.getElementById('order-preview-modal');
                        const closeBtn = previewModal.querySelector('.close');
        
                        previewBtn.addEventListener('click', function() {
                            previewModal.style.display = 'block';
                        });
        
                        closeBtn.addEventListener('click', function() {
                            previewModal.style.display = 'none';
                        });
        
                        window.addEventListener('click', function(event) {
                            if (event.target == previewModal) {
                                previewModal.style.display = 'none';
                            }
                        });
                    });
                    </script>
</body>
</html>