<?php
session_start();
require_once "db_conn.php";

// Initialize response array
$response = array(
    'success' => false,
    'message' => '',
    'cart_count' => 0
);

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if product_id and quantity are set
    if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        
        // Validate quantity
        if ($quantity <= 0) {
            $response['message'] = 'Invalid quantity';
            echo json_encode($response);
            exit();
        }
        
        // Initialize cart if it doesn't exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
        
        // Check if product exists in database and get its details
        $sql = "SELECT id, name, price, stock FROM products WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $product_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($product = mysqli_fetch_assoc($result)) {
                // Check if there's enough stock
                if ($product['stock'] >= $quantity) {
                    // Check if product already in cart
                    $product_exists = false;
                    foreach ($_SESSION['cart'] as $key => $item) {
                        if (isset($item['id']) && $item['id'] == $product_id) {
                            // Make sure we don't exceed stock
                            $new_quantity = min($item['quantity'] + $quantity, $product['stock']);
                            $_SESSION['cart'][$key]['quantity'] = $new_quantity;
                            $product_exists = true;
                            break;
                        }
                    }
                    
                    // If product is not in cart, add it
                    if (!$product_exists) {
                        $_SESSION['cart'][] = array(
                            'id' => $product_id,
                            'name' => $product['name'],
                            'price' => $product['price'],
                            'quantity' => $quantity
                        );
                    }
                    
                    // Calculate cart count
                    $cart_count = 0;
                    foreach ($_SESSION['cart'] as $item) {
                        $cart_count += $item['quantity'];
                    }
                    
                    $response['success'] = true;
                    $response['message'] = $product['name'] . ' added to cart';
                    $response['cart_count'] = $cart_count;
                } else {
                    $response['message'] = 'Not enough stock available. Only ' . $product['stock'] . ' items in stock.';
                }
            } else {
                $response['message'] = 'Product not found';
            }
            
            mysqli_stmt_close($stmt);
        } else {
            $response['message'] = 'Database error: ' . mysqli_error($conn);
        }
    } else {
        $response['message'] = 'Missing product ID or quantity';
    }
} else {
    $response['message'] = 'Invalid request method';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;