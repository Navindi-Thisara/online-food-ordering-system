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
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f9f9f9; }
        h1 { margin-bottom: 10px; }
        a { margin-right: 10px; text-decoration: none; color: #007BFF; }
        a:hover { text-decoration: underline; }

        .menu-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .menu-item { 
            border: 1px solid #ccc; 
            padding: 15px; 
            width: 220px; 
            background-color: #fff; 
            border-radius: 8px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .menu-item:hover { transform: scale(1.03); }

        .menu-item img { 
            width: 100%; 
            height: 150px; 
            object-fit: cover; 
            border-radius: 6px; 
            margin-bottom: 10px; 
        }

        .menu-item h3 { margin: 0 0 10px 0; font-size: 1.1em; }
        .menu-item p { margin: 5px 0; font-size: 0.95em; }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover { background-color: #218838; }

        input[type="number"] {
            width: 50px;
            padding: 4px;
            margin-right: 5px;
        }

        /* Responsive for mobile */
        @media (max-width: 768px) {
            .menu-grid { flex-direction: column; align-items: center; }
            .menu-item { width: 90%; }
        }
    </style>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['name']; ?> (User)</h1>
    <a href="../logout.php">Logout</a> | 
    <a href="cart.php">View Cart (<?php echo count($_SESSION['cart']); ?>)</a>

    <h2>Menu Items</h2>

    <?php if(isset($message)) { echo "<p style='color:green;'>$message</p>"; } ?>

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
                <form method="POST">
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
    </div>
</body>
</html>
