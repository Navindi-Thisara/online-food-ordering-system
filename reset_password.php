<?php
include("includes/db_connect.php");

// New password 
$newPassword = 'Nethu@123'; 
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Email of the user whose password you want to reset
$email = 'nethupula123@gmail.com'; 

// Update password in database
$sql = "UPDATE users SET password=? WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $hashedPassword, $email);

if ($stmt->execute()) {
    echo "Password has been reset successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$conn->close();
?>
