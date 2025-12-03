<?php
session_start();
require_once '../includes/config.php';
// Check if user is admin
$stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if(!$user || !$user['is_admin']) {
    header("Location: ../pages/homepage.php?error=Access denied - Admin only");
    exit();
}

// Protect this page
if(!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php?error=Please login first");
    exit();
}

// Check if user is admin
$stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if(!$user || !$user['is_admin']) {
    header("Location: ../pages/homepage.php?error=Access denied");
    exit();
}

// Handle actions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if($action == 'add') {
        // Add product
        $category = $_POST['category'];
        $name = $_POST['name'];
        $specs = $_POST['specs'] ?? '';
        $price = $_POST['price'];
        $wattage = $_POST['wattage'] ?? 0;
        $stock = $_POST['stock'];
        $image_url = $_POST['image_url'] ?? '';
        
        try {
            $stmt = $pdo->prepare("INSERT INTO products (category, name, specs, price, wattage, stock, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$category, $name, $specs, $price, $wattage, $stock, $image_url]);
            
            header("Location: admin-products.php?success=Product added successfully");
            exit();
        } catch(PDOException $e) {
            header("Location: admin-products.php?error=Failed to add product");
            exit();
        }
    }
    
    elseif($action == 'edit') {
        // Edit product
        $product_id = $_POST['product_id'];
        $category = $_POST['category'];
        $name = $_POST['name'];
        $specs = $_POST['specs'] ?? '';
        $price = $_POST['price'];
        $wattage = $_POST['wattage'] ?? 0;
        $stock = $_POST['stock'];
        $image_url = $_POST['image_url'] ?? '';
        
        try {
            $stmt = $pdo->prepare("UPDATE products SET category = ?, name = ?, specs = ?, price = ?, wattage = ?, stock = ?, image_url = ? WHERE id = ?");
            $stmt->execute([$category, $name, $specs, $price, $wattage, $stock, $image_url, $product_id]);
            
            header("Location: admin-products.php?success=Product updated successfully");
            exit();
        } catch(PDOException $e) {
            header("Location: admin-products.php?error=Failed to update product");
            exit();
        }
    }
    
    elseif($action == 'delete') {
        // Delete product
        $product_id = $_POST['product_id'] ?? 0;
        
        try {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            
            header("Location: admin-products.php?success=Product deleted successfully");
            exit();
        } catch(PDOException $e) {
            header("Location: admin-products.php?error=Failed to delete product");
            exit();
        }
    }
}

// If no valid action, redirect back
header("Location: admin-products.php");
exit();
?>