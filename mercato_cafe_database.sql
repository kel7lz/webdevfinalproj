CREATE DATABASE IF NOT EXISTS mercato_cafe;
USE mercato_cafe;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(60) NOT NULL,
  email VARCHAR(120) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(10) NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_email (email),
  UNIQUE KEY uq_username (username)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(80) NOT NULL,
  sort_order TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_slug (slug)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  image_url VARCHAR(255) DEFAULT NULL,
  calories INT DEFAULT NULL,
  is_available TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_category (category_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  order_type VARCHAR(20) NOT NULL DEFAULT 'dine-in',
  status VARCHAR(20) NOT NULL DEFAULT 'pending',
  total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  notes TEXT,
  placed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_user (user_id),
  KEY idx_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  unit_price DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_order (order_id),
  KEY idx_product (product_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payments (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id INT UNSIGNED NOT NULL,
  paymongo_intent_id VARCHAR(120) DEFAULT NULL,
  method VARCHAR(40) DEFAULT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'pending',
  amount DECIMAL(10,2) NOT NULL,
  paid_at TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_payment_order (order_id)
) ENGINE=InnoDB;

INSERT INTO categories (name, slug, sort_order) VALUES
('Espresso Blends', 'espresso-blends', 1),
('No-Coffee Blends', 'no-coffee-blends', 2),
('Fresh Pastries', 'fresh-pastries', 3),
('Cold Drinks', 'cold-drinks', 4);

INSERT INTO products (category_id, name, description, price, calories, image_url) VALUES
(1, 'Spanish Latte', 'Rich and smooth coffee blended with sweetened condensed milk.', 130.00, 210, 'images/spanish-latte.jpg'),
(1, 'Americano', 'Classic bold espresso diluted with hot water.', 90.00, 15, 'images/americano.jpg'),
(1, 'Cappuccino', 'Equal parts espresso, steamed milk, and thick milk foam.', 120.00, 180, 'images/cappuccino.jpg'),
(1, 'Mocha Latte', 'Espresso with chocolate syrup and steamed milk.', 140.00, 280, 'images/mocha-latte.jpg'),
(2, 'Iced Strawberry Matcha', 'Strawberry meets earthy matcha over ice.', 135.00, 160, 'images/strawberry-matcha.jpg'),
(2, 'Hibiscus Berry Iced Tea', 'Hibiscus tea blended with mixed berries.', 110.00, 80, 'images/hibiscus-tea.jpg'),
(2, 'Matcha Latte', 'Premium matcha with oat milk.', 125.00, 140, 'images/matcha-latte.jpg'),
(2, 'Chai Latte', 'Spiced chai tea with steamed milk and honey.', 115.00, 170, 'images/chai-latte.jpg'),
(3, 'Butter Croissant', 'Flaky, golden croissant baked fresh.', 90.00, 310, 'images/butter-croissant.jpg'),
(3, 'Blueberry Streusel Muffin', 'Muffin loaded with blueberries.', 95.00, 380, 'images/blueberry-muffin.jpg'),
(3, 'Pain au Chocolat', 'Flaky pastry with dark chocolate.', 135.00, 350, 'images/pain-au-chocolat.jpg'),
(3, 'Cinnamon Roll', 'Gooey cinnamon roll with cream cheese frosting.', 110.00, 420, 'images/cinnamon-roll.jpg'),
(4, 'Cold Brew', 'Steeped 18 hours for smooth cold coffee.', 120.00, 10, 'images/cold-brew.jpg'),
(4, 'Strawberry Lemonade', 'Fresh lemonade with strawberry puree.', 95.00, 130, 'images/strawberry-lemonade.jpg'),
(4, 'Iced Caramel Macchiato', 'Vanilla, cold milk, espresso, caramel drizzle.', 145.00, 250, 'images/caramel-macchiato.jpg'),
(4, 'Mango Smoothie', 'Mango blended with yogurt and coconut milk.', 130.00, 220, 'images/mango-smoothie.jpg');

INSERT INTO users (username, email, password_hash, role) VALUES
('admin', 'admin@mercatocafe.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');