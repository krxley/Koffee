<?php
header('Content-Type: application/json');
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/User.php';

$action = $_GET['action'] ?? '';
$user = new User($conn);

switch ($action) {
    case 'login':
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $_SESSION['message'] = 'Email and password are required';
            header('Location: ../index.php');
            exit();
        }
        
        $result = $user->login($email, $password);
        if ($result['success']) {
            $_SESSION['message'] = 'Welcome back!';
            if ($result['role'] === 'admin') {
                header('Location: ../admin/dashboard.php');
            } else {
                header('Location: ../customer/shop.php');
            }
        } else {
            $_SESSION['message'] = $result['message'];
            header('Location: ../index.php');
        }
        break;

    case 'register':
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $full_name = $_POST['full_name'] ?? '';

        if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
            $_SESSION['message'] = 'All fields are required';
            header('Location: ../register.php');
            exit();
        }

        if ($password !== $confirm_password) {
            $_SESSION['message'] = 'Passwords do not match';
            header('Location: ../register.php');
            exit();
        }

        if (strlen($password) < 6) {
            $_SESSION['message'] = 'Password must be at least 6 characters';
            header('Location: ../register.php');
            exit();
        }

        $result = $user->register($username, $email, $password, $full_name);
        if ($result['success']) {
            $_SESSION['message'] = 'Registration successful! Please login.';
            header('Location: ../index.php');
        } else {
            $_SESSION['message'] = $result['message'];
            header('Location: ../register.php');
        }
        break;

    case 'logout':
        $user->logout();
        $_SESSION['message'] = 'Logged out successfully';
        header('Location: ../index.php');
        break;

    default:
        header('Location: ../index.php');
        break;
}
?>
