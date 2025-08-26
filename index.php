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
    <title>MealMate - Food Ordering System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
        }

        body {
            background: url('assets/images/home-bg.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            position: relative;
        }

        /* Dark overlay for readability */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 0;
        }

        /* Header with logo */
        .header {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            align-items: center;
            padding: 10px 20px;
            z-index: 1;
        }
        /* Navbar container */
        .navbar {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            background-color: rgba(0,0,0,0.8); 
            padding: 10px 20px;
            position: fixed;  
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 2rem;
            font-weight: bold;
            color: white;
            gap: 10px;
        }

        .logo img {
            height: 70px;
            width: 70px;
            object-fit: contain;
        }

        /* Mobile */
        @media (max-width: 768px) {
            .logo {
                font-size: 1.5rem;
            }
            .logo img {
                height: 50px;
                width: 50px;
            }
        }

        .container {
            position: relative;
            z-index: 1;
        }

        h1 {
            font-size: 3rem;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
            margin-bottom: 20px;
        }

        p {
            font-size: 1.5rem;
            margin-bottom: 40px;
            text-shadow: 1px 1px 6px rgba(0,0,0,0.6);
        }

        .btn {
            display: inline-block;
            margin: 10px;
            padding: 15px 30px;
            font-size: 1.2rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.3s;
        }

        .btn-register { background-color: #28a745; }
        .btn-register:hover { background-color: #218838; }

        .btn-login { background-color: #007bff; }
        .btn-login:hover { background-color: #0056b3; }

        .btn-browse { background-color: #ffc107; color: #333; }
        .btn-browse:hover { background-color: #e0a800; }

        @media (max-width: 768px) {
            h1 { font-size: 2.2rem; }
            p { font-size: 1.2rem; }
            .btn { font-size: 1rem; padding: 12px 25px; }
            .logo { font-size: 1.5rem; }
            .logo img { height: 40px; width: 40px; }
        }
    </style>
</head>
<body>
    <!-- Header/Navbar -->
    <header class="navbar">
    <div class="logo">
        <img src="assets/images/logo.png" alt="MealMate Logo">
        <span>MealMate</span>
    </div>
    </header>


    <div class="container">
        <h1>Welcome to MealMate</h1>
        <p>Delicious food delivered to your doorstep!</p>
        <a href="register.php" class="btn btn-register">Register</a>
        <a href="login.php" class="btn btn-login">Login</a>
        <a href="user/menu.php" class="btn btn-browse">Browse Menu</a>
    </div>
</body>
</html>
