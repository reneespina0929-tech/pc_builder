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

// Protect this page - require login
if(!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php?error=Please login to access the System Builder");
    exit();
}

// Fetch all products from database
try {
    $stmt = $pdo->query("SELECT * FROM products WHERE is_active = 1 ORDER BY category, name");
    $allProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group products by category
    $productsByCategory = [];
    foreach($allProducts as $product) {
        $cat = $product['category'];
        if(!isset($productsByCategory[$cat])) {
            $productsByCategory[$cat] = [];
        }
        $productsByCategory[$cat][] = $product;
    }
    
    // DEBUG - Remove this later
    echo "<!-- DEBUG: Found " . count($allProducts) . " products -->";
    echo "<!-- DEBUG: Categories: " . implode(", ", array_keys($productsByCategory)) . " -->";
    
} catch(PDOException $e) {
    $productsByCategory = [];
    echo "<!-- DEBUG ERROR: " . $e->getMessage() . " -->";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UBUILD - System Builder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/system-builder.css">
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

    <div class="builder-container">
        <div class="builder-header">
            <h1>Build Your Dream PC</h1>
            <p>Select components to create your perfect custom build</p>
        </div>

        <div class="builder-content" id="builderContent">
            <!-- Components will be generated here -->
        </div>

        <div class="build-summary">
            <h2>Build Summary</h2>
            <div class="summary-grid">
                <div class="summary-card">
                    <h3>Total Components</h3>
                    <p id="totalComponents">0 / 9</p>
                </div>
                <div class="summary-card">
                    <h3>Estimated Total</h3>
                    <p id="totalPrice">₱0.00</p>
                </div>
                <div class="summary-card">
                    <h3>Estimated Wattage</h3>
                    <p id="totalWattage">0W</p>
                </div>
            </div>
            <div class="action-buttons">
                <button class="btn btn-primary" onclick="saveBuild(event)">
                    <i class="fas fa-save"></i> Save Build
                </button>
                <button class="btn btn-secondary" onclick="clearBuild()">
                    <i class="fas fa-trash"></i> Clear All
                </button>
            </div>
        </div>
    </div>

    <div class="modal" id="componentModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Select Component</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="product-grid" id="productGrid"></div>
            <div style="margin-top: 20px; text-align: right;">
                <button class="btn btn-add" onclick="addSelectedComponent()">Add to Build</button>
            </div>
        </div>
    </div>

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

    <script>
        // Pass PHP products to JavaScript
        const productDatabase = <?php echo json_encode($productsByCategory); ?>;

        const components = [
            { id: 'cpu', name: 'Processor (CPU)', icon: 'fa-microchip', selected: null },
            { id: 'motherboard', name: 'Motherboard', icon: 'fa-memory', selected: null },
            { id: 'cooler', name: 'CPU Cooler', icon: 'fa-fan', selected: null },
            { id: 'ram', name: 'Memory (RAM)', icon: 'fa-server', selected: null },
            { id: 'gpu', name: 'Graphics Card (GPU)', icon: 'fa-desktop', selected: null },
            { id: 'storage', name: 'Storage', icon: 'fa-hard-drive', selected: null },
            { id: 'psu', name: 'Power Supply', icon: 'fa-plug', selected: null },
            { id: 'case', name: 'PC Case', icon: 'fa-box', selected: null },
            { id: 'monitor', name: 'Monitor', icon: 'fa-tv', selected: null }
        ];

        let currentComponent = null;
        let selectedProduct = null;

        function initBuilder() {
            renderComponents();
            updateSummary();
        }

        function renderComponents() {
            const container = document.getElementById('builderContent');
            container.innerHTML = '';

            components.forEach(component => {
                const row = createComponentRow(component);
                container.appendChild(row);
            });
        }

        function createComponentRow(component) {
            const row = document.createElement('div');
            row.className = 'component-row';
            
            row.innerHTML = `
                <div class="component-icon">
                    <i class="fas ${component.icon}"></i>
                </div>
                <div class="component-info">
                    <div class="component-name">${component.name}</div>
                    <div class="component-status">${component.selected ? 'Selected' : 'Not selected'}</div>
                </div>
                <div class="component-selected">
                    ${component.selected ? `
                        <div class="selected-item">
                            <div class="selected-item-info">
                                <div class="selected-item-name">${component.selected.name}</div>
                                <div>${component.selected.specs || ''}</div>
                                <div class="selected-item-price">₱${parseFloat(component.selected.price).toLocaleString()}</div>
                            </div>
                        </div>
                    ` : `
                        <div class="no-selection">No component selected</div>
                    `}
                </div>
                <div class="component-actions">
                    ${component.selected ? `
                        <button class="btn btn-change" onclick="openModal('${component.id}')">
                            <i class="fas fa-sync-alt"></i> Change
                        </button>
                        <button class="btn btn-remove" onclick="removeComponent('${component.id}')">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    ` : `
                        <button class="btn btn-add" onclick="openModal('${component.id}')">
                            <i class="fas fa-plus"></i> Add Component
                        </button>
                    `}
                </div>
            `;

            return row;
        }

        function openModal(componentId) {
            currentComponent = componentId;
            const component = components.find(c => c.id === componentId);
            
            document.getElementById('modalTitle').textContent = `Select ${component.name}`;
            
            const products = productDatabase[componentId] || [];
            const grid = document.getElementById('productGrid');
            grid.innerHTML = '';

            if(products.length === 0) {
                grid.innerHTML = '<p style="text-align: center; padding: 40px; color: #999;">No products available. <a href="admin-products.php">Add products</a></p>';
            } else {
                products.forEach(product => {
                    const card = document.createElement('div');
                    card.className = 'product-card';
                    card.onclick = () => selectProduct(product);
                    
                card.innerHTML = `
                    <h4>${product.name}</h4>
                    <p>${product.specs || 'No specs'}</p>
                    <p>Power: ${product.wattage}W | Stock: ${product.stock}</p>
                    <div class="product-price">₱${parseFloat(product.price).toLocaleString()}</div>
                    <button class="btn btn-add-cart" onclick="addToCart(${product.id}, '${product.name}'); event.stopPropagation();">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                `;
                    
                    grid.appendChild(card);
                });
            }

            document.getElementById('componentModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('componentModal').classList.remove('active');
            selectedProduct = null;
        }

        function selectProduct(product) {
            selectedProduct = product;
            document.querySelectorAll('.product-card').forEach(card => {
                card.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
        }

        function addSelectedComponent() {
            if (!selectedProduct) {
                alert('Please select a component first!');
                return;
            }

            const component = components.find(c => c.id === currentComponent);
            component.selected = selectedProduct;

            closeModal();
            renderComponents();
            updateSummary();
        }

        function removeComponent(componentId) {
            const component = components.find(c => c.id === componentId);
            component.selected = null;
            renderComponents();
            updateSummary();
        }

        function updateSummary() {
            const selectedCount = components.filter(c => c.selected).length;
            const totalPrice = components.reduce((sum, c) => sum + (c.selected ? parseFloat(c.selected.price) : 0), 0);
            const totalWattage = components.reduce((sum, c) => sum + (c.selected ? parseInt(c.selected.wattage) : 0), 0);

            document.getElementById('totalComponents').textContent = `${selectedCount} / ${components.length}`;
            document.getElementById('totalPrice').textContent = `₱${totalPrice.toLocaleString()}`;
            document.getElementById('totalWattage').textContent = `${totalWattage}W`;
        }

async function saveBuild(event) {
    const selectedComponents = components.filter(c => c.selected);
    
    if (selectedComponents.length === 0) {
        alert('Please add at least one component before saving!');
        return;
    }

    // Ask for build name
    const buildName = prompt('Enter a name for your build:', 'My Gaming PC');
    if (!buildName) return; // User cancelled

    // Prepare data
    const buildData = {
        build_name: buildName,
        components: selectedComponents.map(c => ({
            type: c.id,
            id: parseInt(c.selected.id),
            name: c.selected.name,
            specs: c.selected.specs || '',
            price: parseFloat(c.selected.price),
            wattage: parseInt(c.selected.wattage)
        })),
        total_price: components.reduce((sum, c) => sum + (c.selected ? parseFloat(c.selected.price) : 0), 0),
        total_wattage: components.reduce((sum, c) => sum + (c.selected ? parseInt(c.selected.wattage) : 0), 0)
    };

    try {
        // Show loading
        const originalText = event.target.innerHTML;
        event.target.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        event.target.disabled = true;

        console.log('Sending data:', buildData); // Debug log

        // Send to backend
        const response = await fetch('../api/save-builds.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(buildData)
        });

        console.log('Response status:', response.status); // Debug log

        // Check if response is OK
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        console.log('Result:', result); // Debug log

        // Reset button
        event.target.innerHTML = originalText;
        event.target.disabled = false;

        if (result.success) {
            alert('✅ ' + result.message + '\n\nBuild ID: #' + result.build_id + '\nBuild Name: ' + result.build_name);
            
            // Optional: Clear the build after saving
            if (confirm('Would you like to start a new build?')) {
                clearBuild();
            }
        } else {
            // Show detailed error
            let errorMsg = '❌ Error: ' + result.error;
            if (result.details) {
                errorMsg += '\n\nDetails: ' + result.details;
            }
            alert(errorMsg);
        }

    } catch (error) {
        console.error('Error saving build:', error);
        alert('❌ Failed to save build. Please check:\n\n1. Are you logged in?\n2. Is the server running?\n3. Check browser console for details\n\nError: ' + error.message);
        
        // Reset button
        event.target.innerHTML = originalText;
        event.target.disabled = false;
    }
}

        function clearBuild() {
            if (!confirm('Are you sure you want to clear all components?')) {
                return;
            }
            components.forEach(c => c.selected = null);
            renderComponents();
            updateSummary();
        }

        // Dropdown
        const componentsLink = document.getElementById('componentsLink');
        const componentsDropdown = document.getElementById('componentsDropdown');

        componentsLink.addEventListener('click', function(e) {
            e.preventDefault();
            componentsDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function(e) {
            if (!componentsLink.contains(e.target) && !componentsDropdown.contains(e.target)) {
                componentsDropdown.classList.remove('show');
            }
        });

        window.onload = initBuilder;

        // ADD THESE FUNCTIONS TO THE <script> SECTION OF system-builder.php

// Add to Cart function
async function addToCart(productId, productName) {
    try {
        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('product_id', productId);
        formData.append('quantity', 1);
        
        const response = await fetch('../api/cart-actions.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if(result.success) {
            alert('✅ ' + productName + ' added to cart!');
            updateCartCount(result.cart_count);
        } else {
            alert('❌ ' + result.error);
        }
    } catch(error) {
        console.error('Error:', error);
        alert('Failed to add to cart. Please try again.');
    }
}

// Update cart count badge
function updateCartCount(count) {
    const badge = document.querySelector('.fa-shopping-cart + span');
    if(badge) {
        badge.textContent = count;
        if(count > 0) {
            badge.style.display = 'flex';
        }
    } else if(count > 0) {
        const cartLink = document.querySelector('.fa-shopping-cart').parentElement;
        const newBadge = document.createElement('span');
        newBadge.style.cssText = 'position: absolute; top: -8px; right: -8px; background: #ff4444; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold;';
        newBadge.textContent = count;
        cartLink.appendChild(newBadge);
    }
}

// UPDATE THE openModal FUNCTION to include "Add to Cart" button:
function openModal(componentId) {
    currentComponent = componentId;
    const component = components.find(c => c.id === componentId);
    
    document.getElementById('modalTitle').textContent = `Select ${component.name}`;
    
    const products = productDatabase[componentId] || [];
    const grid = document.getElementById('productGrid');
    grid.innerHTML = '';

    if(products.length === 0) {
        grid.innerHTML = '<p style="text-align: center; padding: 40px; color: #999;">No products available.</p>';
    } else {
        products.forEach(product => {
            const card = document.createElement('div');
            card.className = 'product-card';
            card.onclick = () => selectProduct(product);
            
            card.innerHTML = `
                <h4>${product.name}</h4>
                <p>${product.specs || 'No specs'}</p>
                <p>Power: ${product.wattage}W | Stock: ${product.stock}</p>
                <div class="product-price">₱${parseFloat(product.price).toLocaleString()}</div>
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button class="btn btn-add" style="flex: 1;" onclick="selectProductAndAdd(${product.id}); event.stopPropagation();">
                        <i class="fas fa-check"></i> Add to Build
                    </button>
                    <button class="btn" style="background: #28a745; color: white;" onclick="addToCart(${product.id}, '${product.name.replace(/'/g, "\\'")}'); event.stopPropagation();">
                        <i class="fas fa-shopping-cart"></i>
                    </button>
                </div>
            `;
            
            grid.appendChild(card);
        });
    }

    document.getElementById('componentModal').classList.add('active');
}

// Helper function to select product
function selectProductAndAdd(productId) {
    const products = productDatabase[currentComponent] || [];
    const product = products.find(p => p.id == productId);
    if(product) {
        selectProduct(product);
    }
}
    </script>
</body>
</html>