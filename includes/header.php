<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Food Ordering System</title>

  <!-- CSS -->
  <link rel="stylesheet" href="style.css">

  <!-- JS -->
  <script src="script.js" defer></script>
</head>
<body>
  <header>
    <nav class="navbar">
      <div class="logo">
        <a href="index.php">🍔 Foodie</a>
      </div>
      <ul class="nav-links">
        <li><a href="menu.php">Menu</a></li>
        <li><a href="cart.php">Cart <span id="cart-count">0</span></a></li>
        <li><a href="orders.php">Orders</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
      <div class="menu-toggle">☰</div> <!-- for mobile toggle -->
    </nav>
  </header>

  <main>
