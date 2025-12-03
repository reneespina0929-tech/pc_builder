<?php
require_once 'includes/config.php';

echo "<h2>🔧 Fixing Owner Admin Access</h2>";

try {
    // Step 1: Check if is_admin column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
    $exists = $stmt->fetch();
    
    if(!$exists) {
        echo "<p>📌 Adding 'is_admin' column to users table...</p>";
        $pdo->exec("ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0 AFTER password");
        echo "<p>✅ Column added successfully!</p>";
    } else {
        echo "<p>✅ Column 'is_admin' already exists</p>";
    }
    
    // Step 2: Check all users and their admin status
    echo "<h3>Current Users:</h3>";
    echo "<table border='1' cellpadding='10' style='background: white; width: 100%;'>";
    echo "<tr style='background: #04192F; color: white;'><th>ID</th><th>Username</th><th>Email</th><th>Is Admin</th></tr>";
    
    $stmt = $pdo->query("SELECT id, username, email, is_admin FROM users");
    while($user = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td><strong>" . $user['username'] . "</strong></td>";
        echo "<td>" . $user['email'] . "</td>";
        echo "<td>" . ($user['is_admin'] ? '✅ YES' : '❌ NO') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Step 3: Make 'owner' user an admin
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'owner'");
    $stmt->execute();
    $owner = $stmt->fetch();
    
    if($owner) {
        if($owner['is_admin'] == 1) {
            echo "<p>✅ User 'owner' is already an admin!</p>";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET is_admin = 1 WHERE username = 'owner'");
            $stmt->execute();
            echo "<p>✅ User 'owner' has been made an admin!</p>";
        }
    } else {
        echo "<p>⚠️ No user with username 'owner' found!</p>";
        echo "<p>Creating owner account...</p>";
        
        $username = 'owner';
        $email = 'owner@ubuild.com';
        $password = password_hash('owner123', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, 1)");
        $stmt->execute([$username, $email, $password]);
        
        echo "<p>✅ Owner account created!</p>";
        echo "<p><strong>Username:</strong> owner</p>";
        echo "<p><strong>Password:</strong> owner123</p>";
    }
    
    echo "<hr>";
    echo "<h3>✅ All Done!</h3>";
    echo "<p>Now logout and login again as 'owner' to access the admin panel.</p>";
    echo "<p><a href='auth/logout.php' style='background: #04192F; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0;'>Logout Now</a></p>";
    echo "<p><a href='auth/login.php'>Go to Login Page</a></p>";
    
} catch(PDOException $e) {
    echo "<p style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>❌ Error: " . $e->getMessage() . "</p>";
    
    // If column doesn't exist error, show manual SQL
    if(strpos($e->getMessage(), 'Unknown column') !== false) {
        echo "<hr>";
        echo "<h3>⚠️ Manual Fix Required</h3>";
        echo "<p>Please run this SQL in phpMyAdmin:</p>";
        echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0 AFTER password;</pre>";
        echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>UPDATE users SET is_admin = 1 WHERE username = 'owner';</pre>";
    }
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
    h2 { color: #04192F; margin-bottom: 20px; }
    h3 { color: #04192F; margin-top: 30px; }
    p { 
        background: white; 
        padding: 15px; 
        border-radius: 5px; 
        margin: 10px 0;
        line-height: 1.6;
    }
    table {
        margin: 20px 0;
        border-collapse: collapse;
    }
    pre {
        overflow-x: auto;
    }
</style>