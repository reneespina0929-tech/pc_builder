<?php
require_once 'includes/config.php';

echo "<h2>🛒 Setting Up Shopping Cart System</h2>";

try {
    // Create cart table
    $sql1 = "CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT DEFAULT 1,
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_product (user_id, product_id)
    )";
    
    $pdo->exec($sql1);
    echo "<p>✅ Cart table created successfully!</p>";
    
    // Create orders table
    $sql2 = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        order_number VARCHAR(50) UNIQUE NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
        shipping_address TEXT,
        payment_method VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    $pdo->exec($sql2);
    echo "<p>✅ Orders table created successfully!</p>";
    
    // Create order_items table
    $sql3 = "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        product_name VARCHAR(255) NOT NULL,
        product_specs TEXT,
        price DECIMAL(10,2) NOT NULL,
        quantity INT NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id)
    )";
    
    $pdo->exec($sql3);
    echo "<p>✅ Order items table created successfully!</p>";
    
    echo "<hr>";
    echo "<h3>✅ Shopping Cart System Ready!</h3>";
    echo "<p>You can now:</p>";
    echo "<ul style='background: white; padding: 20px; line-height: 2;'>";
    echo "<li>✅ Add products to cart</li>";
    echo "<li>✅ View cart items</li>";
    echo "<li>✅ Update quantities</li>";
    echo "<li>✅ Remove items</li>";
    echo "<li>✅ Checkout and create orders</li>";
    echo "</ul>";
    echo "<p><a href='pages/homepage.php'>Go to Homepage</a></p>";
    
} catch(PDOException $e) {
    echo "<p style='background: #f8d7da; color: #721c24; padding: 15px;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h2, h3 { color: #04192F; }
    p, ul { 
        background: white; 
        padding: 15px; 
        border-radius: 5px; 
        margin: 10px 0;
    }
</style>