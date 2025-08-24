<?php
session_start();
include("../includes/db_connect.php");

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.html");
    exit();
}

$cart = $_SESSION['cart'] ?? [];
$message = "";

// Place order
if (isset($_POST['place_order']) && !empty($cart)) {
    $user_id = $_SESSION['user_id'];
    $total = 0;

    // Calculate total
    foreach ($cart as $item_id => $qty) {
        $stmt = $conn->prepare("SELECT price FROM menu_items WHERE id=?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $total += $row['price'] * $qty;
    }

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
    $stmt->bind_param("id", $user_id, $total);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insert order items
    foreach ($cart as $item_id => $qty) {
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
    $cart = [];
    $message = "✅ Order placed successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart - Food Ordering System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f9f9f9; }
        h1 { margin-bottom: 10px; }
        a { margin-right: 15px; text-decoration: none; color: #007BFF; }
        a:hover { text-decoration: underline; }

        table { border-collapse: collapse; width: 100%; max-width: 800px; margin-top: 20px; background-color: #fff; border-radius: 8px; overflow: hidden; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #007BFF; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }

        button {
            margin-top: 15px;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover { background-color: #218838; }

        p.message { color: green; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Your Cart</h1>
    <a href="menu.php">← Back to Menu</a> | <a href="../logout.php">Logout</a>

    <?php if (!empty($message)) echo "<p class='message'>{$message}</p>"; ?>

    <?php if (!empty($cart)) { ?>
        <form method="POST">
            <table>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price (Rs)</th>
                    <th>Subtotal (Rs)</th>
                </tr>
                <?php
                $totalAmount = 0;
                foreach ($cart as $item_id => $qty) {
                    $stmt = $conn->prepare("SELECT name, price FROM menu_items WHERE id=?");
                    $stmt->bind_param("i", $item_id);
                    $stmt->execute();
                    $item = $stmt->get_result()->fetch_assoc();
                    $subtotal = $item['price'] * $qty;
                    $totalAmount += $subtotal;
                    echo "<tr>
                            <td>{$item['name']}</td>
                            <td>{$qty}</td>
                            <td>{$item['price']}</td>
                            <td>{$subtotal}</td>
                          </tr>";
                }
                ?>
                <tr>
                    <th colspan="3">Total</th>
                    <th><?php echo $totalAmount; ?></th>
                </tr>
            </table>
            <button type="submit" name="place_order">Place Order</button>
        </form>
    <?php } else {
        echo "<p>Your cart is empty.</p>";
    } ?>
</body>
</html>
