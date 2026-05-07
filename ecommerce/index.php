<?php
require_once 'config/config.php';
require_once 'config/database.php';

// Check if user is logged in and redirect appropriately
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: customer/shop.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koffee E-Commerce - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-green: #064E3B;
            --bg-light: #F9F8F3;
        }
        body {
            background: linear-gradient(135deg, var(--primary-green) 0%, #0a6b57 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Inter', sans-serif;
        }
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            padding: 60px 40px;
        }
        .login-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-green);
            margin-bottom: 10px;
            text-align: center;
        }
        .login-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 40px;
        }
        .form-control {
            border: 2px solid #eee;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(6, 78, 59, 0.1);
        }
        .btn-login {
            background: var(--primary-green);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            background: #043a2c;
            transform: translateY(-2px);
        }
        .signup-link {
            text-align: center;
            color: #666;
        }
        .signup-link a {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 600;
        }
        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-title">Koffee</div>
    <div class="login-subtitle">E-Commerce System</div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <form method="POST" action="api/auth.php?action=login">
        <input type="email" class="form-control" name="email" placeholder="Email Address" required>
        <input type="password" class="form-control" name="password" placeholder="Password" required>
        <button type="submit" class="btn-login">Login</button>
    </form>

    <div class="signup-link">
        Don't have an account? <a href="register.php">Sign Up</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
