<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UBUILD - Homepage</title>
    <link rel="stylesheet" href="assets/css/homepage.css">
</head>
<body>
    <header>
        <h1>UBUILD</h1>
        <div class="login-register">
            <?php if(isset($_SESSION['user_id'])): ?>
                <!-- User is logged in -->
                <span style="margin-right: 15px;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="auth/logout.php">Logout</a>
            <?php else: ?>
                <!-- User is not logged in -->
                <a href="auth/login.php">Login</a>
                <a href="auth/register.php">Register</a> 
            <?php endif; ?>
        </div>
    </header>

    <nav class="nav-bar">
        <ul>
            <li><a href="Homepage.php">Home</a></li>
            <li><a href="pages/pre-built.php">Pre-Built</a></li>
            <li><a href="pages/System-builder.php">System Builder</a></li>
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

    <?php
    // Display success/info messages
    if(isset($_GET['success'])) {
        echo '<div style="background: #4CAF50; color: white; padding: 15px; text-align: center;">' . htmlspecialchars($_GET['success']) . '</div>';
    }
    if(isset($_GET['message'])) {
        echo '<div style="background: #2196F3; color: white; padding: 15px; text-align: center;">' . htmlspecialchars($_GET['message']) . '</div>';
    }
    ?>

    <div class="hero-section">
        <div class="hero-content">
            <h1>Build Your Own PC</h1>
            <p>Customize your perfect gaming rig with our intuitive PC building platform. Create a powerful computer tailored exactly to your needs.</p>
            <button id="builder-btn-hero">Build</button>
        </div>
    </div>

    <div class="best-builds-section">
        <div class="builds-text">
            <h1>Our Best Builds</h1>
            <p>Explore cutting-edge builds designed for gamers, creators, and tech enthusiasts.</p>
        </div>
        <div class="img-container">
            <div class="img-1">
                <h2>Apex Gaming Beast</h2>
            </div>
            <div class="img-2">
                <h2>Versatile Power Workhouse</h2>
            </div>
            <div class="img-3">
                <h2>Content Creation PC</h2>
            </div>
        </div>
    </div>

    <div class="testimonials">
        <div class="testimonials-text">
            <h2>What our builders say</h2>
            <p>Real experiences from passionate tech enthusiasts who trust our builds.</p>
        </div>
        <div class="clients-feedback">
            <div class="testimonials-profiles-1">
                <h2>⭐⭐⭐⭐⭐</h2>
                <p>This PC transformed my gaming experience. Smooth, powerful, and built to last.</p>
                <div class="profile-1">
                    <img src="assets/images/Foto Profissional _ Posicionamento _ Musculação.jpeg" alt="">
                    <div class="profile-1-details">
                        <p>Jake Rodriguez</p>
                        <p>Professional Gamer</p>
                    </div>
                </div>
            </div>
            <div class="testimonials-profiles-2">
                <h2>⭐⭐⭐⭐⭐</h2>
                <p>This PC transformed my gaming experience. Smooth, powerful, and built to last.</p>
                <div class="profile-1">
                    <img src="assets/images/pexels-alipazani-2829373.jpg" alt="">
                    <div class="profile-1-details">
                        <p>Jake Rodriguez</p>
                        <p>Professional Gamer</p>
                    </div>
                </div>
            </div>
            <div class="testimonials-profiles-3">
                <h2>⭐⭐⭐⭐⭐</h2>
                <p>This PC transformed my gaming experience. Smooth, powerful, and built to last.</p>
                <div class="profile-1">
                    <img src="assets/images/pexels-habib-hosseini-3650469.jpg" alt="">
                    <div class="profile-1-details">
                        <p>Jake Rodriguez</p>
                        <p>Professional Gamer</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="CTA">
        <h1>Ready to build your dream pc?</h1>
        <p>Start your journey with our intuitive PC builder and bring your vision to life.</p>
        <div class="CTA-Btn">
            <button id="build-btn">Build</button>
            <button id="pre-built-btn">See pre-built options</button>
        </div>
    </div>

    <footer>
        <div class="upper-links">
            <h1>UBUILD</h1>
            <ul>
                <li><a href="homepage.php">Home</a></li>
                <li><a href="pages/pre-built.php">Pre-built</a></li>
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

    <script src="assets/js/homepage.js"></script>
</body>
</html>