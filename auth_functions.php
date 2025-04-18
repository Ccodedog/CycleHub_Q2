<?php
/**
 * Authenticates a user with the provided email and password
 * 
 * @param mysqli $conn Database connection
 * @param string $email User email
 * @param string $password User password
 * @return array Result with success status, message, and user data if successful
 */
function authenticateUser($conn, $email, $password) {
    // Check user credentials
    $sql = "SELECT id, name, email, password FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if(mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify password
        if(password_verify($password, $user["password"])) {
            return [
                'success' => true,
                'user' => $user
            ];
        } else {
            return [
                'success' => false,
                'message' => "Invalid email or password"
            ];
        }
    } else {
        return [
            'success' => false,
            'message' => "Invalid email or password"
        ];
    }
}