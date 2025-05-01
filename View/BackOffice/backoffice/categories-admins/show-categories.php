<?php
// Include configuration and controller
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/controller/FunctionsController.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if admin is not logged in
if (!isset($_SESSION['adminname'])) {
    header("Location: " . ADMINURL . "/admins/login-admins.php");
    exit();
}

// Fetch categories from the database
try {
    $categories = $conn->query("SELECT * FROM categories");
    $allCategories = $categories->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    die("Error fetching categories: " . $e->getMessage());
}

?>

<?php require_once dirname(__DIR__, 1) . '/layouts/header.php'; ?>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4 d-inline">Categories</h5>
                <a href="<?php echo ADMINURL; ?>/categories-admins/create-category.php" class="btn btn-primary mb-4 float-right">Create Categories</a>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Update</th>
                            <th scope="col">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allCategories as $category): ?>
                            <tr>
                                <th scope="row"><?php echo htmlspecialchars($category->id); ?></th>
                                <td><?php echo htmlspecialchars($category->name); ?></td>
                                <td>
                                    <a href="<?php echo ADMINURL; ?>/categories-admins/update-category.php?id=<?php echo htmlspecialchars($category->id); ?>" class="btn btn-warning text-white">Update</a>
                                </td>
                                <td>
                                    <a href="<?php echo ADMINURL; ?>/categories-admins/delete-category.php?id=<?php echo htmlspecialchars($category->id); ?>" class="btn btn-danger">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__, 1) . '/layouts/footer.php'; ?>