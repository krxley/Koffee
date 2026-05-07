<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Order.php';
require_once '../classes/Product.php';
require_once '../classes/User.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$order = new Order($conn);
$product = new Product($conn);
$user = new User($conn);

$total_orders = $order->getTotalCount();
$total_revenue = $order->getTotalRevenue();
$total_products = $product->getTotalCount();
$total_users = $user->getTotalUsers();
$recent_orders = $order->getAll(5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Koffee</title>
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
        .sidebar {
            background: var(--primary-green);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            padding: 20px 0;
            color: white;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 20px;
            font-size: 1.8rem;
            font-weight: 800;
            border-bottom: 2px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-nav li {
            margin: 0;
        }
        .sidebar-nav a {
            display: block;
            padding: 15px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            padding-left: 30px;
        }
        .main-content {
            margin-left: 250px;
            padding: 40px;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }
        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-green);
        }
        .stat-label {
            color: #666;
            margin-top: 10px;
            font-weight: 600;
        }
        .stat-icon {
            font-size: 3rem;
            color: var(--primary-green);
            opacity: 0.2;
            position: absolute;
            right: 20px;
            top: 20px;
        }
        .recent-orders {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            border: none;
            background: var(--bg-light);
            color: var(--primary-green);
            font-weight: 700;
            padding: 15px;
        }
        .table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .badge-pending {
            background: #ffc107;
            color: #333;
        }
        .badge-completed {
            background: #28a745;
            color: white;
        }
        .badge-processing {
            background: #007bff;
            color: white;
        }
        .top-nav {
            background: white;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-brand">Koffee Admin</div>
    <ul class="sidebar-nav">
        <li><a href="dashboard.php" class="active"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="orders.php"><i class="bi bi-bag-fill"></i> Orders</a></li>
        <li><a href="products.php"><i class="bi bi-box-fill"></i> Products</a></li>
        <li><a href="users.php"><i class="bi bi-people-fill"></i> Users</a></li>
        <li><a href="profile.php"><i class="bi bi-person-fill"></i> Profile</a></li>
        <li><a href="../api/auth.php?action=logout"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="top-nav">
        <h1 style="margin: 0; color: var(--primary-green); font-weight: 800;">Dashboard</h1>
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
    </div>

    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card position-relative">
                    <i class="bi bi-bag-fill stat-icon"></i>
                    <div class="stat-value"><?php echo $total_orders; ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card position-relative">
                    <i class="bi bi-cash-coin stat-icon"></i>
                    <div class="stat-value">$<?php echo number_format($total_revenue, 2); ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card position-relative">
                    <i class="bi bi-box-fill stat-icon"></i>
                    <div class="stat-value"><?php echo $total_products; ?></div>
                    <div class="stat-label">Products</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card position-relative">
                    <i class="bi bi-people-fill stat-icon"></i>
                    <div class="stat-value"><?php echo $total_users; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
        </div>

        <div class="recent-orders">
            <h3 style="color: var(--primary-green); font-weight: 800; margin-bottom: 20px;">Recent Orders</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $o): ?>
                    <tr>
                        <td>#<?php echo $o['id']; ?></td>
                        <td><?php echo htmlspecialchars($o['username']); ?></td>
                        <td>$<?php echo number_format($o['total_amount'], 2); ?></td>
                        <td><span class="badge badge-<?php echo $o['status']; ?>"><?php echo ucfirst($o['status']); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                        <td><a href="order-detail.php?id=<?php echo $o['id']; ?>" class="text-decoration-none">View</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
