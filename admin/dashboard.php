<?php
session_start();
include("../includes/db_connect.php");

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch stats
$total_users = $conn->query("SELECT COUNT(*) AS total_users FROM users")->fetch_assoc()['total_users'];
$total_orders = $conn->query("SELECT COUNT(*) AS total_orders FROM orders")->fetch_assoc()['total_orders'];
$pending_orders = $conn->query("SELECT COUNT(*) AS pending_orders FROM orders WHERE status='Pending'")->fetch_assoc()['pending_orders'];
$total_revenue = $conn->query("SELECT SUM(total_amount) AS total_revenue FROM orders WHERE status IN ('Confirmed','Delivered')")->fetch_assoc()['total_revenue'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>MealMate Admin Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body, html {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f5f6fa;
}

/* Navbar */
.navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: #222;
    padding: 10px 20px;
    color: white;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
}
.logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.8rem;
    font-weight: bold;
    color: white;
    text-decoration: none;
}
.logo img { height: 60px; width: 60px; object-fit: contain; }
.nav-links { display: flex; gap: 20px; }
.nav-links a {
    color: #ffc107;
    font-weight: bold;
    text-decoration: none;
    transition: 0.3s;
}
.nav-links a:hover { color: #fff; }

/* Page Container */
.container { padding: 30px; max-width: 1200px; margin: auto; }
h1 {
    text-align: center;
    margin-bottom: 40px;
    font-size: 2.5rem;
    color: #333;
}

/* Cards */
.cards {
    display: flex;
    flex-wrap: wrap;
    gap: 25px;
    justify-content: center;
}
.card-link {
    text-decoration: none;
    flex: 1 1 250px;
    max-width: 250px;
}
.card {
    padding: 30px;
    border-radius: 12px;
    color: #fff;
    text-align: center;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    position: relative;
}
.card:hover { transform: translateY(-8px); box-shadow: 0 12px 25px rgba(0,0,0,0.2); }
.card h2 { margin: 0 0 10px 0; font-size: 2rem; }
.card p { margin: 0; font-size: 1.1rem; }
.card i { position: absolute; top: 15px; right: 15px; font-size: 40px; opacity: 0.15; }

/* Card Colors */
.users { background: linear-gradient(135deg,#2980b9,#3498db); }
.orders { background: linear-gradient(135deg,#d35400,#e67e22); }
.pending { background: linear-gradient(135deg,#f39c12,#f1c40f); color: #333; }
.revenue { background: linear-gradient(135deg,#27ae60,#2ecc71); }
.menu { background: linear-gradient(135deg,#8e44ad,#9b59b6); }
.restaurants { background: linear-gradient(135deg,#c0392b,#e74c3c); }

/* Responsive */
@media (max-width: 768px) {
    .cards { flex-direction: column; align-items: center; }
    .card-link { max-width: 90%; }
    h1 { font-size: 2rem; }
    .logo { font-size: 1.5rem; }
    .logo img { height: 45px; width: 45px; }
    .nav-links { flex-direction: column; gap: 10px; }
}
</style>
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <a href="dashboard.php" class="logo">
            <img src="../assets/images/logo.png" alt="MealMate Logo">
            MealMate
        </a>
        <div class="nav-links">
            <a href="../index.php">Home</a> 
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </header>

    <div class="container">
        <h1>Admin Dashboard</h1>
        <div class="cards">
            <a class="card-link" href="manage_users.php">
                <div class="card users"><i class="fas fa-users"></i><h2><?= $total_users ?></h2><p>Manage Users</p></div>
            </a>
            <a class="card-link" href="manage_orders.php">
                <div class="card orders"><i class="fas fa-box-open"></i><h2><?= $total_orders ?></h2><p>Manage Orders</p></div>
            </a>
            <a class="card-link" href="pending_orders.php">
                <div class="card pending"><i class="fas fa-clock"></i><h2><?= $pending_orders ?></h2><p>Pending Orders</p></div>
            </a>
            <a class="card-link" href="revenue.php">
                <div class="card revenue"><i class="fas fa-money-bill-wave"></i><h2>Rs <?= number_format($total_revenue, 2) ?></h2><p>Total Revenue</p></div>
            </a>
            <a class="card-link" href="manage_menu.php">
                <div class="card menu"><i class="fas fa-utensils"></i><h2>Menu</h2><p>Manage Menu Items</p></div>
            </a>
            <a class="card-link" href="manage_restaurants.php">
                <div class="card restaurants"><i class="fas fa-store"></i><h2>Restaurants</h2><p>Manage Restaurants</p></div>
            </a>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
