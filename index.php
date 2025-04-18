<?php 
session_start();
require_once "db_conn.php";

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = array();
    header("Location: index.php");
    exit();
}

// Calculate cart count
$cart_count = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}

// Get categories
$sql = "SELECT DISTINCT category FROM products";
$categories = mysqli_query($conn, $sql);

// Get category names for display
$category_names = array();
while($cat = mysqli_fetch_assoc($categories)) {
    $category_names[] = $cat['category'];
}
mysqli_data_seek($categories, 0); // Reset the result pointer

// Check for order success message
$order_success = false;
if(isset($_SESSION['order_success']) && $_SESSION['order_success'] === true) {
    $order_success = true;
    // Clear the session variable after using it
    $_SESSION['order_success'] = false;
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
    <title>CycleHub - Premium Bikes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Additional custom styles for enhanced look */
        .section-container {
            border: 2px solid var(--primary-light);
            border-radius: var(--border-radius-lg);
            padding: var(--spacing-xl);
            margin-bottom: var(--spacing-xxl);
            box-shadow: var(--shadow-md);
            background-color: white;
        }
        
        .category-card {
            border: 2px solid var(--gray-300);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            text-align: center;
            transition: all var(--transition-normal);
            background-color: white;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 180px;
        }
        
        .category-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }
        
        .category-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: var(--spacing-md);
        }
        
        .category-card h3 {
            margin-bottom: var(--spacing-sm);
            color: var(--secondary-color);
        }
        
        .view-more {
            color: var(--primary-color);
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: var(--spacing-lg);
        }
        
        .feature {
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            text-align: center;
            transition: all var(--transition-normal);
            background-color: white;
        }
        
        .feature:hover {
            border-color: var(--primary-color);
            transform: translateY(-5px);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: var(--spacing-md);
        }
        
        .cta {
            background-color: var(--primary-light);
            padding: var(--spacing-xxl) 0;
        }
        
        .cta-content {
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .cta-content h2 {
            color: var(--secondary-color);
            font-size: 2rem;
            margin-bottom: var(--spacing-md);
        }
        
        .cta-content p {
            margin-bottom: var(--spacing-lg);
            font-size: 1.1rem;
        }
        
        .hero {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: var(--spacing-xxl) 0;
            margin-bottom: var(--spacing-xxl);
        }
        
        .hero-content {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero-content h1 {
            color: white;
            font-size: 2.5rem;
            margin-bottom: var(--spacing-md);
        }
        
        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: var(--spacing-lg);
            opacity: 0.9;
        }
        
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: var(--spacing-lg);
        }
        
        section h2 {
            text-align: center;
            margin-bottom: var(--spacing-xl);
            position: relative;
            padding-bottom: var(--spacing-sm);
        }
        
        section h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">Cycle<span>Hub</span></div>
            <nav>
                <ul>
                    <li><a href="index.php" class="active">Home</a></li>
                    <li><a href="product_details.php">Bikes</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if(isset($_SESSION["id"])): ?>
                        <li>
                            <a href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                    <li class="cart-actions">
                        <a href="cart.php" class="cart-icon"><i class="fas fa-shopping-cart"></i> <span id="cart-count"><?php echo $cart_count; ?></span></a>
                        <form method="post" style="display: inline;">
                            <button type="submit" name="clear_cart" class="clear-cart-btn" onclick="return confirm('Are you sure you want to clear your cart?')">
                                <i class="fas fa-trash"></i> Clear
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <div id="notification">
    <?php if($order_success): ?>
    <div class="notification-popup success">
        <div class="notification-content">
            <i class="fas fa-check-circle"></i>
            <p>Your order has been placed successfully! Thank you for shopping with CycleHub.</p>
            <button onclick="closeNotification()"><i class="fas fa-times"></i></button>
        </div>
    </div>
    <?php endif; ?>
    </div>

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Find Your Perfect Ride</h1>
                <p>Explore our collection of high-quality bikes for every terrain and style</p>
                <a href="product_details.php" class="btn">Shop Now <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>

    <section class="categories">
        <div class="container section-container">
            <h2>Browse By Category</h2>
            <div class="category-grid">
                <?php foreach($category_names as $category): ?>
                    <a href="product_details.php?category=<?php echo urlencode($category); ?>" class="category-card">
                        <div class="category-icon">
                            <?php
                            // Choose appropriate icon based on category
                            $icon = 'fa-bicycle';
                            if (stripos($category, 'mountain') !== false) {
                                $icon = 'fa-mountain';
                            } elseif (stripos($category, 'road') !== false) {
                                $icon = 'fa-road';
                            } elseif (stripos($category, 'electric') !== false) {
                                $icon = 'fa-bolt';
                            } elseif (stripos($category, 'city') !== false) {
                                $icon = 'fa-city';
                            }
                            ?>
                            <i class="fas <?php echo $icon; ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($category); ?></h3>
                        <span class="view-more">View Collection <i class="fas fa-arrow-right"></i></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="why-us">
        <div class="container section-container">
            <h2>Why Choose CycleHub?</h2>
            <div class="features">
                <div class="feature">
                    <div class="feature-icon"><i class="fas fa-truck"></i></div>
                    <h3>Free Shipping</h3>
                    <p>On all orders over $500</p>
                </div>
                <div class="feature">
                    <div class="feature-icon"><i class="fas fa-tools"></i></div>
                    <h3>Expert Assembly</h3>
                    <p>Professional bike assembly included</p>
                </div>
                <div class="feature">
                    <div class="feature-icon"><i class="fas fa-undo"></i></div>
                    <h3>Easy Returns</h3>
                    <p>30-day hassle-free returns</p>
                </div>
                <div class="feature">
                    <div class="feature-icon"><i class="fas fa-headset"></i></div>
                    <h3>Support</h3>
                    <p>Dedicated customer service</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Find Your New Bike?</h2>
                <p>Browse our complete collection of premium bikes</p>
                <a href="product_details.php" class="btn">Shop All Bikes</a>
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
                        <li><a href="product_details.php?category=Mountain%20Bike">Mountain Bikes</a></li>
                        <li><a href="product_details.php?category=Road%20Bike">Road Bikes</a></li>
                        <li><a href="product_details.php?category=City%20Bike">City Bikes</a></li>
                        <li><a href="product_details.php?category=Electric%20Bike">Electric Bikes</a></li>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if notification has content and show it
            const notification = document.getElementById('notification');
            if (notification.innerHTML.trim() !== '') {
                notification.style.display = 'block';
                
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    closeNotification();
                }, 5000);
            }
            
            // Notification function for dynamic notifications
            function showNotification(message, type) {
                const notification = document.getElementById('notification');
                
                notification.innerHTML = `
                    <div class="notification-popup ${type}">
                        <div class="notification-content">
                            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                            <p>${message}</p>
                            <button onclick="closeNotification()"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                `;
                
                notification.style.display = 'block';
                
                setTimeout(() => {
                    closeNotification();
                }, 5000);
            }
        });

        // Make closeNotification globally available
        function closeNotification() {
            const notification = document.getElementById('notification');
            notification.style.display = 'none';
        }    </script>
</body>
</html>
