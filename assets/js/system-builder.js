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

        async function saveBuild() {
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

        // Send to backend
        const response = await fetch('../api/save-builds.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(buildData)
        });

        const result = await response.json();

        // Reset button
        event.target.innerHTML = originalText;
        event.target.disabled = false;

        if (result.success) {
            alert('✅ ' + result.message + '\n\nBuild ID: #' + result.build_id);
            // Optional: Clear the build after saving
            if (confirm('Would you like to start a new build?')) {
                clearBuild();
            }
        } else {
            alert('❌ Error: ' + result.error);
        }

    } catch (error) {
        console.error('Error saving build:', error);
        alert('❌ Failed to save build. Please try again.');
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

        // Add this JavaScript to your product pages (cpu.php, gpu.php, motherboard.php, etc.)

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
            // Show success message
            alert('✅ ' + productName + ' added to cart!');
            
            // Update cart count in header
            updateCartCount(result.cart_count);
        } else {
            alert('❌ ' + result.error);
        }
    } catch(error) {
        console.error('Error:', error);
        alert('Failed to add to cart. Please try again.');
    }
}

function updateCartCount(count) {
    // Update the cart badge number
    const badge = document.querySelector('.fa-shopping-cart + span');
    if(badge) {
        badge.textContent = count;
        if(count > 0) {
            badge.style.display = 'flex';
        }
    } else if(count > 0) {
        // Create badge if it doesn't exist
        const cartLink = document.querySelector('.fa-shopping-cart').parentElement;
        const newBadge = document.createElement('span');
        newBadge.style.cssText = 'position: absolute; top: -8px; right: -8px; background: #ff4444; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold;';
        newBadge.textContent = count;
        cartLink.appendChild(newBadge);
    }
}

// Example: Add "Add to Cart" button to product cards
// Replace the existing "View" button with this:
/*
<button class="btn btn-add-cart" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>')">
    <i class="fas fa-shopping-cart"></i> Add to Cart
</button>
*/