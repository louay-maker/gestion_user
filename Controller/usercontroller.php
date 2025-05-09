<?php
require_once "C:/xampp/htdocs/projet/config.php";
require_once "C:/xampp/htdocs/projet/Model/Model.php"; 
require_once "C:/xampp/htdocs/projet/vendor/autoload.php";

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
        // Vérifier si l'email existe
        $user = $this->model->fetchUserByEmail($email);
        if (!$user) {
            return false;
        }
        $token = bin2hex(random_bytes(50));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $query = "UPDATE utilisateur SET reset_token = :token, reset_expiry = :expiry WHERE Email = :email";
        $result = $this->model->executeQuery($query, [
            ':token' => $token,
            ':expiry' => $expiry,
            ':email' => $email
        ]);
        if ($result > 0) {
            // Construire le lien de réinitialisation avec l'URL de ton site
            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/projet/View/FrontOffice/reset-password.php?token=" . $token;
            
            $mail = new PHPMailer(true);
            try {
                // Configuration du serveur SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';             // Utilise Gmail comme exemple
                $mail->SMTPAuth = true;
                $mail->Username = 'ton-email@gmail.com';    // Ton adresse Gmail
                $mail->Password = 'ton-mot-de-passe-app';   // Mot de passe d'application Gmail
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                
                // Si tu utilises Gmail, active ceci pour déboguer
                //$mail->SMTPDebug = 2;
                
                // Destinataires
                $mail->setFrom('ton-email@gmail.com', 'StartUp Connect');
                $mail->addAddress($email);
                
                // Contenu de l'email
                $mail->isHTML(true);
                $mail->Subject = 'Réinitialisation de votre mot de passe';
                $mail->Body = "
                    <html>
                    <body>
                        <h2>Réinitialisation de votre mot de passe</h2>
                        <p>Vous avez demandé à réinitialiser votre mot de passe.</p>
                        <p>Cliquez sur le lien ci-dessous pour définir un nouveau mot de passe :</p>
                        <p><a href='$resetLink'>Réinitialiser mon mot de passe</a></p>
                        <p>Ce lien expirera dans 1 heure.</p>
                        <p>Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email.</p>
                        <br>
                        <p>Cordialement,<br>L'équipe StartUp Connect</p>
                    </body>
                    </html>
                ";
                $mail->AltBody = "Réinitialisation de votre mot de passe\n\n
                    Vous avez demandé à réinitialiser votre mot de passe.\n
                    Copiez et collez le lien suivant dans votre navigateur pour définir un nouveau mot de passe :\n
                    $resetLink\n\n
                    Ce lien expirera dans 1 heure.\n
                    Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email.\n\n
                    Cordialement,\nL'équipe StartUp Connect";
                
                $mail->send();
                return true;
            } catch (Exception $e) {
                error_log("Erreur d'envoi d'email: " . $mail->ErrorInfo);
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
