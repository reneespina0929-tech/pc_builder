<?php
session_start();
require_once '../includes/config.php';

header('Content-Type: application/json');

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Please login first']);
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

try {
    switch($action) {
        case 'add':
            $product_id = $_POST['product_id'] ?? 0;
            $quantity = $_POST['quantity'] ?? 1;
            
            // Check if product exists
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            
            if(!$product) {
                echo json_encode(['success' => false, 'error' => 'Product not found']);
                exit();
            }
            
            // Check stock
            if($product['stock'] < $quantity) {
                echo json_encode(['success' => false, 'error' => 'Insufficient stock']);
                exit();
            }
            
            // Check if already in cart
            $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user_id, $product_id]);
            $existing = $stmt->fetch();
            
            if($existing) {
                // Update quantity
                $new_quantity = $existing['quantity'] + $quantity;
                if($new_quantity > $product['stock']) {
                    echo json_encode(['success' => false, 'error' => 'Cannot add more than available stock']);
                    exit();
                }
                
                $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$new_quantity, $user_id, $product_id]);
            } else {
                // Insert new
                $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $product_id, $quantity]);
            }
            
            // Get new cart count
            $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch();
            $cart_count = $result['total'] ?? 0;
            
            echo json_encode([
                'success' => true, 
                'message' => 'Added to cart successfully!',
                'cart_count' => $cart_count
            ]);
            break;
            
        case 'update':
            $product_id = $_POST['product_id'] ?? 0;
            $quantity = $_POST['quantity'] ?? 1;
            
            if($quantity < 1) {
                echo json_encode(['success' => false, 'error' => 'Quantity must be at least 1']);
                exit();
            }
            
            // Check stock
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            
            if($quantity > $product['stock']) {
                echo json_encode(['success' => false, 'error' => 'Insufficient stock']);
                exit();
            }
            
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$quantity, $user_id, $product_id]);
            
            echo json_encode(['success' => true, 'message' => 'Cart updated']);
            break;
            
        case 'remove':
            $product_id = $_POST['product_id'] ?? 0;
            
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user_id, $product_id]);
            
            // Get new cart count
            $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch();
            $cart_count = $result['total'] ?? 0;
            
            echo json_encode([
                'success' => true, 
                'message' => 'Item removed from cart',
                'cart_count' => $cart_count
            ]);
            break;
            
        case 'clear':
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);
            
            echo json_encode(['success' => true, 'message' => 'Cart cleared']);
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>