<?php
// Start session
session_start();

// Redirect if user is already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/menu.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Ordering System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 100px;
            background-color: #f8f8f8;
        }
        a {
            display: inline-block;
            margin: 15px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background-color: #218838;
        }
        h1 {
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Welcome to Food Ordering System</h1>
    <p>Please choose an option below:</p>
    <a href="register.html">Register</a>
    <a href="login.html">Login</a>
    <a href="#">Browse Menu</a> <!-- Link later to menu page -->
</body>
</html>
