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
    <title>UBUILD - Motherboard Components</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/motherboard.css">
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
            <h2>The Motherboard: The Heart of Your PC</h2>
            <p>Unite your CPU, RAM, GPU, and storage. The motherboard is the central nervous system, dictating performance, expandability, and stability. Choose wisely for your ultimate build.</p>
            <button>Explore our Motherboards</button>
        </div>
        <div class="hero-image">
            <img src="../assets/images//A computer circuit board with a circuit board in neon colors_ _ Premium AI-generated image.jpeg" alt="Motherboard with RGB">
        </div>
    </section>

    <!-- Find Perfect Motherboard Section -->
    <section class="find-motherboard">
        <h2>Find Your Perfect Motherboard</h2>
        <div class="motherboard-categories">
            <div class="category-card">
                <img src="https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=400&h=200&fit=crop" alt="Gaming Motherboard">
                <div class="category-content">
                    <h3>Gaming & Performance</h3>
                    <p>Maximize your FPS and responsiveness with boards built for enthusiasts and overclocking.</p>
                </div>
            </div>
            <div class="category-card">
                <img src="https://images.unsplash.com/photo-1484788984921-03950022c9ef?w=400&h=200&fit=crop" alt="Workstation Setup">
                <div class="category-content">
                    <h3>Workstation & Productivity</h3>
                    <p>Stable platforms with extensive I/O for professional applications and content creation tasks.</p>
                </div>
            </div>
            <div class="category-card">
                <img src="https://images.unsplash.com/photo-1555617981-dac3880eac6e?w=400&h=200&fit=crop" alt="Compact Motherboard">
                <div class="category-content">
                    <h3>Compact & Mini-ITX</h3>
                    <p>Build powerful small form factor PCs without compromising on essential features or aesthetics.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Motherboard Products Section -->
    <section class="motherboard-products">
        <div class="products-grid">
            <div class="product-card">
                <img src="../assets/images//ASUS ROG Strix Z790-E Gaming WiFi II.jpeg" alt="ASUS ROG Strix Z790-F">
                <div class="product-content">
                    <p class="brand-name">Asus</p>
                    <h3>ASUS ROG Strix Z790-F Gaming WiFi II</h3>
                    <p class="product-specs">Intel Z790, DDR5, PCIe 5.0, Wi-Fi 6E</p>
                    <div class="product-footer">
                        <span class="price">$399.99</span>
                        <button class="view-btn">View</button>
                    </div>
                </div>
            </div>
            <div class="product-card">
                <img src="../assets/images/The MPG series brings out fashionable products by showing extremely unique styles and expressing a conceited attitude towards the challenge of the gaming world_ With extraordinary performance and style, the MPG se.jpeg" alt="MSI MPG B650">
                <div class="product-content">
                    <p class="brand-name">MSI</p>
                    <h3>MSI MPG B650 CARBON WIFI</h3>
                    <p class="product-specs">AMD B650, DDR5, PCIe 4.0, Wi-Fi 6E</p>
                    <div class="product-footer">
                        <span class="price">$279.99</span>
                        <button class="view-btn">View</button>
                    </div>
                </div>
            </div>
            <div class="product-card">
                <img src="../assets/images/GIGABYTE Z790 AORUS ELITE X AX DDR5 (Socket LGA 1700, Z790).jpeg" alt="GIGABYTE Z790">
                <div class="product-content">
                    <p class="brand-name">GIGABYTE</p>
                    <h3>GIGABYTE Z790 AORUS ELITE AX</h3>
                    <p class="product-specs">Intel Z790, DDR5, PCIe 5.0, Wi-Fi 6E</p>
                    <div class="product-footer">
                        <span class="price">$299.99</span>
                        <button class="view-btn">View</button>
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

    <script src="../assets/js/motherboard.js">
        
    </script>
</body>
</html>