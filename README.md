# E-Commerce Platform - Technical Documentation

## Table of Contents
1. [Installation & Setup](#installation--setup)
2. [Project Structure](#project-structure)
3. [Features Overview](#features-overview)
4. [API Documentation](#api-documentation)
5. [Database Schema](#database-schema)
6. [User Guide](#user-guide)
7. [Development](#development)
8. [Troubleshooting](#troubleshooting)

## Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 16+ and npm
- MySQL 8.0+
- Laragon (or similar development environment)

### Setup Steps

1. **Clone/Navigate to Project**
```bash
git clone https://github.com/gilbertozioma/Laravel-Ecommerce.git
```

```bash
cd c:\laragon\www\Laravel-Ecommerce
```

1. **Install PHP Dependencies**
```bash
composer install
```

1. **Install Node Dependencies**
```bash
npm install
```

1. **Create Environment File**
```bash
copy .env.example .env
```

1. **Generate Application Key**
```bash
php artisan key:generate
```

1. **Configure Database** (in `.env`)
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce
DB_USERNAME=root
DB_PASSWORD=
```

1. **Run Migrations**
```bash
php artisan migrate
```

1. **Seed Database with Test Data**
```bash
php artisan db:seed
```

1. **Build Frontend Assets**
```bash
npm run build
```

1.  **Start Development Server**
```bash
php artisan serve
```

Access the application at `http://localhost:8000`

### Test Credentials

**Admin User:**
- Email: admin@example.com
- Password: password

**Regular User:**
- Email: user@example.com
- Password: password

## Project Structure

```
app/
├── Console/
│   └── Commands/              # Artisan commands
├── Http/
│   ├── Controllers/           # Application controllers
│   │   ├── CartController.php
│   │   ├── ProductController.php
│   │   ├── OrderController.php
│   │   └── AdminController.php
│   ├── Middleware/            # HTTP middleware
│   │   └── TransferGuestCart.php
│   └── Requests/              # Form validation
├── Jobs/
│   └── NotifyLowStock.php     # Background jobs
├── Mail/
│   ├── DailySalesReportMail.php
│   └── LowStockNotificationMail.php
├── Models/                     # Eloquent models
│   ├── User.php
│   ├── Product.php
│   ├── CartItem.php
│   ├── Order.php
│   └── OrderItem.php
├── Services/
│   ├── OrderService.php       # Business logic
│   └── CartService.php
└── Providers/
    └── AppServiceProvider.php

database/
├── migrations/                 # Database migrations
├── seeders/                    # Database seeders
└── factories/                  # Model factories for testing

resources/
├── views/                      # Blade templates
│   ├── layouts/
│   │   └── navigation.blade.php
│   ├── shop.blade.php
│   ├── cart-icon.blade.php
│   ├── cart-page.blade.php
│   ├── checkout-page.blade.php
│   ├── dashboard.blade.php
│   ├── profile.blade.php
│   └── admin/
│       └── dashboard.blade.php
├── js/
│   ├── app.js                 # Main JS entry
│   ├── bootstrap.js           # Bootstrap configuration
│   ├── cart.js                # Cart functionality
│   └── profile.js             # Profile page logic
└── css/
    └── app.css                # Application styles

routes/
├── web.php                     # Web routes
├── auth.php                    # Authentication routes
└── console.php                 # Console commands

public/
├── index.php                   # Application entry point
└── build/                      # Compiled assets

config/
├── app.php                     # Application configuration
├── database.php                # Database configuration
├── auth.php                    # Authentication configuration
└── mail.php                    # Mail configuration

tests/                          # Test suites
├── Feature/
└── Unit/
```

## Features Overview

### 1. **Product Management**
- Browse product catalog with responsive grid layout
- View detailed product information
- Check real-time stock availability
- Add products to cart directly from listing and detail pages

**Key File:** `app/Http/Controllers/ProductController.php`

### 2. **Shopping Cart**
- Add/remove items from cart
- Update item quantities with inventory validation
- View cart with item details and subtotal
- Clear entire cart
- Works for both guests (session) and authenticated users (database)

**Key Files:**
- `app/Http/Controllers/CartController.php`
- `resources/js/cart.js`
- `resources/views/cart-icon.blade.php`

### 3. **Checkout & Orders**
- Secure checkout process
- Order summary with total calculation
- Guest users redirect to login before order placement
- Order confirmation
- Order history in user profile

**Key Files:**
- `app/Http/Controllers/OrderController.php`
- `app/Services/OrderService.php`
- `resources/views/checkout-page.blade.php`

### 4. **User Authentication**
- User registration and login via Laravel Breeze
- Secure password storage with bcrypt hashing
- Profile management page
- Role-based access control (user/admin roles)
- Guest cart transfer to account on login
- Order history for authenticated users

**Key Files:**
- `app/Models/User.php`
- `routes/auth.php`
- `app/Http/Middleware/TransferGuestCart.php`

### 5. **Admin Dashboard**
- View all orders with pagination
- Filter orders by status, date, and user
- Update order status
- View order details with items
- Export orders to CSV

**Key Files:**
- `app/Http/Controllers/AdminController.php`
- `resources/views/admin/dashboard.blade.php`

### 6. **Reactive Cart Badge**
- Real-time cart count display
- Updates without page refresh
- Shows on navbar for quick reference
- Hides when cart is empty

**Key File:** `resources/views/cart-icon.blade.php`

## API Documentation

### Cart Endpoints

#### Get Cart Count
```
GET /cart/count
Response: { "count": 5 }
```
Returns the total number of items in the current user's cart (works for guests and authenticated users).

#### Get Cart Items
```
GET /cart/items
Response: {
  "items": [
    {
      "id": 1,
      "product_id": 5,
      "product_name": "Product Name",
      "product_price": 29.99,
      "product_stock": 10,
      "quantity": 2,
      "total": 59.98
    }
  ],
  "subtotal": 59.98
}
```
Returns all items in the cart with pricing information.

#### Add Item to Cart
```
POST /cart/add
Body: { "product_id": 5 }
Response: { "product": "Product Name" }
```
Adds a product to the cart. Returns 409 if out of stock.

#### Update Item Quantity
```
POST /cart/{id}/quantity
Body: { "quantity": 3 }
Response: { "success": true }
```
Updates the quantity of an item. The `{id}` is the cart item ID.

#### Remove Item from Cart
```
DELETE /cart/{id}
Response: { "success": true }
```
Removes an item from the cart.

#### Clear Cart
```
POST /cart/clear
Response: { "success": true }
```
Removes all items from the cart.

### Order Endpoints

#### Place Order
```
POST /order/place
Response: { 
  "success": true, 
  "order_id": 123,
  "message": "Order placed successfully"
}
```
Creates an order from the current cart. Requires authentication. Automatically clears the cart after order placement.

## Database Schema

### Users Table
```sql
CREATE TABLE users (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255),
  email VARCHAR(255) UNIQUE,
  email_verified_at TIMESTAMP NULL,
  password VARCHAR(255),
  role ENUM('user', 'admin') DEFAULT 'user',
  remember_token VARCHAR(100),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### Products Table
```sql
CREATE TABLE products (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255),
  description TEXT,
  price DECIMAL(10, 2),
  stock_quantity INT DEFAULT 0,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### Cart Items Table
```sql
CREATE TABLE cart_items (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT,
  product_id BIGINT,
  quantity INT DEFAULT 1,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);
```

### Orders Table
```sql
CREATE TABLE orders (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT,
  total_amount DECIMAL(10, 2),
  status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Order Items Table
```sql
CREATE TABLE order_items (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  order_id BIGINT,
  product_id BIGINT,
  quantity INT,
  price DECIMAL(10, 2),
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);
```

## User Guide

### For Customers

#### Browsing Products
1. Visit the Shop page
2. View product grid
3. Click on any product to see details
4. Check stock availability

#### Adding to Cart
1. Click "Add to Cart" button on product
2. See badge update in navbar
3. View cart by clicking cart icon or "Cart" link

#### Shopping Cart
1. Review items and quantities
2. Adjust quantities using input fields
3. Remove items with delete button
4. See subtotal update in real-time

#### Checkout (Guests)
1. Click "Proceed to Checkout"
2. Redirected to login/register page
3. Create account or login with existing credentials
4. Cart items transfer to account automatically
5. Complete order placement

#### Checkout (Authenticated Users)
1. Click "Proceed to Checkout"
2. Review order summary
3. Click "Place Order"
4. Order confirmation page

#### View Order History
1. Go to Profile page (click username dropdown → Profile)
2. Scroll to "Order History" section
3. View table with all your orders (ID, Date, Total, Status)
4. Click "View" button to expand and see items in each order
5. Orders sorted by most recent first
6. Status badge shows order status (pending, processing, completed, cancelled)

### For Admins

#### Accessing Admin Panel
1. Login with admin credentials
2. Click "Admin Dashboard" in dropdown menu
3. View orders and metrics

#### Managing Orders
1. View orders list with pagination
2. Filter by status, date range, or user
3. Click order to view details
4. Update status from dropdown
5. Export to CSV for reporting

## Development

### Building Assets
```bash
# Development build (with watch mode)
npm run dev

# Production build
npm run build
```

### Running Migrations
```bash
# Run all pending migrations
php artisan migrate

# Rollback last batch
php artisan migrate:rollback

# Reset database
php artisan migrate:reset

# Refresh database (reset + migrate)
php artisan migrate:refresh --seed
```

### Seeding Database
```bash
# Seed with all seeders
php artisan db:seed

# Seed with specific seeder
php artisan db:seed --class=ProductSeeder
```

### Creating Models & Migrations
```bash
# Create model with migration
php artisan make:model ModelName -m

# Create controller
php artisan make:controller ControllerName

# Create middleware
php artisan make:middleware MiddlewareName
```

### Useful Artisan Commands
```bash
# Clear cache
php artisan cache:clear

# Clear config cache
php artisan config:cache

# Clear routes cache
php artisan route:cache

# Show all routes
php artisan route:list

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Start tinker (REPL)
php artisan tinker
```

## Troubleshooting

### Cart Badge Not Updating
**Symptom:** Badge shows 0 even with items in cart
**Solution:** 
1. Clear browser cache (Ctrl+Shift+Delete)
2. Check if cart-icon.blade.php is included only once in navigation
3. Verify `/cart/count` endpoint returns correct JSON
4. Check browser console for JavaScript errors

### Login/Logout Issues
**Symptom:** Can't login or logout
**Solution:**
1. Clear Laravel cache: `php artisan cache:clear`
2. Clear sessions: Delete `storage/framework/sessions/*`
3. Check `.env` SESSION_DRIVER is set to file or database

### Database Connection Error
**Symptom:** "SQLSTATE[HY000]" error
**Solution:**
1. Verify MySQL is running
2. Check `.env` database credentials
3. Ensure database exists: `php artisan migrate`
4. Check database user has proper permissions

### Assets Not Loading
**Symptom:** No CSS/JS, page looks broken
**Solution:**
1. Build assets: `npm run build`
2. Clear browser cache
3. Check `public/build/manifest.json` exists
4. Verify APP_URL in `.env` is correct

### Permission Issues on Windows
**Symptom:** "Permission denied" when creating files
**Solution:**
1. Run VS Code as Administrator
2. Or use `sudo` prefix on terminal commands
3. Check file permissions in `storage/` and `bootstrap/cache/`

### Out of Memory Error
**Symptom:** "Fatal error: Allowed memory size exhausted"
**Solution:**
1. Increase PHP memory limit in `.env`:
```
php.ini: memory_limit = 256M
```
2. Or run: `php -d memory_limit=256M artisan command`

