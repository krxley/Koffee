<?php
/**
 * Product Class
 * Handles product management
 */

class Product {
    private $conn;
    private $table = 'products';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all products
     */
    public function getAll($limit = 12, $offset = 0) {
        $query = "SELECT * FROM {$this->table} WHERE is_available = TRUE ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get products by category
     */
    public function getByCategory($category, $limit = 12, $offset = 0) {
        $query = "SELECT * FROM {$this->table} WHERE category = ? AND is_available = TRUE ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sii', $category, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Search products
     */
    public function search($keyword, $limit = 12, $offset = 0) {
        $search_term = '%' . $keyword . '%';
        $query = "SELECT * FROM {$this->table} WHERE (name LIKE ? OR description LIKE ?) AND is_available = TRUE ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssii', $search_term, $search_term, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get product by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ? AND is_available = TRUE";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get all categories
     */
    public function getCategories() {
        $query = "SELECT DISTINCT category FROM {$this->table} WHERE is_available = TRUE ORDER BY category";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Add new product (Admin)
     */
    public function add($name, $description, $price, $category, $image_url, $stock_quantity) {
        $query = "INSERT INTO {$this->table} (name, description, price, category, image_url, stock_quantity) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssdssi', $name, $description, $price, $category, $image_url, $stock_quantity);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Product added successfully', 'product_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'message' => 'Failed to add product'];
    }

    /**
     * Update product (Admin)
     */
    public function update($id, $name, $description, $price, $category, $image_url, $stock_quantity, $is_available) {
        $query = "UPDATE {$this->table} SET name = ?, description = ?, price = ?, category = ?, image_url = ?, stock_quantity = ?, is_available = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssdssiii', $name, $description, $price, $category, $image_url, $stock_quantity, $is_available, $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Product updated successfully'];
        }
        return ['success' => false, 'message' => 'Failed to update product'];
    }

    /**
     * Delete product (Admin)
     */
    public function delete($id) {
        $query = "UPDATE {$this->table} SET is_available = FALSE WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Product deleted successfully'];
        }
        return ['success' => false, 'message' => 'Failed to delete product'];
    }

    /**
     * Get total products count
     */
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE is_available = TRUE";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }

    /**
     * Update stock (Admin)
     */
    public function updateStock($id, $quantity) {
        $query = "UPDATE {$this->table} SET stock_quantity = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $quantity, $id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Stock updated successfully'];
        }
        return ['success' => false, 'message' => 'Failed to update stock'];
    }
}
?>
