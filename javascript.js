document.addEventListener('DOMContentLoaded', function() {
  // Price range slider functionality
  const priceRange = document.getElementById('price-range');
  const priceLabel = document.querySelector('.price-label');
 
  // Initialize price label with the default value
  if (priceRange && priceLabel) {
      priceLabel.textContent = `${priceRange.value}`;
      
      priceRange.addEventListener('input', () => {
          priceLabel.textContent = `${priceRange.value}`;
      });
  }
 
  // Notification function
  window.showNotification = function(message, type) {
      const notification = document.getElementById('notification');
      
      notification.innerHTML = `
          <div class="notification-popup ${type}">
              <div class="notification-content">
                  <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                  <p>${message}</p>
                  <button onclick="closeNotification()"><i class="fas fa-times"></i></button>
              </div>
          </div>
      `;
      
      notification.style.display = 'block';
      
      // Auto-hide after 5 seconds
      setTimeout(() => {
          closeNotification();
      }, 5000);
  }

  window.closeNotification = function() {
      const notification = document.getElementById('notification');
      notification.style.display = 'none';
  } 
  // Add to cart buttons
  const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
 
  addToCartButtons.forEach(button => {
      button.addEventListener('click', function() {
          const productId = this.getAttribute('data-product-id');
          const productName = this.getAttribute('data-product-name');
          
          // Find the quantity input - improved selector logic
          let quantityInput;
          if (document.getElementById(`quantity-${productId}`)) {
              quantityInput = document.getElementById(`quantity-${productId}`);
          } else if (document.getElementById('quantity-detail')) {
              // For product detail page
              quantityInput = document.getElementById('quantity-detail');
          } else {
              quantityInput = document.getElementById(`quantity-list-${productId}`);
          }
          
          // If still no quantity input found, show error
          if (!quantityInput) {
              showNotification('Quantity input not found', 'error');
              return;
          }
          
          const quantity = parseInt(quantityInput.value);
          
          if (isNaN(quantity) || quantity < 1) {
              showNotification('Please enter a valid quantity', 'error');
              return;
          }
          
          // Add to cart via fetch API - using add_to_cart.php instead of index.php
          fetch('add_to_cart.php', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: `product_id=${productId}&quantity=${quantity}`
          })
          .then(response => {
              // Check if response is ok before trying to parse JSON
              if (!response.ok) {
                  throw new Error('Network response was not ok');
              }
              return response.text().then(text => {
                  try {
                      return JSON.parse(text);
                  } catch (e) {
                      console.error('Error parsing JSON:', text);
                      throw new Error('Invalid JSON response');
                  }
              });
          })
          .then(data => {
              if (data.success) {
                  showNotification(`${productName} added to cart`, 'success');
                  
                  // Update cart count in the header
                  const cartCountElement = document.getElementById('cart-count');
                  if (cartCountElement) {
                      cartCountElement.textContent = data.cart_count;
                  }
                  
                  // Reset quantity to 1
                  quantityInput.value = 1;
              } else {
                  showNotification(data.message || 'Error adding to cart', 'error');
              }
          })
          .catch(error => {
              console.error('Error:', error);
              showNotification('An error occurred while adding to cart', 'error');
          });
      });
  });

  // Rest of the code remains the same
  // CHECKOUT PAGE FUNCTIONALITY
  // Payment method selection highlighting
  const paymentMethods = document.querySelectorAll('.payment-method input[type="radio"]');
  if (paymentMethods) {
      paymentMethods.forEach(method => {
          // Set initial active state
          if (method.checked) {
              method.closest('.payment-method').classList.add('active');
          }
          
          // Handle payment method selection
          method.addEventListener('change', function() {
              // Remove active class from all payment methods
              document.querySelectorAll('.payment-method').forEach(el => {
                  el.classList.remove('active');
              });
              
              // Add active class to selected payment method
              if (this.checked) {
                  this.closest('.payment-method').classList.add('active');
              }
          });
      });
  }
 
  // Form validation for checkout
  const checkoutForm = document.querySelector('form[action="checkout.php"]');
  if (checkoutForm) {
      checkoutForm.addEventListener('submit', function(e) {
          let isValid = true;
          const requiredFields = checkoutForm.querySelectorAll('[required]');
          
          // Reset error states
          requiredFields.forEach(field => {
              field.classList.remove('error');
          });
          
          // Check each required field
          requiredFields.forEach(field => {
              if (!field.value.trim()) {
                  field.classList.add('error');
                  isValid = false;
              }
          });
          
          // Email validation
          const emailField = document.getElementById('email');
          if (emailField && emailField.value.trim()) {
              const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
              if (!emailPattern.test(emailField.value.trim())) {
                  emailField.classList.add('error');
                  isValid = false;
              }
          }
          
          if (!isValid) {
              e.preventDefault();
              // Show notification for form errors
              showNotification('Please fill in all required fields correctly', 'error');
              
              // Scroll to the first error field
              const firstError = checkoutForm.querySelector('.error');
              if (firstError) {
                  firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                  firstError.focus();
              }
          }
      });
  }
 
  // Responsive behavior for checkout layout
  function adjustCheckoutLayout() {
      const checkoutContainer = document.querySelector('.checkout-container');
      if (checkoutContainer) {
          if (window.innerWidth < 768) {
              checkoutContainer.classList.add('mobile-layout');
          } else {
              checkoutContainer.classList.remove('mobile-layout');
          }
      }
  }
 
  // Initial call and window resize listener for checkout layout
  adjustCheckoutLayout();
  window.addEventListener('resize', adjustCheckoutLayout);
});