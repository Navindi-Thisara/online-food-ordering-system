<?php
session_start();
include("../includes/db_connect.php");

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.html");
    exit();
}

$cart = $_SESSION['cart'] ?? [];

if (isset($_POST['place_order']) && !empty($cart)) {
    $user_id = $_SESSION['user_id'];
    $total = 0;

    // Calculate total
    foreach($cart as $item_id => $qty) {
        $stmt = $conn->prepare("SELECT price FROM menu_items WHERE id=?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total += $row['price'] * $qty;
    }

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
    $stmt->bind_param("id", $user_id, $total);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insert order items
    foreach($cart as $item_id => $qty) {
        $stmt = $conn->prepare("SELECT price FROM menu_items WHERE id=?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $price = $stmt->get_result()->fetch_assoc()['price'];
        $subtotal = $price * $qty;

        $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("iiid", $order_id, $item_id, $qty, $subtotal);
        $stmt2->execute();
    }

    // Clear cart
    $_SESSION['cart'] = [];
    $message = "Order placed successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart - Food Ordering System</title>
</head>
<body>
    <h1>Your Cart</h1>
    <a href="menu.php">Back to Menu</a> | <a href="../logout.php">Logout</a>

    <?php if(!empty($message)) { echo "<p style='color:green;'>$message</p>"; } ?>

    <?php if(!empty($cart)) { ?>
        <form method="POST">
            <table border="1" cellpadding="5">
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
                <?php
                $totalAmount = 0;
                foreach($cart as $item_id => $qty) {
                    $stmt = $conn->prepare("SELECT name, price FROM menu_items WHERE id=?");
                    $stmt->bind_param("i", $item_id);
                    $stmt->execute();
                    $item = $stmt->get_result()->fetch_assoc();
                    $subtotal = $item['price'] * $qty;
                    $totalAmount += $subtotal;
                    echo "<tr>
                            <td>{$item['name']}</td>
                            <td>{$qty}</td>
                            <td>Rs {$item['price']}</td>
                            <td>Rs {$subtotal}</td>
                          </tr>";
                }
                ?>
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td><strong>Rs <?php echo $totalAmount; ?></strong></td>
                </tr>
            </table>
            <button type="submit" name="place_order">Place Order</button>
        </form>
    <?php } else {
        echo "<p>Your cart is empty.</p>";
    } ?>
</body>
</html>
