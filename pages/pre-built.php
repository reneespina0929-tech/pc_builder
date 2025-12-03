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
            <title>UBUILD - Pre-Built</title>
            <link rel="stylesheet" href="../assets/css/pre-built.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

            <div class="hero-image"></div>
            
            <div class="pc-container">
            <div class="pc-types">
                <div class="pc-nav" role="tablist" aria-label="PC types">
                <!-- data-title / data-text / data-img store the content for each tab -->
                <button role="tab" aria-selected="true" class="tab-btn active"
                    data-title="Pre-built Gaming PC"
                    data-text="Let’s be real—everyone loves gaming. Whether you’re grinding ranked, exploring open worlds, or just vibing with friends on Discord, having the right PC makes all the difference. But picking one? Yeah, that’s where things get messy. Every rig looks the same, specs sound like alien code, and your wallet’s just sitting there screaming 'choose wisely.'"
                    data-img="../assets/images/download (88).jpeg">
                    GAMING PC
                </button>

                <button role="tab" aria-selected="false" class="tab-btn"
                    data-title="Pre-built Budget PC"
                    data-text="Not everyone’s trying to drop a bag on a monster rig—and honestly, you don’t need to. If you just want a solid PC that gets the job done for gaming, school, work, or content on the side, a pre-built budget setup is the way to go.

No stress about parts, no overthinking compatibility—just plug, play, and flex your savings. We’ve hand-picked the best cheap pre-built PCs that deliver legit performance without breaking the bank. Perfect for beginners, casual gamers, or anyone who wants smooth performance on a tight budget."
                    data-img="../assets/images/pc.jpeg">
                    BUDGET PC
                </button>

                <button role="tab" aria-selected="false" class="tab-btn"
                    data-title="All-In-One PC"
                    data-text="Need something tidy and compact? All-in-one PCs save desk space while still being reliable for browsing, streaming, and light creative tasks."
                    data-img="../assets/images/Black PC.jpeg">
                    ALL IN ONE PC
                </button>
                </div>

                <div class="pc-content" id="pcContent">
                <div class="details">
                    <h2 id="pcTitle">Pre-Built Gaming PC</h2>
                    <p id="pcText">Let’s be real—everyone loves gaming. Whether you’re grinding ranked, exploring open worlds, or just vibing with friends on Discord, having the right PC makes all the difference. But picking one? Yeah, that’s where things get messy. Every rig looks the same, specs sound like alien code, and your wallet’s just sitting there screaming "choose wisely."</p>
                    <p id="pcText2">That’s why we did the hard part for you. We rounded up the most powerful and popular gaming PC builds from trusted merchants, so you don’t waste hours comparing specs that barely matter. Whether you’re chasing max FPS or future-proof performance, you’ll find the build that fits your playstyle and your budget.</p>
                </div>

                <div class="pc-img">
                    <img id="pcImage" src="../assets/images/download (88).jpeg" alt="PC image">
                </div>
                </div>
            </div>
            </div>
            <div class="cta-section">
                    <div class="cta-details">
                        <h1>Ready to Power Up?</h1>
                        <p>Stop overthinking specs and start playing, creating, and working on a rig that’s built to perform straight outta the box. Whether you’re after a budget-friendly setup, a clean AIO workstation, or a gaming beast, we’ve got you covered.</p>
                        <button id="Build-Now">Build Now</button>
                    </div>
                </div>  
            <footer>
                <div class="upper-links">
                    <h1>UBUILD</h1>
                    <ul>
                        <li><a href="homepage.php">Home</a></li>
                        <li><a href="pre-built.php">Pre-built</a></li>
                        <li><a href="system-buildder.php">System Builder</a></li>
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
            <script src="../assets/js/pre-built.js">
                
            </script>
        </body>
        </html>