<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/controller/FunctionsController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['adminname'])) {
    header("Location: " . ADMINURL . "/admins/login-admins.php");
    exit();
}

$controller = new FunctionsController($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'adminname' => $_POST['adminname'] ?? '',
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
    ];

    $result = $controller->createAdmin($data);

    if ($result['success']) {
        header("Location: admins.php");
        exit();
    } else {
        echo "<script>alert('" . htmlspecialchars($result['message']) . "');</script>";
    }
}
?>

<?php require "../layouts/header.php"; ?>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-5 d-inline">Create Admins</h5>
                <form id="adminForm" method="POST" action="">
                    <!-- Admin Name Input -->
                    <div class="form-outline mb-4 mt-4">
                        <input type="text" id="adminname" name="adminname" class="form-control" placeholder="Admin name" required>
                    </div>
                    <!-- Email Input -->
                    <div class="form-outline mb-4">
                        <input type="text" id="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <!-- Password Input -->
                    <div class="form-outline mb-4">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary mb-4 text-center">Create</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require "../layouts/footer.php"; ?>

<script>
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    const form = document.getElementById('adminForm');
    const adminNameInput = document.getElementById('adminname');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    form.addEventListener('submit', function (event) {
        const adminName = adminNameInput.value.trim();
        const email = emailInput.value.trim();
        const password = passwordInput.value;

        if (adminName.length < 3) {
            alert('Admin name must be at least 3 characters long.');
            event.preventDefault(); // Prevent form submission
            return;
        }

        if (!emailRegex.test(email)) {
            alert('Please enter a valid email address.');
            event.preventDefault(); // Prevent form submission
            return;
        }

        if (password.length < 8 || password.length > 20) {
            alert('Password must be between 8 and 20 characters long.');
            event.preventDefault(); // Prevent form submission
            return;
        }
    });
</script>
