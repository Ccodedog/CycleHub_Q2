<?php
session_start();
require_once "db_conn.php";

// Check if user is already logged in
if(isset($_SESSION["id"])) {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

// Process registration form submission
if(isset($_POST["register"])) {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $address = trim($_POST["address"]);
    $city = trim($_POST["city"]);
    $country = trim($_POST["country"]);
    
    // Validate inputs
    if(empty($name) || empty($email) || empty($password) || empty($confirm_password) ||
       empty($address) || empty($city) || empty($country)) {
        $error = "Please fill in all fields";
    } elseif($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        // Check if email already exists
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if(mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Email already exists";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $sql = "INSERT INTO users (name, email, password, address, city, country) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssssss", $name, $email, $hashed_password, $address, $city, $country);
            
            if(mysqli_stmt_execute($stmt)) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
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
    <title>Register - CycleHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Enhanced styling for registration page */
        .register-section {
            padding: 60px 0;
            background-color: #f9f9f9;
        }
        
        .register-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        .register-container h2 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            font-size: 32px;
        }
        
        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
        
        .form-section h3 {
            color: #3498db;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
            gap: 20px;
        }
        
        .form-group {
            flex: 1;
            min-width: 250px;
        }
        
        .form-group.full-width {
            flex-basis: 100%;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }
        
        .register-btn {
            width: 100%;
            padding: 14px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .register-btn:hover {
            background-color: #2980b9;
        }
        
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #c62828;
        }
        
        .success-message {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .success-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            max-width: 500px;
            width: 90%;
            position: relative;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        
        .success-content i {
            color: #4CAF50;
            font-size: 60px;
            margin-bottom: 20px;
        }
        
        .success-content h3 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .success-content p {
            margin-bottom: 20px;
            font-size: 18px;
            color: #555;
        }
        
        .redirect-message {
            font-size: 16px;
            color: #777;
        }
        
        .success-content button {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
        }
        
        .login-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .register-container {
                padding: 20px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 10px;
            }
            
            .form-group {
                min-width: 100%;
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
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="login.php">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="register-section">
        <div class="container">
            <div class="register-container">
                <h2>Create an Account</h2>
                
                <?php if(!empty($error)): ?>
                    <div class="error-message">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(!empty($success)): ?>
                    <div class="success-message" id="successMessage">
                        <div class="success-content">
                            <i class="fas fa-check-circle"></i>
                            <h3>Success!</h3>
                            <p><?php echo $success; ?></p>
                            <p class="redirect-message">Redirecting to login page in <span id="countdown">3</span> seconds...</p>
                            <button type="button" onclick="document.getElementById('successMessage').style.display='none'; clearTimeout(redirectTimer);">×</button>
                        </div>
                    </div>
                    <script>
                        // Countdown timer
                        var seconds = 3;
                        var countdownElement = document.getElementById('countdown');
                        var countdownInterval = setInterval(function() {
                            seconds--;
                            countdownElement.textContent = seconds;
                            if (seconds <= 0) {
                                clearInterval(countdownInterval);
                            }
                        }, 1000);
                        
                        // Redirect after 3 seconds
                        var redirectTimer = setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 3000);
                    </script>
                <?php endif; ?>                
                <form method="post" action="register.php">
                    <div class="form-section">
                        <h3>Account Information</h3>
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Delivery Address</h3>
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="address">Street Address</label>
                                <input type="text" id="address" name="address" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" required>
                            </div>
                            <div class="form-group">
                                <label for="country">Country</label>
                                <input type="text" id="country" name="country" required>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" name="register" class="register-btn">Create Account</button>
                </form>
                
                <div class="login-link">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
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