<?php
ob_start();
require_once dirname(__DIR__, 3) . '/controller/FunctionsController.php'; // Corrected path
require_once dirname(__DIR__, 3) . '/config.php'; // Corrected path
require_once dirname(__DIR__, 1) . '/layouts/header.php'; // Corrected path

// Ensure the admin is logged in
if (!isset($_SESSION['adminname'])) {
    header("Location: " . ADMINURL . "/admins/login-admins.php");
    exit();
}

// Handle form submission
if (isset($_POST['submit'])) {
    if (empty($_POST['name'])) {
        echo "<script>alert('One or more inputs are empty');</script>";
    } else {
        $name = htmlspecialchars(trim($_POST['name'])); // Sanitize input

        try {
            // Insert new category into the database
            $insert = $conn->prepare("INSERT INTO categories (name) VALUES (:name)");
            $insert->execute([":name" => $name]);

            // Redirect to show-categories.php
            header("Location: show-categories.php?message=Category+Created");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-5 d-inline">Create Categories</h5>
        <form method="POST" action="create-category.php">
          <!-- Name input -->
          <div class="form-outline mb-4 mt-4">
            <input type="text" name="name" id="form2Example1" class="form-control" placeholder="Category Name" />
          </div>
          <!-- Submit button -->
          <button type="submit" name="submit" class="btn btn-primary mb-4 text-center">Create</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once dirname(__DIR__, 1) . '/layouts/footer.php'; ?>
