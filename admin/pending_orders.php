<?php
session_start();
include('../includes/db_connect.php');

// Ensure only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

$message = "";

// Update order status
if (isset($_POST['update_status'])) {
    $id = intval($_POST['order_id']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success text-center mt-2'>Order #$id updated successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger text-center mt-2'>Failed to update order. Please try again.</div>";
    }
    $stmt->close();
}

// Fetch only pending orders
$orders = $conn->query("
    SELECT o.id, o.created_at, o.total_amount, o.status, u.name AS username
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.status='Pending'
    ORDER BY o.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pending Orders - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4 text-center">Pending Orders</h2>

    <!-- Success/Error Messages -->
    <?php if ($message) echo $message; ?>

    <div class="table-responsive shadow rounded bg-white p-3">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Date</th>
                    <th>Total Amount (Rs)</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($orders->num_rows > 0): ?>
                <?php while ($row = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= date("d M Y, h:i A", strtotime($row['created_at'])) ?></td>
                        <td>Rs <?= number_format($row['total_amount'], 2) ?></td>
                        <td>
                            <span class="badge bg-warning"><?= $row['status'] ?></span>
                        </td>
                        <td>
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="Pending" selected>Pending</option>
                                    <option value="Confirmed">Confirmed</option>
                                    <option value="Delivered">Delivered</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">No pending orders found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
