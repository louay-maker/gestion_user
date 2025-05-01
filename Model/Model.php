<?php
class Model {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    // Generic Methods
    public function fetchAll($table) {
        try {
            $stmt = $this->conn->query("SELECT * FROM $table");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Fetch all error: " . $e->getMessage());
            return [];
        }
    }

    public function fetchById($table, $id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM $table WHERE ID = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Fetch by ID error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteById($table, $id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM $table WHERE ID = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Delete error: " . $e->getMessage());
            return false;
        }
    }

    public function countRecords($table) {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) AS count FROM $table");
            return $stmt->fetch(PDO::FETCH_OBJ)->count;
        } catch (PDOException $e) {
            error_log("Count error: " . $e->getMessage());
            return 0;
        }
    }

    // User Methods
    public function fetchUserByEmail($email) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM utilisateur WHERE Email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Fetch user error: " . $e->getMessage());
            return false;
        }
    }

    public function insertUser($data) {
        try {
            $sql = "INSERT INTO utilisateur 
                    (Nom, Prenom, Email, MotDePasse, Role_ID) 
                    VALUES (:Nom, :Prenom, :Email, :MotDePasse, :Role_ID)";
            
            $stmt = $this->conn->prepare($sql);
    
            // Préparer les données sans le champ avatar
            $execute_data = [
                ':Nom'        => $data['Nom'] ?? null,
                ':Prenom'     => $data['Prenom'] ?? null,
                ':Email'      => $data['Email'] ?? null,
                ':MotDePasse' => $data['MotDePasse'] ?? null,
                ':Role_ID'    => $data['Role_ID'] ?? 1 // Default value
            ];
    
            return $stmt->execute($execute_data);
        } catch (PDOException $e) {
            error_log("Erreur SQL : " . $e->getMessage());
            return false;
        }
    }
    


    public function updateUser($id, $data) {
        try {
            $fields = [];
            $params = [':id' => $id];

            foreach ($data as $key => $value) {
                if ($key === 'MotDePasse' && !empty($value)) {
                    $fields[] = "MotDePasse = :MotDePasse";
                    $params[':MotDePasse'] = password_hash($value, PASSWORD_DEFAULT);
                } elseif ($key !== 'id') {
                    $fields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }

            $sql = "UPDATE utilisateur SET " . implode(', ', $fields) . " WHERE ID = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Update user error: " . $e->getMessage());
            return false;
        }
    }

    // Admin Methods
    public function fetchAdminByEmail($email) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM admins WHERE Email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Fetch admin error: " . $e->getMessage());
            return false;
        }
    }

    public function insertAdmin($data) {
        try {
            $sql = "INSERT INTO admins 
                    (adminname, Email, MotDePasse) 
                    VALUES 
                    (:adminname, :Email, :MotDePasse)";
            
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':adminname'  => $data['adminname'],
                ':Email'      => $data['Email'],
                ':MotDePasse' => password_hash($data['MotDePasse'], PASSWORD_DEFAULT)
            ]);
        } catch (PDOException $e) {
            error_log("Insert admin error: " . $e->getMessage());
            return false;
        }
    }

    // General Query Methods
    public function fetchOne($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Fetch one error: " . $e->getMessage());
            return false;
        }
    }

    public function executeQuery($query, $parameters = []) {
        try {
            $stmt = $this->conn->prepare($query);
            foreach ($parameters as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Query error: " . $e->getMessage());
            return false;
        }
    }
}