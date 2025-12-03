
<?php
// Database configuration for XAMPP/Localhost
$host = 'localhost';       
$dbname = 'pc_builders';       
$username = 'root';            
$password = '';               

// Create connection using PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>