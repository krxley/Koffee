<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Order.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$order = new Order($conn);
$page = $_GET['page'] ?? 1;
$limit = ITEMS_PER_PAGE;
$offset = ($page - 1) * $limit;

$orders = $order->getAll($limit, $offset);
$total_orders = $order->getTotalCount();
$total_pages = ceil($total_orders / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Koffee Admin</title>
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
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead th {
            border: none;
            background: var(--primary-green);
            color: white;
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
        .btn-action {
            padding: 5px 10px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-brand">Koffee Admin</div>
    <ul class="sidebar-nav">
        <li><a href="dashboard.php"><i class="bi bi-house-fill"></i> Dashboard</a></li>
        <li><a href="orders.php" class="active"><i class="bi bi-bag-fill"></i> Orders</a></li>
        <li><a href="products.php"><i class="bi bi-box-fill"></i> Products</a></li>
        <li><a href="users.php"><i class="bi bi-people-fill"></i> Users</a></li>
        <li><a href="profile.php"><i class="bi bi-person-fill"></i> Profile</a></li>
        <li><a href="../api/auth.php?action=logout"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h1 style="color: var(--primary-green); font-weight: 800; margin-bottom: 30px;">All Orders</h1>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td><strong>#<?php echo $o['id']; ?></strong></td>
                    <td><?php echo htmlspecialchars($o['username']); ?></td>
                    <td><?php echo htmlspecialchars($o['email']); ?></td>
                    <td>$<?php echo number_format($o['total_amount'], 2); ?></td>
                    <td><span class="badge badge-<?php echo $o['status']; ?>"><?php echo ucfirst($o['status']); ?></span></td>
                    <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                    <td><a href="order-detail.php?id=<?php echo $o['id']; ?>" class="btn btn-sm btn-primary btn-action">View Details</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a></li>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $i === (int)$page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
