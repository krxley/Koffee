<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Product.php';
require_once '../classes/Cart.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$product = new Product($conn);
$cart = new Cart($conn);

$page = $_GET['page'] ?? 1;
$limit = ITEMS_PER_PAGE;
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
if ($search) {
    $products = $product->search($search, $limit, $offset);
} else {
    $products = $product->getAll($limit, $offset);
}

$cart_count = $cart->getCount($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Koffee</title>
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
        .nav-link {
            font-weight: 600;
            color: #444 !important;
        }
        .nav-link:hover {
            color: var(--primary-green) !important;
        }
        .cart-icon {
            font-size: 1.3rem;
            color: var(--primary-green);
            position: relative;
            cursor: pointer;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -10px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
        }
        .product-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
            background: var(--primary-green);
            color: white;
        }
        .product-card:hover .product-name,
        .product-card:hover .product-price {
            color: white;
        }
        .product-img {
            width: 150px;
            height: 150px;
            object-fit: contain;
            margin-bottom: 15px;
        }
        .product-name {
            font-weight: 700;
            font-size: 1rem;
            color: #333;
            margin-bottom: 10px;
        }
        .product-price {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-green);
            margin-bottom: 15px;
        }
        .btn-add-cart {
            background: var(--primary-green);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-add-cart:hover {
            background: #043a2c;
        }
        .product-card:hover .btn-add-cart {
            background: white;
            color: var(--primary-green);
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
        <a class="navbar-brand" href="#">Koffee</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="shop.php">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="orders.php">My Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="cart-icon" href="cart.php">
                        <i class="bi bi-bag-fill"></i>
                        <?php if ($cart_count > 0): ?>
                            <span class="cart-badge"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../api/auth.php?action=logout">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5">
    <h1 class="page-title">Our Coffee Shop</h1>

    <div class="mb-4">
        <form method="GET" class="d-flex">
            <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary ms-2">Search</button>
        </form>
    </div>

    <div class="row">
        <?php foreach ($products as $p): ?>
            <div class="col-md-4 col-sm-6">
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($p['image_url']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="product-img">
                    <div class="product-name"><?php echo htmlspecialchars($p['name']); ?></div>
                    <div class="product-price">$<?php echo number_format($p['price'], 2); ?></div>
                    <?php if ($p['stock_quantity'] > 0): ?>
                        <form method="POST" action="../api/cart.php?action=add">
                            <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn-add-cart"><i class="bi bi-bag-plus"></i> Add to Cart</button>
                        </form>
                    <?php else: ?>
                        <button class="btn-add-cart" disabled>Out of Stock</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
