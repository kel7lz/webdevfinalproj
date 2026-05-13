# King's Cup Coffee - Backend

Complete coffee shop website with admin panel and customer ordering system.

## Setup Instructions

### 1. Requirements

- XAMPP (Apache + MySQL + PHP 8.0+)
- Web browser

### 2. Installation

1. Copy all files to `C:\xampp\htdocs\kingscup\`
2. Start Apache and MySQL in XAMPP Control Panel
3. Open http://localhost/phpmyadmin
4. Create database: `kingscup_db`
5. Import `database.sql`

### 3. Configuration

Edit `includes/config.php` if needed:

- Database credentials
- APP_URL (should be `http://localhost/kingscup`)

### 4. Access

- **Customer Site**: http://localhost/kingscup/customer/index.php
- **Admin Panel**: http://localhost/kingscup/admin/login.php

### 5. Login Credentials

- **Admin**: username: `admin` / password: `Admin@123`
- **Customer**: username: `customer` / password: `Admin@123`

### 6. Features

- Customer registration and login
- Menu browsing with categories
- Shopping cart
- Order placement and payment
- Order history
- Password reset
- Admin dashboard with stats
- Product management
- Order management
- Stock management
- Customer management
- PayMongo payment integration (test mode)

## Project Structure
