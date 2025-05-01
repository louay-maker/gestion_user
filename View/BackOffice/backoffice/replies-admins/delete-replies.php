<<<<<<< HEAD
<?php require '../../controllers/functions.php'; ?>
<?php require "../layouts/header.php"; ?>
<?php require "../../config/config.php"; ?>
<?php
=======

<?php
require_once dirname(__DIR__, 3) . '/controller/FunctionsController.php';
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 1) . '/header.php';


>>>>>>> be2b30b (dali)
if (!isset($_SESSION['adminname'])) {
    header("location: " . ADMINURL . "/admins/login-admins.php");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $delete = $conn->query("DELETE FROM replies WHERE id = '$id' ");
    $delete->execute();
    header("location: show-replies.php");
}
?>
<?php require "../layouts/footer.php"; ?>
