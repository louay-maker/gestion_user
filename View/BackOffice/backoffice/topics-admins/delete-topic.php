<?php
ob_start();
require_once dirname(__DIR__, 3) . '/controller/FunctionsController.php'; // Corrected path
require_once dirname(__DIR__, 3) . '/config.php'; // Corrected path
require_once dirname(__DIR__, 1) . '/layouts/header.php'; // Corrected path

// Check if the admin is logged in
if (!isset($_SESSION['adminname'])) {
    header("Location: " . ADMINURL . "/admins/login-admins.php");
    exit();
}

// Check if the ID is provided in the query string
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize ID as an integer

    try {
        // Use prepared statements to securely delete the topic
        $delete = $conn->prepare("DELETE FROM topics WHERE id = :id");
        $delete->bindParam(':id', $id, PDO::PARAM_INT);

        if ($delete->execute()) {
            header("Location: show-topics.php?message=Topic+Deleted");
            exit();
        } else {
            echo "Failed to delete the topic.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No topic ID provided.";
}
?>

<?php require_once dirname(__DIR__, 1) . '/layouts/footer.php'; ?>
