<?php
require_once dirname(__DIR__, 3) . '/controller/FunctionsController.php';
require_once dirname(__DIR__, 3) . '/config.php';

// Initialize the controller and fetch replies
$controller = new FunctionsController($conn);
$replies = $controller->listReplies(); 
?>
<link href="http://localhost/utls/assets/css/custom.css" rel="stylesheet">

<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-4 d-inline">Replies</h5>
        <table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Reply</th>
              <th scope="col">User Image</th>
              <th scope="col">User Name</th>
              <th scope="col">Go to Topic</th>
              <th scope="col">Delete</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($replies as $reply) : ?>
              <tr>
                <th scope="row"><?php echo htmlspecialchars($reply['id']); ?></th>
                <td><?php echo htmlspecialchars($reply['reply']); ?></td>
                <td>
                  <img src="<?php echo htmlspecialchars($reply['user_image']); ?>" alt="User Image" style="width: 50px; height: 50px; border-radius: 50%;">
                </td>
                <td><?php echo htmlspecialchars($reply['user_name']); ?></td>
                <td>
                  <a href="http://localhost/forum/topics/topic.php?id=<?php echo htmlspecialchars($reply['topic_id']); ?>" class="btn btn-success text-center">
                    Go to Topic
                  </a>
                </td>
                <td>
                  <a href="delete-replies.php?id=<?php echo htmlspecialchars($reply['id']); ?>" class="btn btn-danger text-center">
                    Delete
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php require "../layouts/footer.php"; ?>
