<?php
include("includes/db_connect.php");

// Select all users
$sql = "SELECT id, password FROM users";
$result = $conn->query($sql);

while ($user = $result->fetch_assoc()) {
    $id = $user['id'];
    $plainPassword = $user['password'];

    // Hash the password
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    // Update the user record
    $update = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $update->bind_param("si", $hashedPassword, $id);
    $update->execute();
}

echo "All passwords have been hashed successfully.";
$conn->close();
?>
