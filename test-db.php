<?php
// Test Database Connection
echo "<h2>UBUILD Database Connection Test</h2>";

require_once 'includes/config.php';

// Test 1: Check connection
echo "<h3>✅ Database Connected Successfully!</h3>";

// Test 2: Check if users table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $result = $stmt->fetch();
    
    if($result) {
        echo "<p>✅ Users table exists</p>";
        
        // Test 3: Count users
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $count = $stmt->fetch();
        echo "<p>👥 Total users in database: <strong>" . $count['count'] . "</strong></p>";
        
        // Test 4: Show all users (for debugging only - remove in production!)
        echo "<h3>Current Users:</h3>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Created</th></tr>";
        
        $stmt = $pdo->query("SELECT id, username, email, created_at FROM users");
        while($user = $stmt->fetch()) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['username'] . "</td>";
            echo "<td>" . $user['email'] . "</td>";
            echo "<td>" . $user['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Test 5: Try to verify the admin password
        echo "<h3>Testing Admin Login:</h3>";
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin'");
        $stmt->execute();
        $admin = $stmt->fetch();
        
        if($admin) {
            echo "<p>✅ Admin user found</p>";
            echo "<p>Username: <strong>admin</strong></p>";
            echo "<p>Testing password 'admin123'...</p>";
            
            if(password_verify('admin123', $admin['password'])) {
                echo "<p>✅ Password 'admin123' is CORRECT!</p>";
            } else {
                echo "<p>❌ Password 'admin123' is WRONG!</p>";
                echo "<p>Current password hash: " . $admin['password'] . "</p>";
            }
        } else {
            echo "<p>❌ Admin user not found in database</p>";
            echo "<p>🔧 You need to create an admin user. <a href='create-admin.php'>Click here</a></p>";
        }
        
    } else {
        echo "<p>❌ Users table does NOT exist! Please run the database setup SQL.</p>";
    }
    
} catch(PDOException $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Homepage</a></p>";
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h2 { color: #04192F; }
    table { 
        width: 100%; 
        background: white; 
        margin: 20px 0;
    }
    th { background: #04192F; color: white; }
    p { line-height: 1.8; }
</style>