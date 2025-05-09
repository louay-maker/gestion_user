<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Model.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class FunctionsController {
    private $model;
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
        $this->model = new Model($connection);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Get user profile with role information
     */
    public function getUserProfile($userId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT u.ID, u.Nom, u.Prenom, u.Email, r.NomRole as Role 
                FROM utilisateur u
                JOIN role r ON u.Role_ID = r.ID
                WHERE u.ID = ?
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user statistics (placeholders - you'll need to implement these tables)
     */
    public function getUserStats($userId) {
        return [
            'startups' => $this->countUserRecords('startups', $userId),
            'contracts' => $this->countUserRecords('contracts', $userId),
            'investments' => $this->countUserRecords('investments', $userId),
            'events' => $this->countUserRecords('events', $userId)
        ];
    }

    /**
     * Count records for a specific user
     */
    private function countUserRecords($table, $userId) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM $table WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Count error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Count all records in a specific table
     * Only works with tables that exist in the database
     */
    public function countRecords($table) {
        // Only allow counting from existing tables
        if ($table !== 'utilisateur' && $table !== 'role') {
            return 0;
        }
        
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM $table");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Count error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get recent users with limit
     */
    public function getRecentUsers($limit = 5) {
        try {
            $stmt = $this->conn->prepare("
                SELECT u.*, r.NomRole 
                FROM utilisateur u
                JOIN role r ON u.Role_ID = r.ID
                ORDER BY u.ID DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get recent users error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Placeholder for getRecentStartups
     * Returns empty array since table doesn't exist
     */
    public function getRecentStartups($limit = 5) {
        return [];
    }

    /**
     * Get all users with their role information
     */
    public function getAllUsers() {
        try {
            $stmt = $this->conn->prepare("
                SELECT u.*, r.NomRole 
                FROM utilisateur u
                JOIN role r ON u.Role_ID = r.ID
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user by ID with role information
     */
    public function getUserById(int $id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT u.*, r.NomRole 
                FROM utilisateur u
                JOIN role r ON u.Role_ID = r.ID
                WHERE u.ID = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create new user
     */
    public function createUser(array $data) {
        try {
            // Hash password
            $data['MotDePasse'] = password_hash($data['MotDePasse'], PASSWORD_DEFAULT);
            
            $stmt = $this->conn->prepare("
                INSERT INTO utilisateur (Nom, Prenom, Email, MotDePasse, Role_ID) 
                VALUES (:Nom, :Prenom, :Email, :MotDePasse, :Role_ID)
            ");
            
            return $stmt->execute([
                ':Nom' => $data['Nom'],
                ':Prenom' => $data['Prenom'],
                ':Email' => $data['Email'],
                ':MotDePasse' => $data['MotDePasse'],
                ':Role_ID' => $data['Role_ID'] ?? 1 // Default to 'user' role
            ]);
        } catch (PDOException $e) {
            error_log("Create user error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user information
     */
    public function updateUser(int $id, array $data): bool {
        try {
            // If password is being updated
            if (isset($data['MotDePasse'])) {
                $data['MotDePasse'] = password_hash($data['MotDePasse'], PASSWORD_DEFAULT);
            }
            
            $sets = [];
            foreach ($data as $col => $_) {
                $sets[] = "`$col` = :$col";
            }
            
            $sql = 'UPDATE `utilisateur` SET ' . implode(', ', $sets) . ' WHERE `ID` = :id';
            $data['id'] = $id;
            
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            error_log("Update user error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user
     */
    public function deleteUser(int $id): bool {
        try {
            $stmt = $this->conn->prepare("DELETE FROM utilisateur WHERE ID = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Delete user error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle login request
     */
    public function loginUser(string $email, string $password): bool {
        try {
            $stmt = $this->conn->prepare("
                SELECT u.*, r.NomRole 
                FROM utilisateur u
                JOIN role r ON u.Role_ID = r.ID
                WHERE u.Email = ?
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['MotDePasse'])) {
                $_SESSION['user_id'] = $user['ID'];
                $_SESSION['username'] = $user['Nom'] . ' ' . $user['Prenom'];
                $_SESSION['email'] = $user['Email'];
                $_SESSION['user_type'] = strtolower($user['NomRole']);
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle registration
     */
    public function registerUser(array $data) {
        try {
            // Check if email already exists
            $stmt = $this->conn->prepare("SELECT ID FROM utilisateur WHERE Email = ?");
            $stmt->execute([$data['Email']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
            
            // Hash password
            $data['MotDePasse'] = password_hash($data['MotDePasse'], PASSWORD_DEFAULT);
            
            // Default to user role if not specified
            $data['Role_ID'] = $data['Role_ID'] ?? 1;
            
            $stmt = $this->conn->prepare("
                INSERT INTO utilisateur (Nom, Prenom, Email, MotDePasse, Role_ID) 
                VALUES (:Nom, :Prenom, :Email, :MotDePasse, :Role_ID)
            ");
            
            $success = $stmt->execute([
                ':Nom' => $data['Nom'],
                ':Prenom' => $data['Prenom'],
                ':Email' => $data['Email'],
                ':MotDePasse' => $data['MotDePasse'],
                ':Role_ID' => $data['Role_ID']
            ]);
            
            if ($success) {
                return ['success' => true, 'message' => 'Registration successful', 'user_id' => $this->conn->lastInsertId()];
            }
            return ['success' => false, 'message' => 'Registration failed'];
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    /**
     * Handle password reset request
     */
    public function forgotPassword(string $email) {
        try {
            // Check if email exists
            $stmt = $this->conn->prepare("SELECT ID FROM utilisateur WHERE Email = ?");
            $stmt->execute([$email]);
            if (!$stmt->fetch()) {
                return "Email not found";
            }
            
            $token = bin2hex(random_bytes(50));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $this->conn->prepare("
                UPDATE utilisateur
                SET reset_token = ?, reset_expiry = ?
                WHERE Email = ?
            ");
            
            if ($stmt->execute([$token, $expiry, $email])) {
                $resetLink = "http://yourdomain.com/reset-password.php?token=$token";
                
                // Send email with PHPMailer (implementation similar to your existing code)
                // ...
                
                return "Password reset link sent to your email";
            }
            return "Failed to generate reset token";
        } catch (PDOException $e) {
            error_log("Password reset error: " . $e->getMessage());
            return "An error occurred";
        }
    }

    /**
     * Complete password reset
     */
    public function resetPassword(string $token, string $password): bool
{
    // Verify token first
    $user = $this->model->fetchOne(
        "SELECT * FROM utilisateur WHERE reset_token = :token AND reset_expiry > NOW()",
        [':token' => $token]
    );

    if (!$user) return false;

    // Update password and clear reset token
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    return $this->model->executeQuery(
        "UPDATE utilisateur SET 
            password = :password,
            reset_token = NULL,
            reset_expiry = NULL
        WHERE id = :id",
        [
            ':password' => $hashedPassword,
            ':id' => $user['id']
        ]
    );
}

    /**
     * Handle logout
     */
    public function logout(): bool {
        session_unset();
        session_destroy();
        return true;
    }

    /**
     * Get all roles
     */
    public function getAllRoles() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM role");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get roles error: " . $e->getMessage());
            return [];
        }
    }
    public function getUserCountByRole() {
        $sql = "SELECT r.ID, r.NomRole, COUNT(u.ID) as count 
                FROM role r 
                LEFT JOIN utilisateur u ON r.ID = u.Role_ID 
                GROUP BY r.ID, r.NomRole";
        $result = $this->conn->query($sql);
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    
    public function getMonthlyRegistrations($months = 6) {
        // This is a placeholder - you would need to adjust based on your database structure
        // Assuming you have a registration_date or created_at field
        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
                FROM utilisateur 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL $months MONTH) 
                GROUP BY month 
                ORDER BY month DESC";
        $result = $this->conn->query($sql);
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    public function countActiveUsers() {
        return $this->conn->query("SELECT COUNT(*) FROM utilisateur WHERE Actif = 1")->fetchColumn();
    }
    
    public function countRecordsByRole($roleName) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) 
            FROM utilisateur u
            JOIN role r ON u.Role_ID = r.ID 
            WHERE r.NomRole = ?
        ");
        $stmt->execute([$roleName]);
        return $stmt->fetchColumn();
    }
    
    public function getRoleDistribution() {
        return $this->conn->query("
            SELECT r.NomRole, COUNT(u.ID) as count 
            FROM role r
            LEFT JOIN utilisateur u ON r.ID = u.Role_ID 
            GROUP BY r.NomRole
        ")->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getRegistrationTrends() {
        $monthly = $this->conn->query("
            SELECT DATE_FORMAT(DateInscription, '%Y-%m') as month, 
                   COUNT(*) as count 
            FROM utilisateur 
            GROUP BY month 
            ORDER BY month
        ")->fetchAll(PDO::FETCH_KEY_PAIR);
    
        return ['monthly' => $monthly];
    }
    
    public function getGenderDistribution() {
        if (!$this->columnExists('utilisateur', 'Genre')) return [];
        
        return $this->conn->query("
            SELECT Genre, COUNT(*) as count 
            FROM utilisateur 
            GROUP BY Genre
        ")->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function columnExists($table, $column) {
        $stmt = $this->conn->prepare("SHOW COLUMNS FROM $table LIKE ?");
        $stmt->execute([$column]);
        return (bool)$stmt->fetch();
    }
}
  


