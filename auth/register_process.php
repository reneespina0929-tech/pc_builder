<?php
session_start();
require_once '../includes/config.php';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get and sanitize form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $email_confirm = trim($_POST['email_confirm']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $phone = trim($_POST['phone']);
    $terms = isset($_POST['terms']) ? 1 : 0;
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    
    // Validation
    $errors = [];
    
    // Check if all required fields are filled
    if(empty($username) || empty($email) || empty($password)) {
        $errors[] = "Please fill in all required fields";
    }
    
    // Check email match
    if($email !== $email_confirm) {
        $errors[] = "Email addresses do not match";
    }
    
    // Check password match
    if($password !== $password_confirm) {
        $errors[] = "Passwords do not match";
    }
    
    // Check password length
    if(strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }
    
    // Validate email format
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Check if terms accepted
    if(!$terms) {
        $errors[] = "You must agree to the Terms & Conditions";
    }
    
    // If there are errors, redirect back with error message
    if(!empty($errors)) {
        $error_message = implode(", ", $errors);
        header("Location: register.php?error=" . urlencode($error_message));
        exit();
    }
    
    try {
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if($stmt->fetch()) {
            header("Location: register.php?error=Username already taken");
            exit();
        }
        
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->fetch()) {
            header("Location: register.php?error=Email already registered");
            exit();
        }
        
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);
        
        // Redirect to login with success message
        header("Location: login.php?success=Registration successful! Please login.");
        exit();
        
    } catch(PDOException $e) {
        header("Location: register.php?error=Registration failed. Please try again.");
        exit();
    }
    
} else {
    header("Location: register.php");
    exit();
}
?>