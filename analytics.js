// Google tag (gtag.js) - Base code as suggested by Google
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-FEX1LN11BZ');

// Track the three key events
document.addEventListener('DOMContentLoaded', function() {
    // 1. Track page views (website visits) - already handled by base GA4 code
    trackPageView();
    
    // 2. Track add to cart events
    trackAddToCart();
    
    // 3. Track purchases
    trackPurchases();
});

// 1. Track page views (website visits)
function trackPageView() {
    gtag('event', 'page_view', {
        page_title: document.title,
        page_path: window.location.pathname,
        page_location: window.location.href
    });
    console.log('Page view event sent:', {
        page_title: document.title,
        page_path: window.location.pathname
    });
}

// Helper function to safely parse price strings
function parsePriceString(priceString) {
    if (!priceString) return 0;
    
    // Remove currency symbol, commas, and any other non-numeric characters except decimal point
    const cleanedPrice = priceString.replace(/[^\d.]/g, '');
    return parseFloat(cleanedPrice) || 0;
}

// 2. Track add to cart events
function trackAddToCart() {
    // Find all add to cart buttons on the page
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            // Prevent default if needed
            // event.preventDefault();
            
            const productId = this.getAttribute('data-product-id') || '';
            const productName = this.getAttribute('data-product-name') || 'Unknown Product';
            
            // Find price and quantity
            let price = 0;
            let quantity = 1;
            
            // Try to find price near the button
            const priceElement = this.closest('.product-details')?.querySelector('.price');
            if (priceElement) {
                price = parsePriceString(priceElement.textContent);
            }
            
            // Try to find quantity input
            const quantityInput = document.getElementById(`quantity-list-${productId}`);
            if (quantityInput) {
                quantity = parseInt(quantityInput.value) || 1; // Default to 1 if parsing fails
            }
            
            // Ensure we have valid data before sending
            if (productId && price > 0) {
                // Send add_to_cart event
                gtag('event', 'add_to_cart', {
                    currency: 'HKD',
                    value: price * quantity,
                    items: [{
                        item_id: productId,
                        item_name: productName,
                        quantity: quantity,
                        price: price
                    }]
                });
                
                // Log for debugging
                console.log('Add to cart event sent:', {
                    product_id: productId,
                    product_name: productName,
                    price: price,
                    quantity: quantity,
                    total_value: price * quantity
                });
            } else {
                console.warn('Add to cart event not sent - missing data:', {
                    product_id: productId,
                    price: price
                });
            }
        });
    });
}

// 3. Track purchases
function trackPurchases() {
    // Check if we're on the checkout success page
    if (window.location.pathname.includes('checkout.php') &&
        window.location.search.includes('success=true')) {
        
        // Get order details from the page
        const orderTotalElement = document.querySelector('.preview-totals .total-row:last-child span:last-child');
        let orderTotal = 0;
        
        if (orderTotalElement) {
            orderTotal = parsePriceString(orderTotalElement.textContent);
        }
        
        const orderIdElement = document.querySelector('.order-id');
        const orderId = orderIdElement ? orderIdElement.textContent.trim() : 'ORDER-' + Date.now();
        
        // Get purchased items
        const purchasedItems = [];
        document.querySelectorAll('.preview-item').forEach(item => {
            try {
                const itemText = item.textContent.trim();
                // Use proper string splitting based on newlines
                const lines = itemText.split('\n').filter(line => line.trim() !== '');
                
                if (lines.length >= 2) {
                    const itemName = lines[0].trim();
                    const priceQuantityText = lines[1].trim();
                    
                    // Handle price and quantity extraction
                    let price = 0;
                    let quantity = 1;
                    
                    if (priceQuantityText.includes('×')) {
                        const parts = priceQuantityText.split('×');
                        if (parts.length >= 2) {
                            // Extract price (remove currency symbol and commas)
                            price = parsePriceString(parts[0].trim());
                            // Extract quantity
                            quantity = parseInt(parts[1].trim()) || 1;
                        }
                    } else {
                        // If no quantity indicator, try to parse the whole string as price
                        price = parsePriceString(priceQuantityText);
                    }
                    
                    // Only add items with valid price
                    if (price > 0) {
                        purchasedItems.push({
                            item_id: 'ITEM-' + Date.now() + '-' + purchasedItems.length, // Generate an ID if none available
                            item_name: itemName,
                            price: price,
                            quantity: quantity
                        });
                    }
                }
            } catch (error) {
                console.error('Error parsing purchase item:', error);
            }
        });
        
        // Only send purchase event if we have a valid order total and at least one item
        if (orderTotal > 0 && purchasedItems.length > 0) {
            gtag('event', 'purchase', {
                transaction_id: orderId,
                value: orderTotal,
                currency: 'HKD',
                tax: 0, // Add tax if available
                shipping: 0, // Add shipping if available
                items: purchasedItems
            });
            
            // Log for debugging
            console.log('Purchase event sent:', {
                transaction_id: orderId,
                value: orderTotal,
                items_count: purchasedItems.length,
                items: purchasedItems
            });
        } else {
            console.warn('Purchase event not sent - invalid data:', {
                orderTotal: orderTotal,
                itemsCount: purchasedItems.length
            });
        }
    }
}

// Add a global error handler to prevent analytics errors from breaking the site
window.addEventListener('error', function(event) {
    if (event.filename && event.filename.includes('analytics.js')) {
        console.error('Analytics error:', event.message);
        event.preventDefault();
    }
});