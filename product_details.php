<?php
 session_start();
 require_once "db_conn.php";

// Initialize cart count
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
// Handle clear cart action
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
    $cart_count = 0;
    header("Location: " . $_SERVER['PHP_SELF'] . (isset($_GET['id']) ? "?id=" . $_GET['id'] : ""));
    exit;
}


// Handle add to cart AJAX request
if (isset($_POST['add_to_cart'])) {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    // Get product details from database
    $product_query = "SELECT * FROM products WHERE id = $product_id";
    $product_result = mysqli_query($conn, $product_query);
    
    if (mysqli_num_rows($product_result) > 0) {
        $product = mysqli_fetch_assoc($product_result);
        
        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Check if product is already in cart
        if (isset($_SESSION['cart'][$product_id])) {
            // Update quantity
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            // Add new product to cart with all details
            $_SESSION['cart'][$product_id] = [
                'id' => $product_id,
                'name' => $product['name'],
                'price' => $product['price'],
                'image_url' => $product['image_url'],
                'category' => $product['category'],
                'quantity' => $quantity
            ];
        }
        
        // Calculate new cart count (sum of all quantities)
        $cart_count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $cart_count += $item['quantity'];
        }
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'cart_count' => $cart_count]);
        exit;
    } else {
        // Product not found
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }
}
// Get all categories for the filter
$categories_query = "SELECT DISTINCT category FROM products ORDER BY category";
$categories = mysqli_query($conn, $categories_query);

