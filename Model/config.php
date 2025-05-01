<?php 
class config {
    private static $pdo = null; 
    
    public static function getConnexion() {
        if (!isset(self::$pdo)) {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "user";
            
            try { 
                self::$pdo = new PDO(
                    "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
                    $username, 
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]  
                );
            } catch (Exception $e) {
                die('Erreur de connexion: '. $e->getMessage());
            }
        }
        return self::$pdo;
    }
}

// Test de connexion
try {
    $pdo = config::getConnexion();
    echo "Connexion réussie !";
} catch (PDOException $e) {
    echo "Échec de connexion : " . $e->getMessage();
}
?>