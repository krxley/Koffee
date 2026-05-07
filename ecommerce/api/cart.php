<?php
header('Content-Type: application/json');
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Cart.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$action = $_GET['action'] ?? '';
$cart = new Cart($conn);
$user_id = $_SESSION['user_id'];

switch ($action) {
    case 'add':
        $product_id = $_POST['product_id'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;
        
        $result = $cart->addItem($user_id, (int)$product_id, (int)$quantity);
        $_SESSION['message'] = $result['message'];
        header('Location: ../customer/shop.php');
        break;

    case 'update':
        $product_id = $_POST['product_id'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;
        
        $result = $cart->updateQuantity($user_id, (int)$product_id, (int)$quantity);
        $_SESSION['message'] = $result['message'];
        header('Location: ../customer/cart.php');
        break;

    case 'remove':
        $product_id = $_POST['product_id'] ?? 0;
        
        $result = $cart->removeItem($user_id, (int)$product_id);
        $_SESSION['message'] = $result['message'];
        header('Location: ../customer/cart.php');
        break;

    case 'clear':
        $result = $cart->clear($user_id);
        $_SESSION['message'] = $result['message'];
        header('Location: ../customer/cart.php');
        break;

    default:
        header('Location: ../customer/cart.php');
        break;
}
?>
