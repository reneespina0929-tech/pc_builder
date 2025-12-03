<?php
require_once '../includes/config.php';

echo "<h2>Adding Admin Column to Users Table</h2>";

try {
    // Check if column already exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
    $exists = $stmt->fetch();
    
    if($exists) {
        echo "<p>⚠️ Column 'is_admin' already exists!</p>";
    } else {
        // Add is_admin column
        $pdo->exec("ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0 AFTER password");
        echo "<p>✅ Column 'is_admin' added successfully!</p>";
    }
    
    // Check if owner user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'owner'");
    $stmt->execute();
    $owner = $stmt->fetch();
    
    if($owner) {
        // Update existing owner to be admin
        $stmt = $pdo->prepare("UPDATE users SET is_admin = 1 WHERE username = 'owner'");
        $stmt->execute();
        echo "<p>✅ Existing 'owner' user updated to admin!</p>";
    } else {
        // Create owner user
        $username = 'owner';
        $email = 'owner@ubuild.com';
        $password = password_hash('owner123', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, 1)");
        $stmt->execute([$username, $email, $password]);
        
        echo "<p>✅ Admin user 'owner' created successfully!</p>";
        echo "<p><strong>Username:</strong> owner</p>";
        echo "<p><strong>Password:</strong> owner123</p>";
    }
    
    echo "<hr>";
    echo "<h3>✅ Setup Complete!</h3>";
    echo "<p>Only users with username 'owner' can access the admin panel.</p>";
    echo "<p><a href='auth/login.php'>Go to Login Page</a></p>";
    
} catch(PDOException $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h2 { color: #04192F; }
    p { 
        background: white; 
        padding: 15px; 
        border-radius: 5px; 
        margin: 10px 0;
    }
</style>