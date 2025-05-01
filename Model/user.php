<?php
require_once 'config.php';

class Utilisateur {
    private $id, $nom, $prénom, $email, $motdepasse, $role_id;

    public function __construct($nom, $prénom, $email, $motdepasse, $role_id, $id = null) {
        $this->id = $id;
        $this->nom = trim($nom);
        $this->prénom = trim($prénom);
        $this->email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        $this->motdepasse = $motdepasse; // Plaintext password storage
        $this->role_id = (int)$role_id;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrénom() { return $this->prénom; }
    public function getEmail() { return $this->email; }
    public function getMotdepasse() { return $this->motdepasse; }
    public function getRoleId() { return $this->role_id; }

    // CRUD methods
    public static function addUtilisateur($utilisateur) {
        $sql = "INSERT INTO Utilisateur (Nom, Prénom, Email, MotDePasse, Role_ID) 
                VALUES (:nom, :prénom, :email, :motdepasse, :role_id)";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            return $query->execute([
                'nom' => $utilisateur->getNom(),
                'prénom' => $utilisateur->getPrénom(),
                'email' => $utilisateur->getEmail(),
                'motdepasse' => $utilisateur->getMotdepasse(),
                'role_id' => $utilisateur->getRoleId()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur d'ajout utilisateur: " . $e->getMessage());
            throw new Exception("Erreur technique lors de l'inscription");
        }
    }

    public static function updateUtilisateur($utilisateur) {
        $sql = "UPDATE Utilisateur SET 
                    nom = :nom, 
                    prénom = :prénom, 
                    email = :email, 
                    motdepasse = :motdepasse, 
                    role_id = :role_id 
                WHERE id = :id";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            return $query->execute([
                'nom' => $utilisateur->getNom(),
                'prénom' => $utilisateur->getPrénom(),
                'email' => $utilisateur->getEmail(),
                'motdepasse' => $utilisateur->getMotdepasse(),
                'role_id' => $utilisateur->getRoleId(),
                'id' => $utilisateur->getId()
            ]);
        } catch (PDOException $e) {
            error_log("Erreur de modification utilisateur: " . $e->getMessage());
            return false;
        }
    }

    public static function deleteUtilisateur($id) {
        $sql = "DELETE FROM Utilisateur WHERE id = :id";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            return $query->execute(['id' => (int)$id]);
        } catch (PDOException $e) {
            error_log("Erreur de suppression utilisateur: " . $e->getMessage());
            return false;
        }
    }

    public static function getAllUtilisateurs() {
        $sql = "SELECT u.*, r.NomRole FROM Utilisateur u JOIN Role r ON u.role_id = r.ID";
        $db = Config::getConnexion();

        try {
            $query = $db->query($sql);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur de récupération utilisateurs: " . $e->getMessage());
            return [];
        }
    }

    public static function getUtilisateurById($id) {
        $sql = "SELECT u.*, r.NomRole FROM Utilisateur u 
                JOIN Role r ON u.role_id = r.ID 
                WHERE u.id = :id";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => (int)$id]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur de recherche utilisateur: " . $e->getMessage());
            return null;
        }
    }

    public static function searchUtilisateurs($keyword) {
        $sql = "SELECT u.*, r.NomRole FROM Utilisateur u 
                JOIN Role r ON u.role_id = r.ID
                WHERE u.nom LIKE :kw 
                OR u.prénom LIKE :kw 
                OR u.email LIKE :kw";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(['kw' => '%' . trim($keyword) . '%']);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur de recherche: " . $e->getMessage());
            return [];
        }
    }

    public static function emailExists($email) {
        $sql = "SELECT id FROM Utilisateur WHERE email = :email";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(['email' => filter_var(trim($email), FILTER_VALIDATE_EMAIL)]);
            return $query->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur de vérification email: " . $e->getMessage());
            return true;
        }
    }

    public static function authenticate($email, $password) {
        $sql = "SELECT u.*, r.NomRole FROM Utilisateur u 
                JOIN Role r ON u.role_id = r.ID 
                WHERE email = :email";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(['email' => filter_var(trim($email), FILTER_VALIDATE_EMAIL)]);
            $user = $query->fetch(PDO::FETCH_ASSOC);

            // Plaintext password comparison
            return ($user && $password === $user['motdepasse']) ? $user : false;
        } catch (PDOException $e) {
            error_log("Erreur d'authentification: " . $e->getMessage());
            return false;
        }
    }
}