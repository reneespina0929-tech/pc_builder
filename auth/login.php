<?php
// Start session at the very top
session_start();

// If user is already logged in, redirect to homepage
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UBUILD Login</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

    <header>
        <h1>UBUILD</h1>
        <div class="login-register">
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </div>
    </header>

    <nav class="nav-bar">
        <ul>
            <li><a href="../pages/homepage.php">Home</a></li>
            <li><a href="../pages/pre-built.php">Pre-Built</a></li>
            <li><a href="../pages/system-builder.php">System Builder</a></li>
            <li>
                <a href="#" id="componentsLink">Components</a>
                <div class="dropdown" id="componentsDropdown">
                    <a href="pages/cpu.php">CPU</a>
                    <a href="pages/motherboard.php">Motherboard</a>
                    <a href="pages/cpu-cooler.php">CPU Cooler</a>
                    <a href="pages/ram.php">RAM</a>
                    <a href="pages/storage.php">Storage</a>
                    <a href="pages/gpu.php">GPU</a>
                    <a href="pages/power-supply.php">Power Supply</a>
                    <a href="pages/case.php">Case</a>
                </div>
            </li>
            <li><a href="Contacts.php">Contact Us</a></li>
        </ul>
    </nav>

    <main class="login-container">
        <div class="login-card">
            <h2 class="card-title">Login</h2>

            <?php
            // Display error message if login failed
            if(isset($_GET['error'])) {
                echo '<p style="color: red; text-align: center; background: rgba(255,0,0,0.1); padding: 10px; border-radius: 5px;">' . htmlspecialchars($_GET['error']) . '</p>';
            }
            
            // Display success message if coming from registration
            if(isset($_GET['success'])) {
                echo '<p style="color: green; text-align: center; background: rgba(0,255,0,0.1); padding: 10px; border-radius: 5px;">' . htmlspecialchars($_GET['success']) . '</p>';
            }
            ?>

            <form class="login-form" method="POST" action="login_process.php">
                
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Username or email" 
                    autocomplete="username"
                    required>
                
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Password" 
                    autocomplete="current-password"
                    required>
                
                <button type="submit" class="login-button">Login</button>
            </form>

            <div class="card-footer">
                <a href="register.php" class="footer-link">Create an account</a>
                <a href="#" class="footer-link">Forgot Password</a>
            </div>
        </div>
    </main>

    <script src="../assets/js/login.js"></script>
</body>
</html>