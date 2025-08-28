<?php
session_start();
session_unset();
session_destroy();

// Start a new session to store the logout message
session_start();
$_SESSION['logout_success'] = "You have been logged out successfully.";
header("Location: ../login.php");
exit();
?>
