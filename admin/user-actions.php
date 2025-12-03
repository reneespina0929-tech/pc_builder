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
        $user_id = $_POST['user_id'] ?? 0;
        
        // Prevent admin from deleting themselves
        if($user_id == $_SESSION['user_id']) {
            header("Location: admin-users.php?error=You cannot delete yourself");
            exit();
        }
        
        try {
            // Delete user's build components first
            $pdo->query("DELETE bc FROM build_components bc 
                        INNER JOIN builds b ON bc.build_id = b.id 
                        WHERE b.user_id = $user_id");
            
            // Delete user's builds
            $stmt = $pdo->prepare("DELETE FROM builds WHERE user_id = ?");
            $stmt->execute([$user_id]);
            
            // Delete the user
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            
            header("Location: admin-users.php?success=User deleted successfully");
            exit();
            
        } catch(PDOException $e) {
            header("Location: admin-users.php?error=Failed to delete user");
            exit();
        }
    }
}

// If no valid action, redirect back
header("Location: admin-users.php");
exit();
?>