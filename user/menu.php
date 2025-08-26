<?php
session_start();
include("../includes/db_connect.php");

// Initialize cart session if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart only if user is logged in
$message = "";
if (isset($_POST['add_to_cart']) && isset($_SESSION['user_id'])) {
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
    <link rel="stylesheet" href="../style.css">
    <style>
        body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f9f9f9; }
        .header { background-color:#28a745; color:white; padding:15px; text-align:center; position:sticky; top:0; }
        .header a { color:white; margin: 0 10px; text-decoration:none; font-weight:bold; }
        .header a:hover { text-decoration:underline; }
        .container { max-width:1200px; margin:20px auto; padding:10px; }
        .menu-grid { display:flex; flex-wrap:wrap; gap:20px; justify-content:center; }
        .menu-item {
            background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);
            padding:15px; width:250px; display:flex; flex-direction:column; align-items:center;
            transition: transform 0.2s;
        }
        .menu-item:hover { transform: scale(1.03); }
        .menu-item img { width:100%; height:160px; object-fit:cover; border-radius:6px; margin-bottom:10px; }
        .menu-item h3 { margin:5px 0; font-size:1.2em; text-align:center; }
        .menu-item p { font-size:0.95em; margin:5px 0; text-align:center; }
        .menu-item form { display:flex; gap:5px; margin-top:10px; }
        .menu-item input[type="number"] { width:50px; padding:5px; }
        .menu-item button {
            background:#28a745; color:white; border:none; padding:8px 12px; border-radius:5px;
            cursor:pointer; transition:0.3s;
        }
        .menu-item button:hover { background:#218838; }
        .message { color:green; text-align:center; margin-bottom:15px; font-weight:bold; }

        @media (max-width:768px) {
            .menu-grid { flex-direction:column; align-items:center; }
            .menu-item { width:90%; }
        }
    </style>
</head>
<body>
    <div class="header">
        <?php if(isset($_SESSION['user_id'])): ?>
            Welcome, <?php echo $_SESSION['name']; ?> |
            <a href="../logout.php">Logout</a> |
            <a href="cart.php">Cart (<?php echo count($_SESSION['cart']); ?>)</a>
        <?php else: ?>
            <a href="../login.php">Login</a> | 
            <a href="../register.php">Register</a>
        <?php endif; ?>
    </div>

    <div class="container">
        <h1>Menu Items</h1>

        <?php if($message) echo "<p class='message'>$message</p>"; ?>

        <div class="menu-grid">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $imagePath = "../assets/images/" . $row['image'];
                    $imageSrc = (!empty($row['image']) && file_exists($imagePath)) ? $imagePath : "../assets/images/no-image.png";
                    ?>
                    <div class="menu-item">
                        <img src="<?php echo $imageSrc; ?>" alt="<?php echo $row['name']; ?>">
                        <h3><?php echo $row['name']; ?></h3>
                        <p><strong>Restaurant:</strong> <?php echo $row['restaurant_name']; ?></p>
                        <p><?php echo $row['description']; ?></p>
                        <p><strong>Price:</strong> Rs <?php echo $row['price']; ?></p>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <form method="POST">
                                <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                                <input type="number" name="quantity" value="1" min="1" required>
                                <button type="submit" name="add_to_cart">Add to Cart</button>
                            </form>
                        <?php else: ?>
                            <p><a href="../login.php" style="color:#007bff;">Login to add to cart</a></p>
                        <?php endif; ?>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No menu items found.</p>";
            }
            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>
