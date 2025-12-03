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
    
    if($action == 'delete') {
        $build_id = $_POST['build_id'] ?? 0;
        
        try {
            // Delete build components first (foreign key)
            $stmt = $pdo->prepare("DELETE FROM build_components WHERE build_id = ?");
            $stmt->execute([$build_id]);
            
            // Then delete the build
            $stmt = $pdo->prepare("DELETE FROM builds WHERE id = ?");
            $stmt->execute([$build_id]);
            
            header("Location: admin-builds.php?success=Build deleted successfully");
            exit();
            
        } catch(PDOException $e) {
            header("Location: admin-builds.php?error=Failed to delete build");
            exit();
        }
    }
}

// If no valid action, redirect back
header("Location: admin-builds.php");
exit();
?>