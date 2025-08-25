<?php
session_start();
include('../includes/db_connect.php');

// Ensure only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

// Initialize variables
$start_date = $end_date = '';
$where = "WHERE o.status IN ('Confirmed','Delivered')"; // Always enforce valid condition

// Filter by date range if submitted
if (isset($_POST['filter'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if ($start_date && $end_date) {
        // Add AND when filtering
        $where .= " AND o.created_at BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";
    }
}

// Fetch total revenue
$total_query = $conn->query("SELECT SUM(o.total_amount) AS total_revenue FROM orders o $where");
$total_revenue = $total_query->fetch_assoc()['total_revenue'] ?? 0;

// Fetch orders for table
$orders_query = $conn->query("
    SELECT o.id, o.created_at, o.total_amount, o.status, u.name AS username
    FROM orders o
    JOIN users u ON o.user_id = u.id
    $where
    ORDER BY o.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Revenue Report - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4 text-center">Revenue Report</h2>

    <!-- Date Filter Form -->
    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>" required>
        </div>
        <div class="col-md-4">
            <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>" required>
        </div>
        <div class="col-md-4">
            <button type="submit" name="filter" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <!-- Total Revenue -->
    <div class="alert alert-success">
        <h4>Total Revenue: Rs <?= number_format($total_revenue, 2) ?></h4>
    </div>

    <!-- Orders Table -->
    <div class="table-responsive shadow rounded bg-white p-3">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Date</th>
                    <th>Total Amount (Rs)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $orders_query->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td>Rs <?= number_format($row['total_amount'], 2) ?></td>
                    <td><?= $row['status'] ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <a href="dashboard.php" class="btn btn-secondary mt-3">⬅ Back to Dashboard</a>
</div>
</body>
</html>
