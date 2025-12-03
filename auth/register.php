<?php
session_start();

// If user is already logged in, redirect to homepage
if(isset($_SESSION['user_id'])) {
    header("Location: Homepage.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UBUILD - Register</title>
    <link rel="stylesheet" href="../assets/css/register.css">
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
            <li><a href="pages/contacts.php">Contact Us</a></li>
        </ul>
    </nav>

    <!-- Main Content Area -->
    <main class="main-content">
        <div class="signup-container">
            <h2>Create an account</h2>

            <?php
            // Display error messages
            if(isset($_GET['error'])) {
                echo '<p style="color: red; text-align: center; padding: 10px; background: rgba(255,0,0,0.1); border-radius: 5px; margin-bottom: 20px;">' . htmlspecialchars($_GET['error']) . '</p>';
            }
            ?>

            <form action="register_process.php" method="POST">
                <div class="form-grid">
                    <!-- Left Column -->
                    <div class="form-column">
                        <div class="form-group">
                            <label for="username" class="sr-only">Username</label>
                            <input type="text" id="username" name="username" placeholder="Username" required>
                        </div>
                        <div class="form-group">
                            <label for="email" class="sr-only">Email</label>
                            <input type="email" id="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <label for="email-confirm" class="sr-only">Email confirmation</label>
                            <input type="email" id="email-confirm" name="email_confirm" placeholder="Email confirmation" required>
                        </div>
                        <div class="form-group">
                            <label for="password" class="sr-only">Password</label>
                            <input type="password" id="password" name="password" placeholder="Password" required>
                            <p class="password-hint">Your password must contain at least 8 characters.</p>
                        </div>
                        <div class="form-group">
                            <label for="password-confirm" class="sr-only">Password confirmation</label>
                            <input type="password" id="password-confirm" name="password_confirm" placeholder="Password confirmation" required>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="form-column">
                        <div class="form-group">
                            <label for="phone" class="sr-only">Enter Your Phone Number</label>
                            <input type="tel" id="phone" name="phone" placeholder="Enter Your Phone Number">
                        </div>
                        <div class="form-group checkbox-group">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">I agree to the Terms & Conditions and Privacy Policy</label>
                        </div>
                        <div class="form-group checkbox-group">
                            <input type="checkbox" id="newsletter" name="newsletter">
                            <label for="newsletter">Subscribe to our newsletter for updates and offers (optional)</label>
                        </div>
                        
                        <button type="submit" class="btn-submit">Create Account</button>

                        <div class="footer-link">
                            <a href="login.php">Already have an account?</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script src="../assets/js/register.js"></script>

</body>
</html>