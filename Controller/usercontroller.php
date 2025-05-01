<?php
require_once dirname(__DIR__) . "/config.php";
require_once dirname(__DIR__) . "/Model/Model.php";
require_once dirname(__DIR__) . '/vendor/autoload.php';

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

    // Handle all requests
    public function handleRequest($request) {
        $action = $request['action'] ?? '';
        $result = false;
        
        switch ($action) {
            case 'loginUser':
                $result = $this->loginUser($request['Email'], $request['MotDePasse']);
                break;
            case 'registerUser':
                $result = $this->registerUser([
                    'Nom' => $request['Nom'],
                    'Prénom' => $request['Prénom'],
                    'Email' => $request['Email'],
                    'MotDePasse' => $request['MotDePasse'],
                    'Role_ID' => $request['Role_ID'] ?? 2
                ]);
                break;
            case 'loginAdmin':
                $result = $this->loginAdmin($request['Email'], $request['MotDePasse']);
                break;
            case 'createAdmin':
                $result = $this->createAdmin([
                    'adminname' => $request['adminname'],
                    'Email' => $request['Email'],
                    'MotDePasse' => $request['MotDePasse']
                ]);
                break;
            case 'logout':
                $result = $this->logout();
                break;
            case 'updateUser':
                $result = $this->updateUser($request['id'], $request);
                break;
            case 'deleteUser':
                $result = $this->deleteUser($request['id']);
                break;
            case 'forgotPassword':
                $result = $this->forgotPassword($request['Email']);
                break;
            case 'resetPassword':
                $result = $this->resetPassword($request['token'], $request['MotDePasse']);
                break;
        }
        return $result;
    }

    // User CRUD Operations
    public function createUser($data) {
        try {
            $data['MotDePasse'] = password_hash($data['MotDePasse'], PASSWORD_DEFAULT);
            return $this->model->insertUser($data);
        } catch (PDOException $e) {
            error_log("Create user error: " . $e->getMessage());
            return false;
        }
    }

    public function updateUser($id, $data) {
        try {
            if (!empty($data['MotDePasse'])) {
                $data['MotDePasse'] = password_hash($data['MotDePasse'], PASSWORD_DEFAULT);
            }
            return $this->model->updateUser($id, $data);
        } catch (PDOException $e) {
            error_log("Update user error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteUser($id) {
        return $this->model->deleteById('utilisateur', $id);
    }

    public function getAllUsers() {
        return $this->model->fetchAll('utilisateur');
    }

    public function getUser($id) {
        return $this->model->fetchById('utilisateur', $id);
    }

    // Authentication
    public function loginUser($email, $password) {
        $user = $this->model->fetchUserByEmail($email);
        if ($user && password_verify($password, $user['MotDePasse'])) {
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['Nom'] = $user['Nom'];
            $_SESSION['Prénom'] = $user['Prénom'];
            $_SESSION['Email'] = $user['Email'];
            $_SESSION['Role_ID'] = $user['Role_ID'];
            return true;
        }
        return false;
    }

    public function registerUser($data) {
        $data['MotDePasse'] = password_hash($data['MotDePasse'], PASSWORD_DEFAULT);
        return $this->model->insertUser($data);
    }

    // Admin Functions
    public function loginAdmin($email, $password) {
        $admin = $this->model->fetchAdminByEmail($email);
        if ($admin && password_verify($password, $admin['MotDePasse'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['adminname'] = $admin['adminname'];
            $_SESSION['Email'] = $admin['Email'];
            $_SESSION['Role_ID'] = 1;
            return true;
        }
        return false;
    }

    public function createAdmin($data) {
        $data['MotDePasse'] = password_hash($data['MotDePasse'], PASSWORD_DEFAULT);
        return $this->model->insertAdmin($data);
    }

    // Password Recovery
    public function forgotPassword($email) {
        $token = bin2hex(random_bytes(50));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $query = "UPDATE utilisateur SET reset_token = :token, reset_expiry = :expiry WHERE Email = :email";
        $result = $this->model->executeQuery($query, [
            ':token' => $token,
            ':expiry' => $expiry,
            ':email' => $email
        ]);

        if ($result > 0) {
            $resetLink = "http://yourdomain.com/reset-password.php?token=$token";
            
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'your@email.com';
                $mail->Password = 'your_app_password';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('your@email.com', 'Your System');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body = "Click to reset: <a href='$resetLink'>$resetLink</a>";
                
                $mail->send();
                return true;
            } catch (Exception $e) {
                error_log("Mail Error: " . $mail->ErrorInfo);
                return false;
            }
        }
        return false;
    }

    public function resetPassword($token, $newPassword) {
        $user = $this->model->fetchOne(
            "SELECT * FROM utilisateur WHERE reset_token = :token AND reset_expiry > NOW()",
            [':token' => $token]
        );

        if ($user) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            return $this->model->executeQuery(
                "UPDATE utilisateur SET MotDePasse = :password, reset_token = NULL, reset_expiry = NULL WHERE ID = :id",
                [':password' => $hashedPassword, ':id' => $user['ID']]
            );
        }
        return false;
    }

    // Session Management
    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }

    // Counters
    public function countUsers() {
        return $this->model->countRecords('utilisateur');
    }

    public function countAdmins() {
        return $this->model->countRecords('admins');
    }
}