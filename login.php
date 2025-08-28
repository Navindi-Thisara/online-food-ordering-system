<?php
session_start();
include("includes/db_connect.php");

$error = '';
$success = '';

// Show logout message if redirected from logout
if (!empty($_SESSION['logout_success'])) {
    $success = $_SESSION['logout_success'];
    unset($_SESSION['logout_success']);
}

// Process login
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($stmt = $conn->prepare("SELECT * FROM users WHERE email = ?")) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                header($user['role'] === 'admin' 
                    ? "Location: admin/dashboard.php" 
                    : "Location: user/menu.php");
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Email not registered.";
        }

        $stmt->close();
    } else {
        $error = "Database error: Unable to prepare statement.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - MealMate</title>
<link rel="stylesheet" href="style.css">
<style>
body {
    font-family: Arial, sans-serif;
    background: url('assets/images/home-bg.png') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    color: white;
    margin: 0;
}
.container {
    background-color: rgba(0,0,0,0.6);
    padding: 30px;
    border-radius: 10px;
    text-align: center;
    width: 300px;
}
input[type="email"], input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    border: none;
}
button {
    width: 100%;
    padding: 12px;
    background-color: #007bff;
    color: white;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px;
}
button:hover { background-color: #0056b3; }
.register-link {
    display: block;
    margin-top: 15px;
    color: #ffc107;
    text-decoration: none;
    font-weight: bold;
}
.register-link:hover { color: #e0a800; }
.error { color: #ff6961; margin-bottom: 15px; }
.success { color: #7CFC00; margin-bottom: 15px; }
</style>
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form action="" method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
    <a href="register.php" class="register-link">Don't have an account? Register</a>
</div>
</body>
</html>
