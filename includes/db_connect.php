<?php

$host = "localhost:3307";        
$user = "root";             
$pass = "";                 
$dbname = "food_ordering_system";  

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
