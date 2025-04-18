<?php
// Start the session at the very beginning
session_start();
require_once "db_conn.php";

// Debug: Log the session ID and cart contents
error_log("Session ID in cart.php: " . session_id());
error_log("Cart contents: " . json_encode(isset($_SESSION['cart']) ? $_SESSION['cart'] : 'No cart'));

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Calculate cart count and total
$cart_count = 0;
$cart_total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
        $cart_total += $item['price'] * $item['quantity'];
    }
}

// Check login status
$logged_in = isset($_SESSION["id"]) && !empty($_SESSION["id"]);

// Handle remove item from cart
if (isset($_POST['remove_item']) && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        // Redirect to avoid form resubmission
        header("Location: cart.php");
        exit();
    }
}

// Handle update quantity
if (isset($_POST['update_quantity']) && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    
    if ($quantity > 0 && isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        // Redirect to avoid form resubmission
        header("Location: cart.php");
        exit();
    }
}

// Handle clear cart
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = array();
    header("Location: cart.php");
    exit();
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
    <title>Your Cart - CycleHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Cart specific styles */
        .cart-section {
            padding: 60px 0;
        }
        
        .cart-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .cart-header {
            padding: 20px 30px;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .cart-header h2 {
            margin: 0;
            font-size: 1.8rem;
            color: var(--secondary-color);
        }
        
        .cart-items {
            padding: 0 30px;
        }
        
        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .cart-item-details {
            flex: 1;
        }
        
        .cart-item-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .cart-item-price {
            color: var(--accent-color);
            font-weight: 600;
        }
        
        .cart-item-quantity {
            display: flex;
            align-items: center;
            margin: 0 30px;
        }
        
        .cart-item-quantity input {
            width: 60px;
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 0 10px;
        }
        
        .update-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .update-btn:hover {
            background-color: #2980b9;
        }
        
        .remove-btn {
            background-color: var(--danger-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .remove-btn:hover {
            background-color: #c0392b;
        }
        
        .cart-total {
            padding: 20px 30px;
            border-top: 1px solid #f1f1f1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .total-amount {
            font-size: 1.3rem;
            font-weight: 600;
        }
        
        .cart-actions {
            display: flex;
            gap: 15px;
        }
        
        .continue-shopping {
            background-color: #f1f1f1;
            color: var(--dark-color);
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .continue-shopping:hover {
            background-color: #e0e0e0;
        }
        
        .checkout-btn {
            background-color: var(--success-color);
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkout-btn:hover {
            background-color: #27ae60;
        }
        
        .empty-cart {
            padding: 50px 30px;
            text-align: center;
        }
        
        .empty-cart i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-cart p {
            font-size: 1.2rem;
            margin-bottom: 20px;
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

    <section class="cart-section">
        <div class="container">
            <div class="cart-container">
                <div class="cart-header">
                    <h2>Your Shopping Cart</h2>
                </div>
                
                <?php if(empty($_SESSION['cart'])): ?>
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Your cart is empty</p>
                        <a href="index.php" class="btn">Continue Shopping</a>
                    </div>
                <?php else: ?>
                    <div class="cart-items">
                        <?php foreach($_SESSION['cart'] as $product_id => $item): ?>
                            <div class="cart-item">
                                <div class="cart-item-details">
                                    <div class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <div class="cart-item-price">$<?php echo number_format($item['price'], 2); ?></div>
                                </div>
                                <form method="post" class="cart-item-quantity">
                                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1">
                                    <button type="submit" name="update_quantity" class="update-btn">Update</button>
                                </form>
                                <form method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                    <button type="submit" name="remove_item" class="remove-btn">Remove</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="cart-total">
                        <div class="total-amount">Total: $<?php echo number_format($cart_total, 2); ?></div>
                        <div class="cart-actions">
                            <form method="post">
                                <button type="submit" name="clear_cart" class="continue-shopping" onclick="return confirm('Are you sure you want to clear your cart?')">
                                    <i class="fas fa-trash"></i> Clear Cart
                                </button>
                            </form>
                            <a href="index.php" class="continue-shopping">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </a>
                            <a href="checkout.php" class="checkout-btn">
                                Proceed to Checkout <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>CycleHub</h3>
                    <p>Your one-stop shop for all things cycling. Quality bikes for every rider, from beginners to professionals.</p>
                </div>
                <div class="footer-column">
                    <h3>Shop</h3>
                    <ul>
                        <li><a href="index.php?category=Mountain%20Bike">Mountain Bikes</a></li>
                        <li><a href="index.php?category=Road%20Bike">Road Bikes</a></li>
                        <li><a href="index.php?category=City%20Bike">City Bikes</a></li>
                        <li><a href="index.php?category=Electric%20Bike">Electric Bikes</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact</h3>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> 123 Bike Street</li>
                        <li><i class="fas fa-phone"></i> (123) 456-7890</li>
                        <li><i class="fas fa-envelope"></i> info@cyclehub.com</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                © 2023 CycleHub Bike Shop. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>