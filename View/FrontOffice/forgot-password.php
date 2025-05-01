<?php
require_once dirname(__DIR__) . '/../controller/FunctionsController.php';
require_once dirname(__DIR__) . '/../config.php';

$controller = new FunctionsController($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    $recaptcha_secret = '6LeTQJgqAAAAAPJXfbR3qkxoDvSn4SLw0KGzfiRH';
    $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
    $response = file_get_contents($verify_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $response_data = json_decode($response, true);

    if ($response_data['success'] ?? false) {
        // reCAPTCHA verification successful
        if ($controller->processForgotPassword($email)) {
            echo "<script>alert('Password reset instructions sent to your email');</script>";
        } else {
            echo "<script>alert('Email not found');</script>";
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
    <title>Forgot Password | StartUp Connect</title>
    <!-- Using the same CSS as login.php -->
    <link rel="stylesheet" href="../../css/styleLogin.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="background">
    <!-- Login Container with same styling as login.php -->
    <div class="login-container">
        <h2>Forgot Password</h2>
        <form action="forgot-password.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <!-- Google reCAPTCHA widget -->
            <div class="g-recaptcha" data-sitekey="6LeTQJgqAAAAAGM7sDa67YT6NonrAE4WqIYpkWE3"></div>

            <button type="submit" class="btn">Send Reset Link</button>

            <!-- Links styled like login.php -->
            <p class="forgot-password">
                <a href="login.php">Back to Login</a>
            </p>
            <p class="register-link">
                Don't have an account? <a href="register.php">Register</a>
            </p>
        </form>
    </div>
</body>
</html>