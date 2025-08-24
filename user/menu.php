<?php
session_start();
include("../includes/db_connect.php");

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.html");
    exit();
}

// Initialize cart session if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $item_id = $_POST['item_id'];
    $quantity = intval($_POST['quantity']);

    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id] += $quantity;
    } else {
        $_SESSION['cart'][$item_id] = $quantity;
    }
    $message = "Item added to cart!";
}

// Fetch menu items
$sql = "SELECT m.id, m.name, m.description, m.price, m.image, r.name AS restaurant_name
        FROM menu_items m
        JOIN restaurants r ON m.restaurant_id = r.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Menu - Food Ordering System</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .menu-item { border: 1px solid #ccc; padding: 10px; margin: 10px 0; width: 250px; display: inline-block; vertical-align: top; }
        .menu-item img { width: 200px; height: 150px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['name']; ?> (User)</h1>
    <a href="../logout.php">Logout</a> | 
    <a href="cart.php">View Cart (<?php echo count($_SESSION['cart']); ?>)</a>

    <h2>Menu Items</h2>

    <?php if(isset($message)) { echo "<p style='color:green;'>$message</p>"; } ?>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            ?>
            <div class="menu-item">
                <h3><?php echo $row['name'] . " - " . $row['restaurant_name']; ?></h3>
                <p><?php echo $row['description']; ?></p>
                <p>Price: Rs <?php echo $row['price']; ?></p>
                
                <?php 
                // Check if image exists, else show placeholder
                $imagePath = "../assets/images/" . $row['image'];
                if(!empty($row['image']) && file_exists($imagePath)) { ?>
                    <img src="<?php echo $imagePath; ?>" alt="<?php echo $row['name']; ?>">
                <?php } else { ?>
                    <img src="../assets/images/no-image.png" alt="No Image Available">
                <?php } ?>

                <form method="POST" style="margin-top:10px;">
                    <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                    <input type="number" name="quantity" value="1" min="1" required>
                    <button type="submit" name="add_to_cart">Add to Cart</button>
                </form>
            </div>
            <?php
        }
    } else {
        echo "<p>No menu items found.</p>";
    }
    $conn->close();
    ?>
</body>
</html>
