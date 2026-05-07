<?php
/**
 * Order Class
 * Handles order management
 */

class Order {
    private $conn;
    private $table = 'orders';
    private $items_table = 'order_items';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create order from cart
     */
    public function createOrder($user_id, $total_amount, $payment_method, $delivery_address) {
        // Start transaction
        $this->conn->begin_transaction();

        try {
            // Insert order
            $query = "INSERT INTO {$this->table} (user_id, total_amount, payment_method, delivery_address, status) VALUES (?, ?, ?, ?, 'pending')";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('idss', $user_id, $total_amount, $payment_method, $delivery_address);
            $stmt->execute();
            $order_id = $this->conn->insert_id;

            // Get cart items
            $cart_query = "SELECT c.product_id, c.quantity, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
            $stmt = $this->conn->prepare($cart_query);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // Insert order items
            $items_query = "INSERT INTO {$this->items_table} (order_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)";
            $items_stmt = $this->conn->prepare($items_query);

            foreach ($cart_items as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $items_stmt->bind_param('iiidd', $order_id, $item['product_id'], $item['quantity'], $item['price'], $subtotal);
                $items_stmt->execute();

                // Update product stock
                $update_stock = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?";
                $stock_stmt = $this->conn->prepare($update_stock);
                $stock_stmt->bind_param('ii', $item['quantity'], $item['product_id']);
                $stock_stmt->execute();
            }

            // Clear cart
            $clear_cart = "DELETE FROM cart WHERE user_id = ?";
            $clear_stmt = $this->conn->prepare($clear_cart);
            $clear_stmt->bind_param('i', $user_id);
            $clear_stmt->execute();

            $this->conn->commit();
            return ['success' => true, 'message' => 'Order created successfully', 'order_id' => $order_id];
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => 'Failed to create order: ' . $e->getMessage()];
        }
    }

    /**
     * Get order by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get order items
     */
    public function getOrderItems($order_id) {
        $query = "SELECT oi.*, p.name, p.image_url FROM {$this->items_table} oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get user orders
     */
    public function getUserOrders($user_id, $limit = 10, $offset = 0) {
        $query = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('iii', $user_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get all orders (Admin)
     */
    public function getAll($limit = 10, $offset = 0) {
        $query = "SELECT o.*, u.username, u.email FROM {$this->table} o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Update order status (Admin)
     */
    public function updateStatus($order_id, $status) {
        $query = "UPDATE {$this->table} SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('si', $status, $order_id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Order status updated successfully'];
        }
        return ['success' => false, 'message' => 'Failed to update order status'];
    }

    /**
     * Get total orders count (Admin)
     */
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as count FROM {$this->table}";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }

    /**
     * Get total revenue (Admin)
     */
    public function getTotalRevenue() {
        $query = "SELECT SUM(total_amount) as revenue FROM {$this->table} WHERE status IN ('completed', 'processing')";
        $result = $this->conn->query($query);
        $data = $result->fetch_assoc();
        return $data['revenue'] ?? 0;
    }

    /**
     * Get revenue by status
     */
    public function getRevenueByStatus() {
        $query = "SELECT status, COUNT(*) as count, SUM(total_amount) as revenue FROM {$this->table} GROUP BY status";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
