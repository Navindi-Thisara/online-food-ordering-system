<?php
session_start();
include("../includes/db_connect.php");

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

// Fetch Total Users
$total_users = $conn->query("SELECT COUNT(*) AS total_users FROM users")->fetch_assoc()['total_users'];

// Fetch Total Orders
$total_orders = $conn->query("SELECT COUNT(*) AS total_orders FROM orders")->fetch_assoc()['total_orders'];

// Fetch Pending Orders
$pending_orders = $conn->query("SELECT COUNT(*) AS pending_orders FROM orders WHERE status='Pending'")->fetch_assoc()['pending_orders'];

// Fetch Total Revenue
$total_revenue = $conn->query("SELECT SUM(total_amount) AS total_revenue FROM orders WHERE status IN ('Confirmed','Delivered')")->fetch_assoc()['total_revenue'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f9f9f9; }
        h1 { margin-bottom: 20px; }
        a.logout { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #333; font-weight: bold; }

        .cards { display: flex; gap: 20px; flex-wrap: wrap; }

        .card-link { text-decoration: none; flex: 1 1 200px; }
        .card {
            padding: 25px;
            border-radius: 10px;
            color: #fff;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            position: relative;
        }
        .card:hover { transform: translateY(-5px); }

        .card h2 { margin: 0 0 10px; font-size: 28px; }
        .card p { margin: 0; font-size: 18px; }
        .card i { position: absolute; top: 15px; right: 15px; font-size: 40px; opacity: 0.2; }

        .users { background-color: #3498db; }
        .orders { background-color: #e67e22; }
        .pending { background-color: #f1c40f; color: #333; }
        .revenue { background-color: #2ecc71; }

        @media (max-width: 768px) {
            .cards { flex-direction: column; }
        }
    </style>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <a class="logout" href="../logout.php">Logout</a>

    <div class="cards">
        <a class="card-link" href="manage_users.php">
            <div class="card users">
                <i class="fas fa-users"></i>
                <h2><?php echo $total_users; ?></h2>
                <p>Total Users</p>
            </div>
        </a>

        <a class="card-link" href="manage_orders.php">
            <div class="card orders">
                <i class="fas fa-box-open"></i>
                <h2><?php echo $total_orders; ?></h2>
                <p>Total Orders</p>
            </div>
        </a>

        <a class="card-link" href="manage_orders.php?status=Pending">
            <div class="card pending">
                <i class="fas fa-clock"></i>
                <h2><?php echo $pending_orders; ?></h2>
                <p>Pending Orders</p>
            </div>
        </a>

        <a class="card-link" href="manage_orders.php">
            <div class="card revenue">
                <i class="fas fa-money-bill-wave"></i>
                <h2>Rs <?php echo number_format($total_revenue, 2); ?></h2>
                <p>Total Revenue</p>
            </div>
        </a>
    </div>
</body>
</html>

<?php $conn->close(); ?>
