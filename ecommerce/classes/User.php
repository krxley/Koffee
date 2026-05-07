<?php
/**
 * User Class
 * Handles user authentication and profile management
 */

class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Register a new user
     */
    public function register($username, $email, $password, $full_name) {
        // Check if user already exists
        $query = "SELECT id FROM {$this->table} WHERE username = ? OR email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Username or email already exists'];
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Insert user
        $query = "INSERT INTO {$this->table} (username, email, password, full_name, role) VALUES (?, ?, ?, ?, 'customer')";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssss', $username, $email, $hashed_password, $full_name);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Registration successful', 'user_id' => $this->conn->insert_id];
        }
        return ['success' => false, 'message' => 'Registration failed'];
    }

    /**
     * Login user
     */
    public function login($email, $password) {
        $query = "SELECT id, username, email, password, role, is_active FROM {$this->table} WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        $user = $result->fetch_assoc();

        if (!$user['is_active']) {
            return ['success' => false, 'message' => 'Account is deactivated'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_time'] = time();

        return ['success' => true, 'message' => 'Login successful', 'role' => $user['role']];
    }

    /**
     * Get user by ID
     */
    public function getUserById($user_id) {
        $query = "SELECT id, username, email, full_name, phone, address, city, postal_code, role, created_at FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update user profile
     */
    public function updateProfile($user_id, $full_name, $phone, $address, $city, $postal_code) {
        $query = "UPDATE {$this->table} SET full_name = ?, phone = ?, address = ?, city = ?, postal_code = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sssssi', $full_name, $phone, $address, $city, $postal_code, $user_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Profile updated successfully'];
        }
        return ['success' => false, 'message' => 'Profile update failed'];
    }

    /**
     * Change password
     */
    public function changePassword($user_id, $old_password, $new_password) {
        $query = "SELECT password FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!password_verify($old_password, $user['password'])) {
            return ['success' => false, 'message' => 'Old password is incorrect'];
        }

        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
        $query = "UPDATE {$this->table} SET password = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('si', $hashed_password, $user_id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Password changed successfully'];
        }
        return ['success' => false, 'message' => 'Password change failed'];
    }

    /**
     * Logout user
     */
    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }

    /**
     * Get all users (Admin)
     */
    public function getAllUsers($limit = 10, $offset = 0) {
        $query = "SELECT id, username, email, full_name, role, is_active, created_at FROM {$this->table} ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get total users count
     */
    public function getTotalUsers() {
        $query = "SELECT COUNT(*) as count FROM {$this->table}";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }
}
?>
