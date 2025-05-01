<?php
<<<<<<< HEAD
require_once dirname(__DIR__, 3) . '/controller/FunctionsController.php'; // Corrected path
require_once dirname(__DIR__, 3) . '/config.php'; // Corrected path
require_once dirname(__DIR__, 1) . '/layouts/header.php'; // Corrected path

// Ensure the admin is logged in
=======
require_once dirname(__DIR__, 3) . '/controller/FunctionsController.php';
require_once dirname(__DIR__, 3) . '/config.php'; 
require_once dirname(__DIR__, 1) . '/layouts/header.php'; 

>>>>>>> be2b30b (dali)
if (!isset($_SESSION['adminname'])) {
    header("Location: " . ADMINURL . "/admins/login-admins.php");
    exit();
}

<<<<<<< HEAD
// Fetch topics from the database
try {
    $topics = $conn->query("SELECT * FROM topics");
    $allTopics = $topics->fetchAll(PDO::FETCH_OBJ);
=======

$searchTerm = '';
$allTopics = [];

try {

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['search'])) {
        $searchTerm = trim($_POST['search']);
        $query = "SELECT * FROM topics WHERE title LIKE :search OR user_name LIKE :search";
        $stmt = $conn->prepare($query);
        $stmt->execute([':search' => "%$searchTerm%"]);
    } else {
        $stmt = $conn->query("SELECT * FROM topics");
    }

    $allTopics = $stmt->fetchAll(PDO::FETCH_OBJ);
>>>>>>> be2b30b (dali)
} catch (PDOException $e) {
    echo "Error fetching topics: " . $e->getMessage();
}
?>

<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-4 d-inline">Topics</h5>
<<<<<<< HEAD
=======
        <form method="POST" class="mb-4">
          <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by title or user" value="<?php echo htmlspecialchars($searchTerm); ?>">
            <div class="input-group-append">
              <button type="submit" class="btn btn-primary">Search</button>
            </div>
          </div>
        </form>
>>>>>>> be2b30b (dali)
        <table class="table mt-4">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Title</th>
              <th scope="col">Category</th>
              <th scope="col">User</th>
              <th scope="col">Delete</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($allTopics)): ?>
              <?php foreach ($allTopics as $topic): ?>
                <tr>
                  <th scope="row"><?php echo htmlspecialchars($topic->id); ?></th>
                  <td><?php echo htmlspecialchars($topic->title); ?></td>
                  <td><?php echo htmlspecialchars($topic->category); ?></td>
                  <td><?php echo htmlspecialchars($topic->user_name); ?></td>
                  <td>
                    <a href="delete-topic.php?id=<?php echo htmlspecialchars($topic->id); ?>" class="btn btn-danger text-center">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center">No topics found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require_once dirname(__DIR__, 1) . '/layouts/footer.php'; ?>
