<?php
require_once dirname(__DIR__) . '/../controller/FunctionsController.php';
require_once dirname(__DIR__) . '/../config.php';

$controller = new FunctionsController($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    $recaptcha_secret = '6LeTQJgqAAAAAPJXfbR3qkxoDvSn4SLw0KGzfiRH';
    $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
    $response = file_get_contents($verify_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $response_data = json_decode($response, true);

    if ($response_data['success'] ?? false) {
        if ($controller->loginUser($email, $password)) {
            header("Location: ../backoffice/index.php");
            exit();
        } else {
            echo "<script>alert('Invalid email or password');</script>";
        }
    } else {
        echo "<script>alert('Captcha verification failed');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | StartUp Connect</title>
    <!-- Use the French template's CSS -->
    <link rel="stylesheet" href="../../css/styleLogin.css">
    <!-- reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="background">
    <!-- Login Container (matches template design) -->
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <!-- reCAPTCHA -->
            <div class="g-recaptcha" data-sitekey="6LeTQJgqAAAAAGM7sDa67YT6NonrAE4WqIYpkWE3"></div>

            <button type="submit" class="btn">Login</button>

            <!-- Links (styled like the template but in English) -->
            <div class="links">
                <a href="register.php">Don't have an account? Register</a>
                <a href="forgot-password.php">Forgot your password?</a>
            </div>
        </form>
    </div>
</body>
</html>