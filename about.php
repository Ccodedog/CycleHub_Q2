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
    <title>About Us - CycleHub</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        .about-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .about-header {
            background-color: var(--secondary-color);
            color: white;
            padding: 3rem 1rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .about-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .about-content {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .about-text {
            flex: 2;
            min-width: 300px;
        }
        
        .about-sidebar {
            flex: 1;
            min-width: 250px;
            background-color: #f5f5f5;
            padding: 1.5rem;
            border-radius: 8px;
        }
        
        .hours-list {
            list-style: none;
            padding: 0;
        }
        
        .hours-list li {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px dashed #ddd;
        }
        
        .hours-list li:last-child {
            border-bottom: none;
        }
        
        .history-section {
            margin: 3rem 0;
        }
        
        .milestone {
            margin-bottom: 1.5rem;
            padding-left: 1.5rem;
            border-left: 3px solid var(--primary-color);
        }
        
        .milestone-year {
            font-weight: bold;
            color: var(--primary-color);
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
                    <li><a href="contact.php">Contact</a></li>
                    <?php if(isset($_SESSION["id"])): ?>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <section class="about-header">
        <h1>About CycleHub</h1>
        <p>Your trusted bike shop since 2015</p>
    </section>

    <main class="about-section">
        <div class="about-content">
            <div class="about-text">
                <h2>Our Story</h2>
                <p>CycleHub was founded in 2015 by Carson Yam, a passionate cyclist with a vision to create a bike shop that offers not just quality products, but also exceptional service and a sense of community.</p>
                
                <p>What started as a small repair shop has grown into a comprehensive cycling center, offering a wide range of bikes for all ages and skill levels, professional repair services, and a gathering place for local cycling enthusiasts.</p>
                
                <p>Our mission is to promote cycling as a sustainable, healthy, and enjoyable activity by providing expert advice, quality products, and building a community of riders who share our passion for two wheels.</p>
                
                <div class="history-section">
                    <h3>Our Journey</h3>
                    
                    <div class="milestone">
                        <p class="milestone-year">2015</p>
                        <p>CycleHub opens its doors as a small repair shop with a limited selection of bikes.</p>
                    </div>
                    
                    <div class="milestone">
                        <p class="milestone-year">2017</p>
                        <p>Expanded our store to include a wider range of bikes and accessories. Added professional fitting services.</p>
                    </div>
                    
                    <div class="milestone">
                        <p class="milestone-year">2019</p>
                        <p>Launched our community riding club and began organizing weekly group rides for cyclists of all levels.</p>
                    </div>
                    
                    <div class="milestone">
                        <p class="milestone-year">2021</p>
                        <p>Renovated our workshop and expanded our service team to provide faster, more comprehensive repair services.</p>
                    </div>
                    
                    <div class="milestone">
                        <p class="milestone-year">2023</p>
                        <p>Launched our online store to better serve customers beyond our local area.</p>
                    </div>
                </div>
            </div>
            
            <div class="about-sidebar">
                <h3>Store Hours</h3>
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
        </div>
    </main>

    <footer>
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
                    <li>123 Bike Street</li>
                    <li>Phone: (123) 456-7890</li>
                    <li>Email: info@cyclehub.com</li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            © 2023 CycleHub Bike Shop. All rights reserved.
        </div>
    </footer>
</body>
</html>
