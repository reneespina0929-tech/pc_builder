<?php
session_start();
require_once '../includes/config.php';

// Protect this page
if(!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php?error=Please login to view your cart");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if user is admin
$is_admin = false;
$stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$is_admin = $user && $user['is_admin'] == 1;

// Get cart count
$stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$result = $stmt->fetch();
$cart_count = $result['total'] ?? 0;

// Fetch cart items
try {
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            p.name,
            p.specs,
            p.price,
            p.wattage,
            p.stock,
            p.image_url,
            p.category,
            (p.price * c.quantity) as subtotal
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
        ORDER BY c.added_at DESC
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total_price = array_sum(array_column($cart_items, 'subtotal'));
    $total_wattage = array_sum(array_map(function($item) {
        return $item['wattage'] * $item['quantity'];
    }, $cart_items));
    
} catch(PDOException $e) {
    $cart_items = [];
    $total_price = 0;
    $total_wattage = 0;
    $error = "Error loading cart: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UBUILD - Shopping Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cart.css">
</head>
<body>
    <header>
        <h1>UBUILD</h1>
        <div class="login-register">
            <a href="cart.php" style="position: relative; font-size: 20px; margin-right: 20px;">
                <i class="fas fa-shopping-cart"></i>
                <?php if($cart_count > 0): ?>
                    <span style="position: absolute; top: -8px; right: -8px; background: #ff4444; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold;">
                        <?php echo $cart_count; ?>
                    </span>
                <?php endif; ?>
            </a>
            
            <span style="margin-right: 15px;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            <a href="../auth/logout.php">Logout</a>
        </div>
    </header>

    <nav class="nav-bar">
        <ul>
            <li><a href="homepage.php">Home</a></li>
            <li><a href="pre-built.php">Pre-Built</a></li>
            <li><a href="system-builder.php">System Builder</a></li>
            <li>
                <a href="#" id="componentsLink">Components</a>
                <div class="dropdown" id="componentsDropdown">
                    <a href="cpu.php">CPU</a>
                    <a href="motherboard.php">Motherboard</a>
                    <a href="cpu-cooler.php">CPU Cooler</a>
                    <a href="ram.php">RAM</a>
                    <a href="storage.php">Storage</a>
                    <a href="gpu.php">GPU</a>
                    <a href="power-supply.php">Power Supply</a>
                    <a href="case.php">Case</a>
                </div>
            </li>
            
            <?php if($is_admin): ?>
                <li><a href="../admin/admin-products.php" style="color: #ffc107; font-weight: bold;">⚙️ Admin Panel</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <h1><i class="fas fa-shopping-cart"></i> Shopping Cart</h1>
        </div>

        <?php if(empty($cart_items)): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h2>Your cart is empty</h2>
                <p>Add some awesome PC components to get started!</p>
                <a href="system-builder.php" class="btn btn-checkout">Start Building</a>
            </div>
        <?php else: ?>
            <div class="cart-content">
                <div class="cart-items">
                    <h2 style="margin-bottom: 20px; color: #04192F;">Cart Items (<?php echo count($cart_items); ?>)</h2>
                    
                    <?php foreach($cart_items as $item): ?>
                        <div class="cart-item" data-product-id="<?php echo $item['product_id']; ?>">
                            <div class="item-image">
                                <i class="fas fa-microchip"></i>
                            </div>
                            <div class="item-details">
                                <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="item-specs"><?php echo htmlspecialchars($item['specs']); ?></div>
                                <div class="item-specs">Power: <?php echo $item['wattage']; ?>W | Stock: <?php echo $item['stock']; ?> available</div>
                                <div class="item-price">₱<?php echo number_format($item['price'], 2); ?> each</div>
                            </div>
                            <div class="item-actions">
                                <div class="quantity-controls">
                                    <button class="qty-btn" onclick="updateQuantity(<?php echo $item['product_id']; ?>, -1)">-</button>
                                    <input type="number" class="qty-input" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" onchange="setQuantity(<?php echo $item['product_id']; ?>, this.value)">
                                    <button class="qty-btn" onclick="updateQuantity(<?php echo $item['product_id']; ?>, 1)">+</button>
                                </div>
                                <div style="font-weight: bold; font-size: 18px; color: #04192F;">
                                    ₱<?php echo number_format($item['subtotal'], 2); ?>
                                </div>
                                <button class="btn btn-remove" onclick="removeItem(<?php echo $item['product_id']; ?>)">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <div class="summary-title">Order Summary</div>
                    
                    <div class="summary-row">
                        <span>Items (<?php echo array_sum(array_column($cart_items, 'quantity')); ?>):</span>
                        <span>₱<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Total Wattage:</span>
                        <span><?php echo $total_wattage; ?>W</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span style="color: #28a745;">FREE</span>
                    </div>
                    
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>₱<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    
                    <button class="btn-checkout" onclick="checkout()">
                        <i class="fas fa-lock"></i> Proceed to Checkout
                    </button>
                    
                    <button class="btn-continue" onclick="window.location.href='system-builder.php'">
                        Continue Shopping
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <div class="upper-links">
            <h1>UBUILD</h1>
            <ul>
                <li><a href="homepage.php">Home</a></li>
                <li><a href="pre-built.php">Pre-built</a></li>
                <li><a href="system-builder.php">System Builder</a></li>
                <li><a href="#">Components</a></li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </div>
        <hr>
        <div class="bottom-links">
            <p>© 2025 UBUILD. Powering your digital frontier.</p>
            <ul>
                <li><a href="">Privacy policy</a></li>
                <li><a href="">Terms of Service</a></li>
                <li><a href="">Cookie Policy</a></li>
            </ul>
        </div>
    </footer>

    <script>
        const componentsLink = document.getElementById('componentsLink');
        const componentsDropdown = document.getElementById('componentsDropdown');

        componentsLink.addEventListener('click', function(e) {
            e.preventDefault();
            componentsDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function(e) {
            if (!componentsLink.contains(e.target) && !componentsDropdown.contains(e.target)) {
                componentsDropdown.classList.remove('show');
            }
        });

        async function updateQuantity(productId, change) {
            const item = document.querySelector(`[data-product-id="${productId}"]`);
            const input = item.querySelector('.qty-input');
            const newQty = parseInt(input.value) + change;
            
            if(newQty < 1) return;
            if(newQty > parseInt(input.max)) {
                alert('Cannot add more than available stock!');
                return;
            }
            
            await setQuantity(productId, newQty);
        }

        async function setQuantity(productId, quantity) {
            try {
                const formData = new FormData();
                formData.append('action', 'update');
                formData.append('product_id', productId);
                formData.append('quantity', quantity);
                
                const response = await fetch('../api/cart-actions.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if(result.success) {
                    location.reload();
                } else {
                    alert(result.error);
                }
            } catch(error) {
                console.error('Error:', error);
                alert('Failed to update cart');
            }
        }

        async function removeItem(productId) {
            if(!confirm('Remove this item from cart?')) return;
            
            try {
                const formData = new FormData();
                formData.append('action', 'remove');
                formData.append('product_id', productId);
                
                const response = await fetch('../api/cart-actions.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if(result.success) {
                    location.reload();
                } else {
                    alert(result.error);
                }
            } catch(error) {
                console.error('Error:', error);
                alert('Failed to remove item');
            }
        }

        function checkout() {
            alert('Checkout feature coming soon! 🚀\n\nThis will integrate with payment gateways and order processing.');
            // TODO: Implement checkout page
        }
    </script>
</body>
</html>