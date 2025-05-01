<?php
// Corrected path to FunctionsController.php
require_once dirname(__DIR__, 3) . '/controller/FunctionsController.php';

// Start session
session_start();

// Unset and destroy session variables
session_unset();
session_destroy();

// Redirect to the login page
header("Location: http://localhost/utls/View/backoffice/admins/login-admins.php");
exit();
?>
