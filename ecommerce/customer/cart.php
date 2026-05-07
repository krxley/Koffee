<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Cart.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$cart = new Cart($conn);
$cart_items = $cart->getItems($_SESSION['user_id']);
$cart_total = $cart->getTotal($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Koffee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-green: #064E3B;
            --bg-light: #F9F8F3;
        }
        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
        }
        .navbar {
            background: white;
            border-bottom: 2px solid var(--primary-green);
            padding: 20px 0;
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            color: var(--primary-green) !important;
        }
        .cart-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .item-img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-right: 20px;
        }
        .item-info {
            flex: 1;
        }
        .item-name {
            font-weight: 700;
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 5px;
        }
        .item-price {
            color: var(--primary-green);
            font-weight: 600;
        }
        .item-qty {
            display: flex;
            align-items: center;
            margin: 0 20px;
        }
        .qty-input {
            width: 50px;
            text-align: center;
            border: 1px solid #ddd;
            padding: 5px;
            margin: 0 5px;
        }
        .btn-remove {
            color: #dc3545;
            cursor: pointer;
            font-size: 1.2rem;
        }
        .summary {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .summary-row:last-child {
            border-bottom: none;
            font-weight: 800;
            font-size: 1.3rem;
            color: var(--primary-green);
        }
        .btn-checkout {
            background: var(--primary-green);
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 10px;
            font-weight: 700;
            width: 100%;
            margin-top: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-checkout:hover {
            background: #043a2c;
        }
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-cart-icon {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }
        .page-title {
            color: var(--primary-green);
            font-weight: 800;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="shop.php">Koffee</a>
        <div class="ms-auto">
            <a class="nav-link" href="../api/auth.php?action=logout">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <h1 class="page-title">Shopping Cart</h1>

    <?php if (count($cart_items) > 0): ?>
        <div class="row">
            <div class="col-md-8">
                <div class="cart-card">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-img">
                            <div class="item-info">
                                <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="item-price">$<?php echo number_format($item['price'], 2); ?></div>
                            </div>
                            <div class="item-qty">
                                <form method="POST" action="../api/cart.php?action=update" style="display: flex; align-items: center; gap: 5px;">
                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                    <button type="button" onclick="this.nextElementSibling.value = Math.max(1, parseInt(this.nextElementSibling.value) - 1); this.nextElementSibling.form.submit();">-</button>
                                    <input type="number" name="quantity" class="qty-input" value="<?php echo $item['quantity']; ?>" min="1">
                                    <button type="button" onclick="this.previousElementSibling.value = parseInt(this.previousElementSibling.value) + 1; this.previousElementSibling.form.submit();">+</button>
                                </form>
                            </div>
                            <div style="width: 80px; text-align: right; font-weight: 700;">
                                $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </div>
                            <form method="POST" action="../api/cart.php?action=remove" style="margin-left: 20px;">
                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                <button type="submit" class="btn-remove" title="Remove"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary">
                    <h4 style="color: var(--primary-green); font-weight: 800; margin-bottom: 20px;">Order Summary</h4>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($cart_total, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <div class="summary-row">
                        <span>Total</span>
                        <span>$<?php echo number_format($cart_total, 2); ?></span>
                    </div>
                    <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
                    <a href="shop.php" class="btn btn-outline-primary" style="width: 100%; margin-top: 10px;">Continue Shopping</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-cart">
            <div class="empty-cart-icon"><i class="bi bi-bag"></i></div>
            <h3 style="color: #666; margin-bottom: 20px;">Your cart is empty</h3>
            <a href="shop.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
