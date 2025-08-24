<?php
session_start();
include("../includes/db_connect.php");

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders for this user
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - Food Ordering System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { margin-bottom: 10px; }
        a { margin-right: 10px; display: inline-block; margin-bottom: 10px; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; vertical-align: top; }
        th { background-color: #f4f4f4; }

        .order-item { display: flex; align-items: center; margin-bottom: 5px; }
        .order-item img { width: 50px; height: 50px; object-fit: cover; margin-right: 10px; border: 1px solid #ccc; }

        /* Responsive Styling */
        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr { display: block; width: 100%; }
            th { display: none; }
            td { border: none; border-bottom: 1px solid #ccc; padding: 10px 0; }
            td:before { 
                content: attr(data-label); 
                font-weight: bold; 
                display: block; 
                margin-bottom: 5px;
            }
            .order-item img { width: 40px; height: 40px; }
        }
    </style>
</head>
<body>
    <h1>My Orders</h1>
    <a href="menu.php">Back to Menu</a>
    <a href="../logout.php">Logout</a>

    <?php if ($orders_result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Items</th>
                <th>Total Amount (Rs)</th>
                <th>Status</th>
            </tr>

            <?php while ($order = $orders_result->fetch_assoc()): ?>
                <tr>
                    <td data-label="Order ID"><?php echo $order['id']; ?></td>
                    <td data-label="Date"><?php echo $order['created_at']; ?></td>
                    <td data-label="Items">
                        <?php
                        $order_id = $order['id'];
                        $item_sql = "SELECT oi.quantity, oi.subtotal, m.name, m.image 
                                     FROM order_items oi
                                     JOIN menu_items m ON oi.menu_item_id = m.id
                                     WHERE oi.order_id = ?";
                        $item_stmt = $conn->prepare($item_sql);
                        $item_stmt->bind_param("i", $order_id);
                        $item_stmt->execute();
                        $items_result = $item_stmt->get_result();

                        while ($item = $items_result->fetch_assoc()):
                            // Image paths
                            $imageFile = "../assets/images/" . $item['image'];
                            $imageUrl  = (is_file($imageFile) && !empty($item['image']))
                                ? $imageFile
                                : "../assets/images/no-image.png";
                        ?>
                            <div class="order-item">
                                <img src="<?php echo $imageUrl; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <span><?php echo htmlspecialchars($item['name']) . " x " . $item['quantity'] . " (Rs " . $item['subtotal'] . ")"; ?></span>
                            </div>
                        <?php endwhile; ?>
                    </td>
                    <td data-label="Total"><?php echo $order['total_amount']; ?></td>
                    <td data-label="Status"><?php echo ucfirst($order['status']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>You have no orders yet.</p>
    <?php endif; ?>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
