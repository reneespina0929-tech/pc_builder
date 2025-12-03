<?php
require_once 'includes/config.php';

echo "<h2>Create Admin User</h2>";

try {
    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin'");
    $stmt->execute();
    $existing = $stmt->fetch();
    
    if($existing) {
        echo "<p>❌ Admin user already exists!</p>";
        echo "<p>Username: <strong>admin</strong></p>";
        echo "<p>If you forgot the password, delete this user in phpMyAdmin and run this page again.</p>";
    } else {
        // Create admin user
        $username = 'admin';
        $email = 'admin@ubuild.com';
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);
        
        echo "<p>✅ Admin user created successfully!</p>";
        echo "<p>Username: <strong>admin</strong></p>";
        echo "<p>Password: <strong>admin123</strong></p>";
        echo "<p><a href='../auth/login.php'>Go to Login Page</a></p>";
    }
    
} catch(PDOException $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='test-db.php'>← Back to Database Test</a></p>";
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