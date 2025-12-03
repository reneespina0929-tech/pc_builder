<?php
session_start();
require_once '../includes/config.php';

// Check if user is admin
$is_admin = false;
$cart_count = 0;

if(isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    $is_admin = $user && $user['is_admin'] == 1;
    
    // Get cart count
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    $cart_count = $result['total'] ?? 0;
}

$is_admin = false;
if(isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    $is_admin = $user && $user['is_admin'] == 1;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UBUILD - CPU Components</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/cpu.css">
</head>
<body>
    <header>
        <h1>UBUILD</h1>
        <div class="login-register">
        <?php if(isset($_SESSION['user_id'])): ?>
            <!-- Shopping Cart Icon -->
            <a href="cart.php" style="position: relative; font-size: 20px; margin-right: 20px;">
                <i class="fas fa-shopping-cart"></i>
                <?php if($cart_count > 0): ?>
                    <span style="position: absolute; top: -8px; right: -8px; background: #ff4444; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold;">
                        <?php echo $cart_count; ?>
                    </span>
                <?php endif; ?>
            </a>
            
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            <a href="../auth/logout.php">Logout</a>
            <?php else: ?>
            <a href="../auth/login.php">Login</a>
            <a href="../auth/register.php">Register</a>
            <?php endif; ?>
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
        <li><a href="contacts.php">Contact Us</a></li>
                <!-- ONLY SHOW ADMIN PANEL IF USER IS ADMIN -->
        <?php if($is_admin): ?>
            <li><a href="../admin/admin-products.php" style="color: #ffc107; font-weight: bold;">⚙️ Admin Panel</a></li>
        <?php endif; ?>
    </ul>
</nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h2>Powering Your PC: The Heart of Performance</h2>
            <p>Discover how the Central Processing Unit (CPU) drives every calculation, task, and game on your computer. It's the brain of your machine, dictating speed and efficiency.</p>
            <button>Explore CPU Models</button>
        </div>
        <div class="hero-image">
            <img src="https://images.unsplash.com/photo-1555617981-dac3880eac6e?w=600&h=400&fit=crop" alt="CPU Circuit Board">
        </div>
    </section>

    <!-- CPU Features Section -->
    <section class="cpu-features">
        <h2>The Central Processing Unit: Your PC's Brain</h2>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-microchip"></i>
                <h3>Processing Power</h3>
                <p>The CPU performs billions of calculations per second, essential for running applications and executing complex operations.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-tasks"></i>
                <h3>Multitasking Master</h3>
                <p>Modern CPUs excel at handling multiple tasks simultaneously, ensuring a smooth experience whether you're gaming or working.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-plus-square"></i>
                <h3>Foundation of Performance</h3>
                <p>A powerful CPU is the bedrock of a high-performing PC, impacting everything from boot times to rendering speeds.</p>
            </div>
        </div>
    </section>

    <!-- Popular CPU Models Section -->
    <section class="popular-cpus">
        <div class="cpu-grid">
            <div class="cpu-card">
                <img src="../assets/images/Intel Core i9-13900K.jpeg" alt="Intel Core i9-13900K">
                <div class="cpu-card-content">
                    <h3>Intel Core i9-13900K</h3>
                    <span class="cpu-badge">High Performance</span>
                    <div class="cpu-specs">
                        <p>24 Cores / 32 Threads</p>
                        <p>Up to 5.8 GHz Boost</p>
                        <p>36MB Cache</p>
                    </div>
                </div>
            </div>
            <div class="cpu-card">
                <img src="https://images.unsplash.com/photo-1591799265444-d66432b91588?w=400&h=200&fit=crop" alt="AMD Ryzen 9 7950X">
                <div class="cpu-card-content">
                    <h3>AMD Ryzen 9 7950X</h3>
                    <span class="cpu-badge">Gaming Ready</span>
                    <div class="cpu-specs">
                        <p>16 Cores / 32 Threads</p>
                        <p>Up to 5.7 GHz Boost</p>
                        <p>80MB Cache</p>
                    </div>
                </div>
            </div>
            <div class="cpu-card">
                <img src="https://images.unsplash.com/photo-1555617981-dac3880eac6e?w=400&h=200&fit=crop" alt="Intel Core i5-13600K">
                <div class="cpu-card-content">
                    <h3>Intel Core i5-13600K</h3>
                    <span class="cpu-badge">Best Value</span>
                    <div class="cpu-specs">
                        <p>14 Cores / 20 Threads</p>
                        <p>Up to 5.1 GHz Boost</p>
                        <p>24MB Cache</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="upper-links">
            <h1>UBUILD</h1>
            <ul>
                <li><a href="homepage.php">Home</a></li>
                <li><a href="pre-built.php">Pre-built</a></li>
                <li><a href="system-builder.php">System Builder</a></li>
                <li><a href="component.php">Components</a></li>
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

    <script src="../assets/js/cpu.js">
    </script>
</body>
</html>