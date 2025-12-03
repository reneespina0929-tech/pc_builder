<?php
// Start session
session_start();

// Include database connection
require_once '../includes/config.php';

// Check if form was submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data and clean it
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Validate input
    if(empty($username) || empty($password)) {
        header("Location: login.php?error=Please fill in all fields");
        exit();
    }
    
    try {
        // Prepare SQL statement to prevent SQL injection
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        
        // Fetch user data
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // DEBUG: Check if user exists
        if(!$user) {
            header("Location: login.php?error=User not found. Please check your username/email.");
            exit();
        }
        
        // DEBUG: Check password verification
        if(!password_verify($password, $user['password'])) {
            header("Location: login.php?error=Incorrect password. Please try again.");
            exit();
        }
        
        // Password is correct! Create session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        
        // Redirect to homepage
        header("Location: ../pages/homepage.php?success=Welcome back, " . urlencode($user['username']));
        exit();
        
    } catch(PDOException $e) {
        // Show actual error for debugging
        header("Location: login.php?error=Database error: " . urlencode($e->getMessage()));
        exit();
    }
    
} else {
    // If someone tries to access this file directly
    header("Location: login.php");
    exit();
}
?>