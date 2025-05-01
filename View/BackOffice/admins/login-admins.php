<?php
require_once dirname(__DIR__, 3) . '/controller/FunctionsController.php';
require_once dirname(__DIR__, 3) . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['adminname'])) {
    header("Location: " . ADMINURL);
    exit();
}
$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new FunctionsController($conn);
    $_POST['action'] = 'loginAdmin'; // Set the action for handleRequest
    $result = $controller->handleRequest($_POST);

    if ($result) {
        header("Location: " . ADMINURL);
        exit();
    } else {
        $errorMessage = 'Invalid credentials';
    }
}
?>
<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mt-5">Admin Login</h5>

                <?php if (!empty($errorMessage)) : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <!-- Email input -->
                    <div class="form-outline mb-4">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <!-- Password input -->
                    <div class="form-outline mb-4">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <!-- Submit button -->
                    <button type="submit" class="btn btn-primary mb-4">Login</button>
                </form>
            </div>
        </div>
    </div>
</div><?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>