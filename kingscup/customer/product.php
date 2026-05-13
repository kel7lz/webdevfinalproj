<?php
// ============================================================
// King's Cup Coffee — Product Detail Page
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
session_boot();

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$product = db_fetch(
    'SELECT p.*, c.name AS cat_name, c.slug AS cat_slug 
     FROM products p 
     JOIN categories c ON c.id = p.category_id 
     WHERE p.id = ? AND p.is_available = 1',
    [$product_id]
);

if (!$product) {
    flash_set('error', 'Product not found.');
    header('Location: menu.php');
    exit;
}

$page_title = h($product['name']) . ' — ' . APP_NAME;

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 700px; margin: 0 auto; padding: 48px 24px;">
    
    <!-- Back Button -->
    <a href="menu.php?cat=<?= h($product['cat_slug']) ?>" 
       style="display: inline-flex; align-items: center; gap: 8px; color: var(--text-light); 
              margin-bottom: 24px; font-weight: 500; text-decoration: none; font-size: 0.95rem;"
       onmouseover="this.style.color='var(--brown-dark)'" 
       onmouseout="this.style.color='var(--text-light)'">
        ← Back to <?= h($product['cat_name']) ?>
    </a>

    <!-- Product Card -->
    <div style="background: var(--white); border-radius: 16px; box-shadow: 0 4px 24px rgba(59,31,14,0.12); overflow: hidden;">
        
        <!-- Product Image -->
        <div style="width: 100%; height: 350px; background: var(--cream-dark); 
                    display: flex; align-items: center; justify-content: center; overflow: hidden;">
            <?php if ($product['image_url']): ?>
                <img src="../assets/<?= h($product['image_url']) ?>" 
                     alt="<?= h($product['name']) ?>" 
                     style="width: 100%; height: 100%; object-fit: cover;"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <span style="display: none; font-size: 8rem;">☕</span>
            <?php else: ?>
                <span style="font-size: 8rem;">☕</span>
            <?php endif; ?>
        </div>

        <!-- Product Details -->
        <div style="padding: 32px;">
            <h1 style="font-family: 'Playfair Display', serif; font-size: 2rem; color: var(--brown-dark); margin-bottom: 8px;">
                <?= h($product['name']) ?>
            </h1>
            
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <span style="font-size: 0.9rem; color: var(--text-light); background: var(--cream); 
                             padding: 4px 12px; border-radius: 12px;">
                    <?= h($product['cat_name']) ?>
                </span>
                <?php if ($product['calories']): ?>
                    <span style="font-size: 0.9rem; color: var(--text-light);">
                        <?= $product['calories'] ?> calories
                    </span>
                <?php endif; ?>
                <?php if ($product['stock'] > 0): ?>
                    <span style="font-size: 0.9rem; color: #17864B; font-weight: 600;">
                        ✓ In Stock
                    </span>
                <?php else: ?>
                    <span style="font-size: 0.9rem; color: #991B1B; font-weight: 600;">
                        ✕ Out of Stock
                    </span>
                <?php endif; ?>
            </div>

            <?php if ($product['description']): ?>
                <p style="color: var(--text-mid); line-height: 1.7; margin-bottom: 24px; font-size: 1rem;">
                    <?= nl2br(h($product['description'])) ?>
                </p>
            <?php endif; ?>

            <!-- Price -->
            <div style="font-size: 2rem; font-weight: 700; color: var(--brown-dark); margin-bottom: 24px; 
                        padding-bottom: 24px; border-bottom: 2px solid var(--border);">
                ₱<?= number_format($product['price'], 2) ?>
            </div>

            <?php if ($product['stock'] > 0): ?>
            <!-- Add to Cart Form -->
            <form id="addToCartForm" onsubmit="handleAddToCart(event)">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                
                <!-- Quantity Selector -->
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                    <span style="font-weight: 600; color: var(--text-dark); font-size: 0.95rem;">Quantity</span>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button type="button" id="qtyMinus" 
                                style="width: 40px; height: 40px; border: 2px solid var(--border); 
                                       background: var(--white); border-radius: 8px; cursor: pointer; 
                                       font-size: 20px; font-weight: 600; color: var(--text-dark);
                                       display: flex; align-items: center; justify-content: center;
                                       transition: all 0.2s;"
                                onmouseover="this.style.background='var(--cream-dark)'" 
                                onmouseout="this.style.background='var(--white)'">
                            −
                        </button>
                        <span id="qtyValue" style="font-weight: 600; min-width: 30px; text-align: center; font-size: 1.1rem;">1</span>
                        <button type="button" id="qtyPlus" 
                                style="width: 40px; height: 40px; border: 2px solid var(--border); 
                                       background: var(--white); border-radius: 8px; cursor: pointer; 
                                       font-size: 20px; font-weight: 600; color: var(--text-dark);
                                       display: flex; align-items: center; justify-content: center;
                                       transition: all 0.2s;"
                                onmouseover="this.style.background='var(--cream-dark)'" 
                                onmouseout="this.style.background='var(--white)'">
                            +
                        </button>
                    </div>
                    <input type="hidden" name="quantity" value="1" id="qtyInput">
                </div>

                <!-- Submit Button -->
                <button type="submit" id="addToCartBtn"
                        style="width: 100%; padding: 16px; background: var(--brown-dark); color: var(--white); 
                               border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; 
                               cursor: pointer; transition: all 0.2s; font-family: 'DM Sans', sans-serif;"
                        onmouseover="this.style.background='var(--brown-mid)'" 
                        onmouseout="this.style.background='var(--brown-dark)'">
                    Add to Cart — ₱<?= number_format($product['price'], 2) ?>
                </button>
                
                <!-- Cart Notification -->
                <div id="cartNotification" style="display: none; margin-top: 16px; padding: 12px 16px; 
                            background: #D4F5E3; color: #17864B; border-radius: 8px; text-align: center; font-weight: 500;">
                </div>
            </form>
            <?php else: ?>
                <div style="background: #FEE2E2; color: #991B1B; padding: 16px; border-radius: 8px; text-align: center; font-weight: 600;">
                    This product is currently out of stock
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// =============================================
// Quantity Stepper
// =============================================
document.getElementById('qtyMinus').addEventListener('click', function() {
    var qty = parseInt(document.getElementById('qtyValue').textContent);
    if (qty > 1) {
        qty--;
        document.getElementById('qtyValue').textContent = qty;
        document.getElementById('qtyInput').value = qty;
    }
});

