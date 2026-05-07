<?php
/**
 * Cart Class
 * Handles shopping cart operations
 */

class Cart {
    private $conn;
    private $table = 'cart';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Add item to cart
     */
    public function addItem($user_id, $product_id, $quantity) {
        // Check if product already in cart
        $check_query = "SELECT id FROM {$this->table} WHERE user_id = ? AND product_id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param('ii', $user_id, $product_id);
        $check_stmt->execute();

        if ($check_stmt->get_result()->num_rows > 0) {
            // Update quantity
            return $this->updateQuantity($user_id, $product_id, $quantity);
        }

        // Insert new item
        $query = "INSERT INTO {$this->table} (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('iii', $user_id, $product_id, $quantity);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Item added to cart'];
        }
        return ['success' => false, 'message' => 'Failed to add item to cart'];
    }

    /**
     * Update quantity
     */
    public function updateQuantity($user_id, $product_id, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($user_id, $product_id);
        }

        $query = "UPDATE {$this->table} SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('iii', $quantity, $user_id, $product_id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Quantity updated'];
        }
        return ['success' => false, 'message' => 'Failed to update quantity'];
    }

    /**
     * Remove item from cart
     */
    public function removeItem($user_id, $product_id) {
        $query = "DELETE FROM {$this->table} WHERE user_id = ? AND product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $user_id, $product_id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Item removed from cart'];
        }
        return ['success' => false, 'message' => 'Failed to remove item'];
    }

    /**
     * Get cart items
     */
    public function getItems($user_id) {
        $query = "SELECT c.*, p.name, p.price, p.image_url FROM {$this->table} c JOIN products p ON c.product_id = p.id WHERE c.user_id = ? ORDER BY c.added_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get cart total
     */
    public function getTotal($user_id) {
        $query = "SELECT SUM(c.quantity * p.price) as total FROM {$this->table} c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    /**
     * Get cart count
     */
    public function getCount($user_id) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] ?? 0;
    }

    /**
     * Clear cart
     */
    public function clear($user_id) {
        $query = "DELETE FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $user_id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Cart cleared'];
        }
        return ['success' => false, 'message' => 'Failed to clear cart'];
    }
}
?>
