<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Model.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class FunctionsController {
    private $model;

    public function __construct($connection) {
        $this->model = new Model($connection);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Récupère tous les utilisateurs
     */
    public function getAllUsers() {
        return $this->model->fetchAll('utilisateur');
    }

    /**
     * Récupère un utilisateur par son ID
     */
    public function getUserById(int $id) {
        return $this->model->fetchById('utilisateur', $id);
    }

    /**
     * Crée un nouvel utilisateur
     * @param array $data  ['Nom'=>…, 'Prenom'=>…, 'Email'=>…, 'MotDePasse'=>…, 'Role_ID'=>…]
     * @return int|false   ID inséré ou false en cas d’erreur
     */
    public function createUser(array $data) {
        // hash du mot de passe
        $data['MotDePasse'] = password_hash($data['MotDePasse'], PASSWORD_DEFAULT);
        // délégation au Model
        return $this->model->insertUser($data);
    }

    /**
     * Met à jour un utilisateur existant
     * @param int   $id    ID de l’utilisateur
     * @param array $data  colonnes à mettre à jour
     * @return bool        true si OK, false sinon
     */
    public function updateUser(int $id, array $data): bool {
        // Si un nouveau mot de passe est fourni, on le hash
        if (isset($data['MotDePasse'])) {
            $data['MotDePasse'] = password_hash($data['MotDePasse'], PASSWORD_DEFAULT);
        }
        // construction dynamique du SET
        $sets = [];
        foreach ($data as $col => $_) {
            $sets[] = "`$col` = :$col";
        }
        $sql = 'UPDATE `utilisateur` SET ' . implode(', ', $sets) . ' WHERE `ID` = :id';

        // on ajoute l'ID aux paramètres
        $params = $data;
        $params['id'] = $id;

        // exécute et renvoie true si au moins une ligne modifiée
        return $this->model->executeQuery($sql, $params) > 0;
    }

    /**
     * Supprime un utilisateur
     */
    public function deleteUser(int $id) {
        return $this->model->deleteById('utilisateur', $id);
    }

    /**
     * Gestion des actions frontoffice (login, register, etc.)
     */
    public function handleRequest(array $request) {
        $action = $request['action'] ?? '';
        $result = false;
        switch ($action) {
            case 'loginUser':
                $result = $this->loginUser($request['Email'], $request['MotDePasse']);
                break;
            case 'registerUser':
                $result = $this->registerUser([
                    'Nom'       => $request['Nom'],
                    'Prenom'    => $request['Prenom'],
                    'Email'     => $request['Email'],
                    'MotDePasse'=> $request['MotDePasse'],
                    'Role_ID'   => $request['Role_ID'] ?? 2
                ]);
                break;
            case 'loginAdmin':
                $result = $this->loginAdmin($request['Email'], $request['MotDePasse']);
                break;
            case 'createAdmin':
                $result = $this->createAdmin([
                    'adminname' => $request['adminname'],
                    'Email'     => $request['Email'],
                    'MotDePasse'=> $request['MotDePasse']
                ]);
                break;
            case 'logout':
                $result = $this->logout();
                break;
            case 'listUsers':
                $result = $this->listUsers();
                break;
            case 'deleteUser':
                $result = $this->deleteUser((int)$request['ID']);
                break;
            default:
                $result = false;
                break;
        }
        return $result;
    }

    /**
     * Pour récupérer la liste en frontoffice
     */
    public function listUsers() {
        return $this->model->fetchAll('utilisateur');
    }

    /**
     * Génération et envoi du lien de réinitialisation de mot de passe
     */
    public function forgotPassword(string $email) {
        $token  = bin2hex(random_bytes(50));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $update = "UPDATE utilisateur
                   SET reset_token = :token, reset_expiry = :expiry
                   WHERE Email = :email";
        $res = $this->model->executeQuery($update, [
            ':token'  => $token,
            ':expiry' => $expiry,
            ':email'  => $email
        ]);

        if ($res > 0) {
            $link = "http://localhost/utls/View/frontoffice/reset-password.php?token=$token";
            $body = "
                <!DOCTYPE html>
                <html><body>
                  <h2>Password Reset Request</h2>
                  <p>Cliquez sur le bouton pour réinitialiser :</p>
                  <a href='$link'>Reset Password</a>
                </body></html>";

            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'cracked.soft00@gmail.com';
                $mail->Password   = 'yeyv bodn ukig twqq';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('cracked.soft00@gmail.com', 'Tunifarm');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = '🔒 Password Reset';
                $mail->Body    = $body;

                $mail->send();
                return "Password reset link sent";
            } catch (Exception $e) {
                return "Email error: {$mail->ErrorInfo}";
            }
        }
        return "Email not found";
    }

    public function loginUser(string $email, string $password): bool {
        $user = $this->model->fetchUserByEmail($email);
        if ($user && password_verify($password, $user['MotDePasse'])) {
            $_SESSION['user_id']   = $user['ID'];
            $_SESSION['Nom']       = $user['Nom'];
            $_SESSION['Prenom']    = $user['Prenom'];
            $_SESSION['Email']     = $user['Email'];
            $_SESSION['Role_ID']   = $user['Role_ID'];
            return true;
        }
        return false;
    }

    public function registerUser(array $data) {
        $data['MotDePasse'] = password_hash($data['MotDePasse'], PASSWORD_DEFAULT);
        return $this->model->insertUser($data);
    }

    public function loginAdmin(string $email, string $password): bool {
        $admin = $this->model->fetchAdminByEmail($email);
        if ($admin && password_verify($password, $admin['MotDePasse'])) {
            $_SESSION['adminname'] = $admin['adminname'];
            $_SESSION['Email']     = $admin['Email'];
            return true;
        }
        return false;
    }

    public function createAdmin(array $data) {
        if (empty($data['adminname']) || empty($data['Email']) || empty($data['MotDePasse'])) {
            return ['success' => false, 'message' => 'Missing fields'];
        }
        $data['MotDePasse'] = password_hash($data['MotDePasse'], PASSWORD_DEFAULT);
        return $this->model->insertAdmin($data);
    }

    public function logout(): bool {
        session_unset();
        session_destroy();
        return true;
    }

    public function resetPassword(string $token, string $newPassword): string {
        $query = "SELECT * FROM utilisateur WHERE reset_token = :token AND reset_expiry > NOW()";
        $user  = $this->model->fetchOne($query, [':token' => $token]);
        if ($user) {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $upd    = "UPDATE utilisateur
                       SET MotDePasse = :password,
                           reset_token = NULL,
                           reset_expiry = NULL
                       WHERE ID = :id";
            $ok = $this->model->executeQuery($upd, [
                ':password' => $hashed,
                ':id'       => $user['ID']
            ]);
            return $ok ? "Password reset successful" : "Reset failed";
        }
        return "Invalid token";
    }

    public function listAdmins() {
        return $this->model->fetchAll('admins');
    }

    public function countTopics(): int {
        return $this->model->countRecords('topics');
    }

    public function countCategories(): int {
        return $this->model->countRecords('categories');
    }

    public function countAdmins(): int {
        return $this->model->countRecords('admins');
    }

    public function countReplies(): int {
        return $this->model->countRecords('replies');
    }
}
