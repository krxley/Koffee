<?php
/**
 * Application Configuration
 */

define('APP_NAME', 'Koffee E-Commerce');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/Koffee/ecommerce');

// Session timeout (in seconds)
define('SESSION_TIMEOUT', 3600);

// Password hashing
define('PASSWORD_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_OPTIONS', ['cost' => 12]);

// Admin email
define('ADMIN_EMAIL', 'admin@koffee.com');

// Items per page for pagination
define('ITEMS_PER_PAGE', 12);

// Enable/Disable debug mode
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
