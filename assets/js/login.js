
    

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
        });

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