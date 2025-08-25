<?php
session_start();

// Clear all session data
session_unset();
session_destroy();

// Redirect admin to admin login page
header("Location: login.html");
exit();
?>
