<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to home page (e.g., login.php or index.php)
header("Location: main_Page.php"); // Change to "index.php" if your homepage is different
exit();
?>
