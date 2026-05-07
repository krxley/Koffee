# Koffee E-Commerce System

A comprehensive e-commerce platform built with PHP, MySQL, CSS, and JavaScript, featuring both admin dashboard and customer shopping interface.

## Features

### Admin Dashboard
- **Dashboard Overview**: View total orders, revenue, products, and users
- **Order Management**: View all orders, monitor status, and track customer orders
- **Product Management**: Add, edit, delete, and manage product inventory
- **User Management**: Monitor all registered customers
- **Revenue Analytics**: Track total revenue and sales by status

### Customer Features
- **Product Browsing**: Browse all available coffee products
- **Search Functionality**: Search products by name or description
- **Shopping Cart**: Add/remove items and update quantities
- **Order Management**: View order history and status
- **User Profile**: Update personal information and password
- **Checkout**: Secure order placement with payment method selection

## Project Structure

```
ecommerce/
├── config/
│   ├── database.php          # Database connection
│   └── config.php            # Application configuration
├── classes/
│   ├── User.php              # User authentication & management
│   ├── Product.php           # Product operations
│   ├── Order.php             # Order management
│   └── Cart.php              # Shopping cart operations
├── api/
│   ├── auth.php              # Authentication endpoints
│   └── cart.php              # Cart operations
├── admin/
│   ├── dashboard.php         # Admin dashboard
│   ├── orders.php            # Order management
│   ├── products.php          # Product management
│   ├── users.php             # User management
│   └── profile.php           # Admin profile
├── customer/
│   ├── shop.php              # Product listing
│   ├── cart.php              # Shopping cart
│   ├── checkout.php          # Order checkout
│   ├── orders.php            # Order history
│   └── profile.php           # Customer profile
├── database/
│   └── schema.sql            # Database schema
├── index.php                 # Login page
├── register.php              # Registration page
└── README.md
```

## Installation

### Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx web server

### Setup Steps

1. **Create Database**
   ```sql
   CREATE DATABASE koffee_ecommerce;
   ```

2. **Import Schema**
   ```bash
   mysql -u root -p koffee_ecommerce < ecommerce/database/schema.sql
   ```

3. **Configure Database**
   - Edit `ecommerce/config/database.php`
   - Update DB_HOST, DB_USER, DB_PASS, and DB_NAME

4. **Set File Permissions**
   ```bash
   chmod -R 755 ecommerce/
   ```

5. **Access the Application**
   - Frontend: `http://localhost/Koffee/ecommerce/`
   - Login with admin or customer credentials

## Default Admin Account

```
Email: admin@koffee.com
Password: admin123
```

*Note: Change these credentials after first login*

## Database Schema

### Tables
- **users**: Stores user information and credentials
- **products**: Coffee products catalog
- **orders**: Customer orders
- **order_items**: Individual items in each order
- **cart**: Temporary shopping cart storage

## Security Features

- Password hashing using bcrypt
- Session-based authentication
- SQL prepared statements to prevent injection
- Input validation and sanitization
- CSRF protection

## API Endpoints

### Authentication
- `POST /api/auth.php?action=login` - User login
- `POST /api/auth.php?action=register` - User registration
- `GET /api/auth.php?action=logout` - User logout

### Cart Operations
- `POST /api/cart.php?action=add` - Add item to cart
- `POST /api/cart.php?action=update` - Update cart quantity
- `POST /api/cart.php?action=remove` - Remove item from cart
- `POST /api/cart.php?action=clear` - Clear entire cart

## Usage Examples

### Customer Flow
1. Register new account on `/register.php`
2. Login with credentials
3. Browse products on `/customer/shop.php`
4. Add items to cart
5. Checkout and place order
6. Track order status in `/customer/orders.php`

### Admin Flow
1. Login with admin credentials
2. View dashboard for overview
3. Manage products in `/admin/products.php`
4. Monitor orders in `/admin/orders.php`
5. View customer list in `/admin/users.php`

## Future Enhancements

- [ ] Payment gateway integration (Stripe, PayPal)
- [ ] Email notifications
- [ ] Product reviews and ratings
- [ ] Inventory alerts
- [ ] Advanced analytics
- [ ] Multi-language support
- [ ] Mobile app integration

## Support

For issues or questions, please contact: support@koffee.com

## License

MIT License - see LICENSE file for details
