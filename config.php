<?php
try {
    // Define constants only if they are not already defined
    if (!defined("HOST")) {
        define("HOST", "localhost");
    }
    if (!defined("DBNAME")) {
        define("DBNAME", "user");
    }
    if (!defined("USER")) {
        define("USER", "root");
    }
    if (!defined("PASS")) {
        define("PASS", "");
    }
    if (!defined("ADMINURL")) {
        define("ADMINURL", "http://localhost/utls/View/backoffice");
    }

    // Establish the database connection
    $conn = new PDO("mysql:host=" . HOST . ";dbname=" . DBNAME, USER, PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $Exception) {
    die("Database connection failed: " . $Exception->getMessage());
}
