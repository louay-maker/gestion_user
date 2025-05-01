<?php
require_once dirname(__DIR__) . '/../controller/FunctionsController.php';
require_once dirname(__DIR__) . '/../config.php';

$controller = new FunctionsController($conn);

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    if (!empty($token) && !empty($newPassword)) {
        $message = $controller->resetPassword($token, $newPassword);
    } else {
        $message = "Please fill in all fields.";
    }
} else if (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    $message = "Invalid or missing token.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="./assets/css/reset-password.css">
</head>
<body>
    <div class="reset-password-container">
        <h2>Reset Password</h2>
        <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <?php if (!isset($message) || strpos($message, 'successfully') === false): ?>
        <form action="reset-password.php" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" placeholder="Enter your new password" required>

            <button type="submit">Reset Password</button>
        </form>
        <?php endif; ?>

        <a href="login.php">Back to Login</a>
    </div>
</body>
</html>
