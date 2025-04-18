<?php
session_start();
require_once "db_conn.php";
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
    <title>Contact Us - CycleHub</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Enhanced styling for contact page */
        .contact-header {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('images/contact-header.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 80px 20px;
            margin-bottom: 40px;
        }
        
        .contact-header h1 {
            font-size: 3rem;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .contact-header p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .contact-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 60px;
        }
        
        .contact-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
        }
        
        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .info-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        
        .info-card h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.4rem;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        
        .info-item {
            display: flex;
            margin-bottom: 15px;
            align-items: flex-start;
        }
        
        .info-item i {
            color: #3498db;
            font-size: 1.2rem;
            margin-right: 15px;
            margin-top: 3px;
        }
        
        .hours-list {
            list-style: none;
            padding: 0;
        }
        
        .hours-list li {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #e0e0e0;
        }
        
        .hours-list li:last-child {
            border-bottom: none;
        }
        
        @media (max-width: 768px) {
            .contact-content {
                grid-template-columns: 1fr;
            }
            
            .contact-info {
                grid-template-columns: 1fr;
            }
            
            .contact-header h1 {
                font-size: 2.2rem;
            }
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
                    <li><a href="product_details.php">Bikes</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php" class="active">Contact</a></li>
                    <?php if(isset($_SESSION["id"])): ?>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                    <li class="cart-actions">
                        <a href="cart.php" class="cart-icon"><i class="fas fa-shopping-cart"></i> 
                            <span id="cart-count">
                                <?php 
                                $cart_count = 0;
                                if (!empty($_SESSION['cart'])) {
                                    foreach ($_SESSION['cart'] as $item) {
                                        $cart_count += $item['quantity'];
                                    }
                                }
                                echo $cart_count;
                                ?>
                            </span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="contact-header">
        <h1>Contact Us</h1>
        <p>We're here to help with all your cycling needs</p>
    </section>

    <main class="contact-section">
        <div class="contact-content">
            <div class="contact-info">
                <div class="info-card">
                    <h3><i class="fas fa-map-marker-alt"></i> Store Location</h3>
                    <div class="info-item">
                        <i class="fas fa-building"></i>
                        <div>
                            <p>123 Bike Street<br>
                            Cycling City, CC 12345</p>
                        </div>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3><i class="fas fa-address-book"></i> Contact Information</h3>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <p>(123) 456-7890</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <p>info@cyclehub.com</p>
                        </div>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3><i class="fas fa-clock"></i> Store Hours</h3>
                    <ul class="hours-list">
                        <li><span>Monday</span> <span>9:00 AM - 6:00 PM</span></li>
                        <li><span>Tuesday</span> <span>9:00 AM - 6:00 PM</span></li>
                        <li><span>Wednesday</span> <span>9:00 AM - 6:00 PM</span></li>
                        <li><span>Thursday</span> <span>9:00 AM - 7:00 PM</span></li>
                        <li><span>Friday</span> <span>9:00 AM - 7:00 PM</span></li>
                        <li><span>Saturday</span> <span>8:00 AM - 5:00 PM</span></li>
                        <li><span>Sunday</span> <span>10:00 AM - 4:00 PM</span></li>
                    </ul>
                </div>
                
                <div class="info-card">
                    <h3><i class="fas fa-users"></i> Our Team</h3>
                    <div class="info-item">
                        <i class="fas fa-user-tie"></i>
                        <div>
                            <p><strong>Carson Yam</strong><br>Owner & Founder</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-user-friends"></i>
                        <div>
                            <p><strong>Nathan</strong><br>Customer Service Leader</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-tools"></i>
                        <div>
                            <p><strong>Vincent</strong><br>Head Mechanic</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>CycleHub</h3>
                    <p>Your one-stop shop for all things cycling.</p>
                </div>
                <div class="footer-column">
                    <h3>Shop</h3>
                    <ul>
                        <li><a href="index.php?category=Mountain%20Bike">Mountain Bikes</a></li>
                        <li><a href="index.php?category=Road%20Bike">Road Bikes</a></li>
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
