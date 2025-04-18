/CycleHub - Bike Shop Setup Guide/

/Requirements/
PHP 7.4 or higher
MySQL 5.7 or higher
Web server (Apache or Nginx)
Web browser

/Database Setup/
Create a MySQL database using your InfinityFree hosting panel
Import the database schema to create tables for:
users (for account management)
products (for bike inventory)
orders (for customer purchases)
order_items (for items in each order)
Update the database connection details in db_conn.php with your InfinityFree credentials

/Installation/
Download all project files from the repository
Upload all files to your InfinityFree hosting using FTP or the file manager
Maintain the folder structure as provided in the repository
Running the Website
Navigate to your InfinityFree domain in your web browser
The homepage will display with bike categories, featured products, and navigation menu

/Features to Test/

Product Browsing:
View different bike categories from the homepage
Browse all bikes using the "Shop Now" button
Filter products by category

Shopping Cart:
Add products to your cart
View cart contents by clicking the cart icon
Adjust quantities or remove items
Clear cart using the "Clear" button

User Accounts:
Register a new account
Log in and log out
View order history (if implemented)

Checkout Process:
Proceed from cart to checkout
Complete the order form
Receive order confirmation
Troubleshooting

If products don't appear, verify your database connection and product data
For missing images, check that all image files were uploaded correctly
If the cart doesn't work, ensure PHP sessions are enabled on your hosting
For styling issues, verify that the CSS file was uploaded properly