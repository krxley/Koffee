<?php
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koffee E-Commerce - Register</title>
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
        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            padding: 60px 40px;
        }
        .register-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-green);
            margin-bottom: 10px;
            text-align: center;
        }
        .register-subtitle {
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
        .btn-register {
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
        .btn-register:hover {
            background: #043a2c;
            transform: translateY(-2px);
        }
        .login-link {
            text-align: center;
            color: #666;
        }
        .login-link a {
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
<div class="register-container">
    <div class="register-title">Join Koffee</div>
    <div class="register-subtitle">Create your account</div>

    <form method="POST" action="api/auth.php?action=register">
        <input type="text" class="form-control" name="username" placeholder="Username" required>
        <input type="email" class="form-control" name="email" placeholder="Email Address" required>
        <input type="text" class="form-control" name="full_name" placeholder="Full Name" required>
        <input type="password" class="form-control" name="password" placeholder="Password" required>
        <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit" class="btn-register">Create Account</button>
    </form>

    <div class="login-link">
        Already have an account? <a href="index.php">Login</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