// Determine page mode and fetch appropriate data
if (isset($_GET['id'])) {
    $page_mode = 'product';
    $product_id = intval($_GET['id']);
    
    // Get single product details
    $product_query = "SELECT * FROM products WHERE id = $product_id";
    $product_result = mysqli_query($conn, $product_query);
    
    if (mysqli_num_rows($product_result) > 0) {
        $product = mysqli_fetch_assoc($product_result);
        
        // Get related products (same category, excluding current product)
        $category = mysqli_real_escape_string($conn, $product['category']);
        $related_query = "SELECT * FROM products WHERE category = '$category' AND id != $product_id LIMIT 4";
        $related_result = mysqli_query($conn, $related_query);
    } else {
        // Product not found, redirect to all products
        header("Location: product_details.php");
        exit;
    }
} else {
    $page_mode = 'category';
    
    // Set page title based on category filter
    if (isset($_GET['category'])) {
        $category = mysqli_real_escape_string($conn, $_GET['category']);
        $page_title = htmlspecialchars($category) . " Bikes";
        
        // Get products filtered by category
        $products_query = "SELECT * FROM products WHERE category = '$category' ORDER BY name";
    } else {
        $page_title = "All Bikes";
        
        // Get all products
        $products_query = "SELECT * FROM products ORDER BY category, name";
    }
    
    $products = mysqli_query($conn, $products_query);
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
    <title><?php echo $page_mode == 'product' ? htmlspecialchars($product['name']) : $page_title; ?> - CycleHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">Cycle<span>Hub</span></div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="product_details.php" class="active">Bikes</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if(isset($_SESSION["id"])): ?>
                        <li><a href="logout.php">Logout</a></li>
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

    <div id="notification"></div>
    
    <?php if ($page_mode == 'product'): ?>
    <!-- Single Product View -->
    <section class="product-details-section">
        <div class="container">
            <div class="product-details-container">
                <div class="product-image-large">
                    <img src="<?php echo !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'images/default-bike.jpg'; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div class="product-info">
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="product-category"><?php echo htmlspecialchars($product['category']); ?></p>
                    <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                    
                    <div class="product-description">
                        <h3>Description</h3>
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>
                    
                    <p class="product-stock">
                        <?php echo $product['stock'] > 0 ? '<span class="in-stock">In Stock</span>' : '<span class="out-of-stock">Out of Stock</span>'; ?>
                    </p>
                    
                    <div class="add-to-cart-section">
                        <div class="quantity-wrapper">
                            <label for="quantity-detail">Quantity:</label>
                            <input type="number" id="quantity-detail" class="quantity-input" value="1" min="1" max="<?php echo $product['stock']; ?>" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                        </div>
                        <button class="add-to-cart-btn"
                                data-product-id="<?php echo $product['id']; ?>"
                                data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                                <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                    
                    <div class="back-to-shop">
                        <a href="product_details.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Shop</a>
                    </div>
                </div>
            </div>
            
            <?php if (mysqli_num_rows($related_result) > 0): ?>
            <div class="related-products">
                <h2>You May Also Like</h2>
                <div class="related-products-grid">
                    <?php while($related = mysqli_fetch_assoc($related_result)): ?>
                    <div class="product-card" data-category="<?php echo htmlspecialchars($related['category']); ?>">
                        <div class="product-image">
                            <img src="<?php echo !empty($related['image_url']) ? htmlspecialchars($related['image_url']) : 'images/default-bike.jpg'; ?>" alt="<?php echo htmlspecialchars($related['name']); ?>">
                        </div>
                        <div class="product-details">
                            <h3><?php echo htmlspecialchars($related['name']); ?></h3>
                            <p class="price">$<?php echo number_format($related['price'], 2); ?></p>
                            <a href="product_details.php?id=<?php echo $related['id']; ?>" class="view-details-btn">View Details</a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <?php else: ?>
    <!-- Category/All Products View -->
    <section class="products-section">
        <div class="container">
            <h1 class="page-title"><?php echo $page_title; ?></h1>
            
            <div class="filter-categories">
                <a href="product_details.php" class="category-btn <?php echo !isset($_GET['category']) ? 'active' : ''; ?>">All Bikes</a>
                <?php 
                // Reset the categories result pointer
                mysqli_data_seek($categories, 0);
                while($category = mysqli_fetch_assoc($categories)): 
                ?>
                    <a href="product_details.php?category=<?php echo urlencode($category['category']); ?>" class="category-btn <?php echo (isset($_GET['category']) && $_GET['category'] == $category['category']) ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($category['category']); ?>
                    </a>
                <?php endwhile; ?>
            </div>
            
            <div class="products-grid">
                <?php if (mysqli_num_rows($products) > 0): ?>
                    <?php while($product = mysqli_fetch_assoc($products)): ?>
                        <div class="product-card" data-category="<?php echo htmlspecialchars($related['category']); ?>">
                            <div class="product-image">
                                <img src="<?php echo !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'images/default-bike.jpg'; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </div>
                            <div class="product-details">
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="category"><?php echo htmlspecialchars($product['category']); ?></p>
                                <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                                <div class="product-actions">
                                    <div class="quantity-wrapper">
                                        <label for="quantity-list-<?php echo $product['id']; ?>">Qty:</label>
                                        <input type="number" id="quantity-list-<?php echo $product['id']; ?>" class="quantity-input" value="1" min="1" max="<?php echo $product['stock']; ?>">
                                    </div>
                                    <button class="add-to-cart-btn" 
                                            data-product-id="<?php echo $product['id']; ?>" 
                                            data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                                            <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                    <a href="product_details.php?id=<?php echo $product['id']; ?>" class="view-details-btn">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-products">
                        <p>No products found in this category.</p>
                        <a href="product_details.php" class="btn">View All Bikes</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

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
            // Notification function
            window.showNotification = function(message, type) {
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
                
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    closeNotification();
                }, 5000);
            }
            
            window.closeNotification = function() {
                const notification = document.getElementById('notification');
                notification.style.display = 'none';
            }
            
            // Add to cart functionality for ALL buttons
            const addToCartBtns = document.querySelectorAll('.add-to-cart-btn');
            
            addToCartBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    const productName = this.getAttribute('data-product-name');
                    
                    // Define the quantity input - check if we're in detail view or list view
                    let quantityInput;
                    if (this.closest('.add-to-cart-section')) {
                        // Detail view
                        quantityInput = document.getElementById('quantity-detail');
                    } else {
                        // List view - find the quantity input for this specific product
                        quantityInput = document.getElementById('quantity-list-' + productId);
                    }
                    
                    const quantity = parseInt(quantityInput.value);
                    
                    if (isNaN(quantity) || quantity < 1) {
                        showNotification('Please enter a valid quantity', 'error');
                        return;
                    }
                    
                    // Add to cart via fetch API
                    fetch('product_details.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `add_to_cart=1&product_id=${productId}&quantity=${quantity}`
                    })
                    .then(response => {
                        // Check if response is ok before trying to parse JSON
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Error parsing JSON:', text);
                                throw new Error('Invalid JSON response');
                            }
                        });
                    })
                    .then(data => {
                        if (data.success) {
                            showNotification(`${productName} added to cart`, 'success');
                            document.getElementById('cart-count').textContent = data.cart_count;
                            
                            // Reset quantity to 1
                            quantityInput.value = 1;
                        } else {
                            showNotification(data.message || 'Error adding to cart', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred while adding to cart', 'error');
                    });
                });
            });
        });
    </script></body>
</html>