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
    <title>UBUILD - GPU Components</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/gpu.css">
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
            <h2>Unleash Peak Performance</h2>
            <p>Discover the power of Graphics Processing Units. Essential for immersive gaming, rapid content creation, and cutting-edge AI/ML. Elevate your PC experience with the right GPU.</p>
            <button>Explore Gpu Models</button>
        </div>
        <div class="hero-image">
            <img src="../assets/images/NVIDIA vs_ AMD – Which is Better_.jpeg" alt="GPU Circuit Board">
        </div>
    </section>

    <!-- What Does GPU Do Section -->
    <section class="gpu-features">
        <h2>What Does a GPU Do?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-desktop"></i>
                <h3>Blazing Fast Graphics</h3>
                <p>Render stunning, lifelike 3D environments and scenes with unparalleled speed and fidelity for HD, ultra-wide gaming and productivity.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-file-alt"></i>
                <h3>Parallel Processing Power</h3>
                <p>Handle complex tasks in mass parallel—perfect for video editing, simulations, calculations and data processing incredibly efficient.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-brain"></i>
                <h3>AI & Machine Learning</h3>
                <p>Accelerate deep learning inference and machine learning training. Crucial for deep learning models and data analysis.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-tv"></i>
                <h3>Advanced Display Support</h3>
                <p>Drive multiple high-res monitors, resolutions, including 4K and 8K displays, for immersive, expansive workspace and vivid viewing.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-bezier-curve"></i>
                <h3>Real-time Ray Tracing</h3>
                <p>Simulate realistic light behavior for lifelike shadows, and reflections that make next-level visuals, enhancing visual immersion drastically.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-bolt"></i>
                <h3>Efficient Power Delivery</h3>
                <p>Modern GPUs balance raw performance and maximize performance without excessive energy use, ensuring reliability and sustained usability.</p>
            </div>
        </div>
    </section>

    <!-- Why GPUs Matter Section -->
    <section class="gpu-benefits">
        <h2>Why GPUs Matter for you</h2>
        <div class="benefits-container">
            <div class="benefit-card">
                <img src="https://images.unsplash.com/photo-1593305841991-05c297ba4575?w=600&h=250&fit=crop" alt="Gaming Setup">
                <div class="benefit-content">
                    <h3>For the Ultimate Gaming Experience</h3>
                    <div class="benefit-item">
                        <h4>Stunning Visuals</h4>
                        <p>GPUs render complex game environments and character models with breathtaking detail, bringing visual realism to life with every frame.</p>
                    </div>
                    <div class="benefit-item">
                        <h4>High Frame Rates</h4>
                        <p>Achieve smooth, fast gameplay with high refresh rates, providing a competitive edge and a more enjoyable gaming session.</p>
                    </div>
                    <div class="benefit-item">
                        <h4>Immersive VR & Ray Tracing</h4>
                        <p>Power demanding virtual reality applications and unlock realistic lighting and reflections with real-time ray tracing technology.</p>
                    </div>
                </div>
            </div>
            <div class="benefit-card">
                <img src="https://images.unsplash.com/photo-1527689368864-3a821dbccc34?w=600&h=250&fit=crop" alt="Creative Workflow">
                <div class="benefit-content">
                    <h3>Accelerate Your Creative Workflow</h3>
                    <div class="benefit-item">
                        <h4>Faster Rendering</h4>
                        <p>GPUs significantly reduce rendering times for 3D renders, animations, and visual effects, boosting productivity for artists and designers.</p>
                    </div>
                    <div class="benefit-item">
                        <h4>Seamless Video Editing</h4>
                        <p>Edit high-resolution 4K or 8K video footage in real time without lag. Handle multiple streams of raw video for faster project turnaround.</p>
                    </div>
                    <div class="benefit-item">
                        <h4>3D Modeling & Animation</h4>
                        <p>Handle intensive 3D scenes with ease, enabling smooth manipulation, sculpting, and real-time previews of detailed models and animations.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular GPU Models Section -->
    <section class="popular-gpus">
        <h2>POPULAR GPU MODELS</h2>
        <div class="gpu-grid">
            <div class="gpu-card">
                <img src="../assets/images/rtx4090.jpeg" alt="GeForce RTX 4090">
                <h3>GeForce RTX 4090</h3>
                <div class="gpu-specs">
                    <p>VRAM: 24GB GDDR6</p>
                    <p>Boost Clock: 2.52 GHz</p>
                </div>
                <button>View Details</button>
            </div>
            <div class="gpu-card">
                <img src="../assets/images/AMD Radeon RX 7900 XT & Radeon RX 7900 XTX Gaming Review - Techgage.jpeg" alt="Radeon RX 7900 XTX">
                <h3>Radeon RX 7900 XTX</h3>
                <div class="gpu-specs">
                    <p>VRAM: 24GB GDDR6</p>
                    <p>Boost Clock: 2.50 GHz</p>
                </div>
                <button>View Details</button>
            </div>
            <div class="gpu-card">
                <img src="../assets/images/4070.jpeg" alt="GeForce RTX 4070 Ti SUPER">
                <h3>GeForce RTX 4070 Ti SUPER</h3>
                <div class="gpu-specs">
                    <p>VRAM: 16GB GDDR6</p>
                    <p>Boost Clock: 2.61 GHz</p>
                </div>
                <button>View Details</button>
            </div>
            <div class="gpu-card">
                <img src="../assets/images/ASROCK AMD Radeon RX 7800 XT Phantom Gaming 16GB OC GDDR6 256-bit.jpeg" alt="Radeon RX 7800 XT">
                <h3>Radeon RX 7800 XT</h3>
                <div class="gpu-specs">
                    <p>VRAM: 16GB GDDR6</p>
                    <p>Boost Clock: 2.43 GHz</p>
                </div>
                <button>View Details</button>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <h2>Ready to build your Dream PC?</h2>
        <button id="custom-build">Start Your Custom Build</button>
    </section>

    <!-- Footer -->
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

    <script>
        // Dropdown functionality
        const componentsLink = document.getElementById('componentsLink');
        const componentsDropdown = document.getElementById('componentsDropdown');

        componentsLink.addEventListener('click', function(e) {
            e.preventDefault();
            componentsDropdown.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!componentsLink.contains(e.target) && !componentsDropdown.contains(e.target)) {
                componentsDropdown.classList.remove('show');
            }
            
        document.getElementById('custom-build').addEventListener('click', () => {
           window.location.href = "system-builder.php"; 
        });
        });
    </script>
</body>
</html>