-- ============================================================
-- King's Cup Coffee - Complete Database Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS kingscup_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE kingscup_db;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(60) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_token_expires DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(500) DEFAULT NULL,
    calories INT DEFAULT NULL,
    stock INT DEFAULT 0,
    status ENUM('In Stock', 'Low Stock', 'Out of Stock') DEFAULT 'In Stock',
    is_available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    customer_name VARCHAR(150) DEFAULT NULL,
    mobile VARCHAR(20) DEFAULT NULL,
    items TEXT DEFAULT NULL,
    order_type ENUM('dine-in', 'takeout', 'delivery') DEFAULT 'dine-in',
    status VARCHAR(50) DEFAULT 'Pending',
    payment_method VARCHAR(50) DEFAULT NULL,
    payment_status VARCHAR(50) DEFAULT 'Unpaid',
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT DEFAULT NULL,
    product_name VARCHAR(150) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Payments table
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    paymongo_intent_id VARCHAR(255) DEFAULT NULL,
    paymongo_client_key VARCHAR(255) DEFAULT NULL,
    method VARCHAR(50) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    amount DECIMAL(10,2) NOT NULL,
    paid_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert default admin (password: Admin@123)
INSERT INTO users (username, email, password_hash, role) VALUES
('admin', 'admin@kingscup.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample customer
INSERT INTO users (username, email, password_hash, role) VALUES
('customer', 'customer@kingscup.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer');

-- Insert categories
INSERT INTO categories (name, slug, description, sort_order) VALUES
('Espresso Blends', 'espresso-blends', 'Rich and smooth coffee made from carefully selected beans.', 1),
('Fresh Pastries', 'fresh-pastries', 'Freshly baked sweet and savory treats.', 2),
('Cold Drinks', 'cold-drinks', 'Refreshing beverages perfect for a cool pick-me-up.', 3),
('No-Coffee Blends', 'no-coffee-blends', 'Vibrant, fruit-forward blends with no coffee.', 4);

-- Insert products
INSERT INTO products (category_id, name, description, price, calories, stock, status) VALUES
(1, 'Royal Espresso', 'A bold and intense single shot of our finest espresso, brewed to perfection for the true coffee connoisseur.', 95.00, 5, 50, 'In Stock'),
(1, 'King''s Latte', 'Smooth espresso with steamed milk and a crown of velvety foam. The perfect balance of bold and creamy.', 130.00, 120, 40, 'In Stock'),
(1, 'Crown Cappuccino', 'Equal parts espresso, steamed milk, and silky foam. A classic Italian masterpiece.', 125.00, 100, 35, 'In Stock'),
(1, 'Mocha Royale', 'Rich espresso blended with premium chocolate, steamed milk, and topped with whipped cream.', 150.00, 250, 30, 'In Stock'),
(1, 'Caramel Macchiato', 'Freshly steamed milk with vanilla syrup, marked with espresso and finished with caramel drizzle.', 145.00, 200, 25, 'In Stock'),
(2, 'Butter Croissant', 'Flaky, buttery French croissant baked fresh daily. Golden brown and perfectly layered.', 85.00, 230, 25, 'In Stock'),
(2, 'Chocolate Danish', 'Sweet Danish pastry filled with rich Belgian chocolate, baked until golden.', 95.00, 280, 20, 'In Stock'),
(2, 'Blueberry Muffin', 'Moist muffin packed with fresh blueberries and topped with streusel crumble.', 75.00, 210, 15, 'Low Stock'),
(2, 'Cinnamon Roll', 'Soft, fluffy roll swirled with cinnamon sugar and topped with cream cheese frosting.', 90.00, 320, 18, 'In Stock'),
(3, 'Iced Royal Coffee', 'Our signature coffee chilled and served over ice with a touch of sweetness.', 120.00, 80, 45, 'In Stock'),
(3, 'King''s Frappe', 'Blended coffee with ice, milk, and caramel drizzle. A refreshing royal treat.', 160.00, 320, 35, 'In Stock'),
(3, 'Iced Matcha Latte', 'Premium Japanese matcha whisked with cold milk over ice. Earthy and refreshing.', 140.00, 150, 30, 'In Stock'),
(4, 'Matcha Majesty', 'Premium Japanese matcha whisked to perfection with steamed milk. A zen experience.', 140.00, 60, 30, 'In Stock'),
(4, 'Royal Hot Chocolate', 'Rich Belgian hot chocolate topped with marshmallows and a drizzle of chocolate syrup.', 110.00, 290, 25, 'In Stock'),
(4, 'Chai Tea Latte', 'Spiced chai tea concentrate with steamed milk. Warm, aromatic, and comforting.', 125.00, 180, 20, 'In Stock');