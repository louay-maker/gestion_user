<?php
require_once dirname(__DIR__, 2) . '/controller/FunctionsController.php';
require_once dirname(__DIR__, 2) . '/config.php';

$controller = new FunctionsController($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'Nom' => $_POST['Nom'] ?? '',
        'Prenom' => $_POST['Prenom'] ?? '',
        'Email' => $_POST['Email'] ?? '',
        'MotDePasse' => $_POST['MotDePasse'] ?? '',
    ];
    
    $controller->registerUser($data);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription | StartUp Connect</title>
    <!-- Using the same CSS as login.php -->
    <link rel="stylesheet" href="../../css/styleLogin.css">
    <style>
        #passwordStrength {
            font-weight: bold;
            margin-top: 5px;
            margin-bottom: 15px;
        }
        .strength-weak { color: red; }
        .strength-medium { color: orange; }
        .strength-strong { color: green; }
    </style>
</head>
<body class="background">
    <!-- Register Container with same styling as login.php -->
    <div class="login-container">
        <h2>Inscription</h2>
        <form id="registerForm" method="POST" action="register.php" enctype="multipart/form-data">
            <label for="Nom">Nom:</label>
            <input type="text" name="Nom" placeholder="Votre nom" required>
            
            <label for="Prenom">Prénom:</label>
            <input type="text" name="Prenom" placeholder="Votre prénom" required>
            
            <label for="Email">Email:</label>
            <input type="email" id="Email" name="Email" placeholder="exemple@email.com" required>
            
            <label for="MotDePasse">Mot de passe:</label>
            <input type="password" id="MotDePasse" name="MotDePasse" placeholder="••••••••" required>
            <div id="passwordStrength"></div>

            <button type="submit" class="btn">S'inscrire</button>

            <!-- Links styled like login.php -->
            <p class="register-link">
                Déjà inscrit? <a href="login.php">Se connecter</a>
            </p>
        </form>
    </div>

    <script>
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const form = document.getElementById('registerForm');
        const emailInput = document.getElementById('Email');
        const passwordInput = document.getElementById('MotDePasse');
        const passwordStrengthDiv = document.getElementById('passwordStrength');

        passwordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[@$!%*?&#]/.test(password)) strength++;

            if (strength === 0) {
                passwordStrengthDiv.textContent = "";
            } else if (strength <= 2) {
                passwordStrengthDiv.textContent = "Faible";
                passwordStrengthDiv.className = "strength-weak";
            } else if (strength === 3) {
                passwordStrengthDiv.textContent = "Moyen";
                passwordStrengthDiv.className = "strength-medium";
            } else {
                passwordStrengthDiv.textContent = "Fort";
                passwordStrengthDiv.className = "strength-strong";
            }
        });

        form.addEventListener('submit', function(event) {
            const email = emailInput.value;
            const password = passwordInput.value;

            if (!emailRegex.test(email)) {
                alert('Veuillez entrer une adresse email valide.');
                event.preventDefault();
                return;
            }

            if (password.length < 8 || password.length > 20) {
                alert('Le mot de passe doit contenir entre 8 et 20 caractères.');
                event.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>