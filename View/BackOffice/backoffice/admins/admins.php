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
$allAdmins = $controller->listAdmins();
?>
<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-4 d-inline">Admins</h5>
        <a href="<?php echo ADMINURL; ?>/admins/create-admins.php" class="btn btn-primary mb-4 text-center float-right">Create Admins</a>
        <table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Admin Name</th>
              <th scope="col">Email</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($allAdmins)): ?>
              <?php foreach ($allAdmins as $admin) : ?>
                <tr>
                  <th scope="row"><?php echo htmlspecialchars($admin['id']); ?></th>
                  <td><?php echo htmlspecialchars($admin['adminname']); ?></td>
                  <td><?php echo htmlspecialchars($admin['email']); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="3">No admins found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>