<?php
require_once dirname(__DIR__) . '/../controller/FunctionsController.php';
require_once dirname(__DIR__) . '/../config.php';

session_start();
$controller = new FunctionsController($conn);
$message = '';
$tokenValid = false;
$token = $_GET['token'] ?? '';

// Validate token
if (!empty($token)) {
    $user = $controller->model->fetchOne(
        "SELECT id, reset_expiry FROM utilisateur 
        WHERE reset_token = :token AND reset_expiry > NOW()",
        [':token' => hash('sha256', $token)]
    );
    
    $tokenValid = (bool)$user;
    if (!$tokenValid) {
        $message = "Lien invalide ou expiré. Veuillez demander un nouveau lien.";
        error_log("Invalid token attempt: $token");
    }
}

// Process password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postToken = $_POST['token'] ?? '';
    $user = $controller->model->fetchOne(
        "SELECT id FROM utilisateur 
        WHERE reset_token = :token AND reset_expiry > NOW()",
        [':token' => hash('sha256', $postToken)]
    );

    if ($user) {
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Password validation
        $errors = [];
        if (strlen($password) < 8) $errors[] = "8 caractères minimum";
        if (!preg_match('/[A-Z]/', $password)) $errors[] = "1 majuscule";
        if (!preg_match('/[0-9]/', $password)) $errors[] = "1 chiffre";
        if (!preg_match('/[\W]/', $password)) $errors[] = "1 caractère spécial";
        if ($password !== $confirmPassword) $errors[] = "Correspondance des mots de passe";

        if (empty($errors)) {
            if ($controller->resetPassword($postToken, $password)) {
                $message = "Mot de passe réinitialisé avec succès. <a href='login.php'>Se connecter</a>";
                $tokenValid = false;
                error_log("Password reset for user ID: {$user['id']}");
            } else {
                $message = "Erreur de base de données. Veuillez réessayer.";
            }
        } else {
            $message = "Erreurs de mot de passe :<br>- " . implode("<br>- ", $errors);
        }
    } else {
        $message = "Session invalide. Veuillez redémarrer le processus.";
        header("Location: forgot-password.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation mot de passe | StartupConnect</title>
    <link rel="stylesheet" href="../../css/styleLogin.css">
    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</head>
<body class="background">
    <div class="login-container">
        <h2>Réinitialisation du mot de passe</h2>
        
        <?php if ($message): ?>
            <div class="alert <?= strpos($message, "succès") !== false ? 'alert-success' : 'alert-danger' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <?php if ($tokenValid): ?>
            <form method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                
                <div class="password-field">
                    <label for="password">Nouveau mot de passe :</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" 
                               required pattern="^(?=.*[A-Z])(?=.*\d)(?=.*[\W]).{8,}$"
                               title="8 caractères minimum avec 1 majuscule, 1 chiffre et 1 caractère spécial">
                        <button type="button" class="toggle-password" 
                                onclick="togglePassword('password')">👁️</button>
                    </div>
                </div>

                <div class="password-field">
                    <label for="confirm_password">Confirmation :</label>
                    <div class="input-group">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <button type="button" class="toggle-password" 
                                onclick="togglePassword('confirm_password')">👁️</button>
                    </div>
                </div>

                <button type="submit" class="btn">Réinitialiser</button>
            </form>
        <?php else: ?>
            <div class="reset-links">
                <a href="forgot-password.php" class="link-button">Nouveau lien</a>
                <a href="login.php" class="link-button">Connexion</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>