document.getElementById('qtyPlus').addEventListener('click', function() {
    var qty = parseInt(document.getElementById('qtyValue').textContent);
    if (qty < 99) {
        qty++;
        document.getElementById('qtyValue').textContent = qty;
        document.getElementById('qtyInput').value = qty;
    }
});

// =============================================
// Add to Cart Handler
// =============================================
async function handleAddToCart(event) {
    event.preventDefault();
    
    const form = document.getElementById('addToCartForm');
    const btn = document.getElementById('addToCartBtn');
    const notification = document.getElementById('cartNotification');
    const qty = document.getElementById('qtyValue').textContent;
    const csrfToken = form.querySelector('input[name="csrf_token"]').value;
    const productId = form.querySelector('input[name="product_id"]').value;
    
    // Disable button and show loading
    btn.disabled = true;
    btn.textContent = '⏳ Adding to Cart...';
    btn.style.opacity = '0.7';
    notification.style.display = 'none';
    
    try {
        // Create form data
        const formData = new URLSearchParams();
        formData.append('action', 'add');
        formData.append('product_id', productId);
        formData.append('quantity', qty);
        formData.append('csrf_token', csrfToken);
        
        // Send request
        const response = await fetch('../ajax/cart_action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData.toString()
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Success!
            notification.style.display = 'block';
            notification.style.background = '#D4F5E3';
            notification.style.color = '#17864B';
            notification.innerHTML = '✅ <strong>' + data.cart_count + ' item(s)</strong> in cart! ' +
                                    '<a href="cart.php" style="color: var(--brown-dark); font-weight: 700; text-decoration: underline; margin-left: 8px;">View Cart →</a>';
            
            // Update cart count in navigation
            updateCartBadge(data.cart_count);
            
            // Change button text
            btn.textContent = '✓ Added! Add More?';
            btn.style.background = '#17864B';
            
            // Reset button after 2 seconds
            setTimeout(() => {
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.style.background = 'var(--brown-dark)';
                btn.textContent = 'Add to Cart — ₱<?= number_format($product['price'], 2) ?>';
            }, 2000);
            
        } else {
            // Error
            notification.style.display = 'block';
            notification.style.background = '#FEE2E2';
            notification.style.color = '#991B1B';
            notification.textContent = '❌ ' + (data.error || 'Failed to add item to cart.');
            
            // Reset button
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.textContent = 'Try Again';
        }
    } catch (error) {
        // Network error
        notification.style.display = 'block';
        notification.style.background = '#FEE2E2';
        notification.style.color = '#991B1B';
        notification.textContent = '❌ Network error. Please check your connection and try again.';
        
        // Reset button
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.textContent = 'Add to Cart — ₱<?= number_format($product['price'], 2) ?>';
        
        console.error('Cart error:', error);
    }
}

// =============================================
// Update Cart Badge in Navigation
// =============================================
function updateCartBadge(count) {
    // Find cart link in navigation
    const cartLinks = document.querySelectorAll('a[href="cart.php"]');
    cartLinks.forEach(link => {
        // Check if badge already exists
        let badge = link.querySelector('.cart-badge');
        
        if (count > 0) {
            if (!badge) {
                // Create badge
                badge = document.createElement('span');
                badge.className = 'cart-badge';
                badge.style.cssText = 'background: #C8A96E; color: #3B1F0F; padding: 2px 8px; ' +
                                     'border-radius: 12px; font-size: 12px; font-weight: 700; margin-left: 4px;';
                link.appendChild(badge);
            }
            badge.textContent = count;
        } else if (badge) {
            // Remove badge if cart is empty
            badge.remove();
        }
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>