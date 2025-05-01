<?php
ob_start(); // Start output buffering
// Include necessary files
require_once dirname(__DIR__, 3) . '/config.php'; // Ensure this initializes $conn
require_once dirname(__DIR__, 3) . '/controller/FunctionsController.php';
require_once dirname(__DIR__, 1) . '/layouts/header.php';

// Check if ID is provided in the query string
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize ID as integer

    try {
        // Prepare and execute the DELETE query
        $delete = $conn->prepare("DELETE FROM categories WHERE id = :id");
        $delete->bindParam(':id', $id, PDO::PARAM_INT);

        if ($delete->execute()) {
            // Redirect to show-categories.php with a success message
            header("Location: show-categories.php?message=Category+Deleted");
            exit();
        } else {
            echo "Failed to delete the category.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No category ID provided.";
}

require_once dirname(__DIR__, 1) . '/layouts/footer.php';
ob_end_flush(); // End output buffering
