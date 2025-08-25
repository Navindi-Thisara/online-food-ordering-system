<?php
session_start();
require '../includes/db_connect.php';

// Add new menu item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_menu'])) {
    $restaurant_id = intval($_POST['restaurant_id']);
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $image = trim($_POST['image']);

    $stmt = $conn->prepare("INSERT INTO menu_items (restaurant_id, name, price, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $restaurant_id, $name, $price, $image);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_menu.php");
    exit;
}

// Edit menu item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_menu'])) {
    $id = intval($_POST['id']);
    $restaurant_id = intval($_POST['restaurant_id']);
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $image = trim($_POST['image']);

    $stmt = $conn->prepare("UPDATE menu_items SET restaurant_id=?, name=?, price=?, image=? WHERE id=?");
    $stmt->bind_param("isdsi", $restaurant_id, $name, $price, $image, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_menu.php");
    exit;
}

// Delete menu item
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM menu_items WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_menu.php");
    exit;
}

// Fetch restaurants and menu items
$restaurants = $conn->query("SELECT * FROM restaurants");
$menu = $conn->query("SELECT m.id, m.name, m.price, m.image, r.name AS restaurant_name, r.id AS restaurant_id
                      FROM menu_items m 
                      JOIN restaurants r ON m.restaurant_id=r.id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4 text-center">Manage Menu Items</h2>

    <!-- Add Menu Item Form -->
    <div class="card shadow rounded mb-4 p-4 bg-white">
        <form method="POST" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Restaurant</label>
                <select name="restaurant_id" class="form-select" required>
                    <option value="">Select Restaurant</option>
                    <?php while($r = $restaurants->fetch_assoc()): ?>
                        <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Menu Item Name</label>
                <input type="text" name="name" class="form-control" placeholder="Item Name" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Price (Rs)</label>
                <input type="number" step="0.01" name="price" class="form-control" placeholder="Price" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Image Filename</label>
                <input type="text" name="image" class="form-control" placeholder="image.jpg">
            </div>
            <div class="col-md-2">
                <button type="submit" name="add_menu" class="btn btn-success w-100">Add Menu Item</button>
            </div>
        </form>
    </div>

    <!-- Menu List Table -->
    <div class="card shadow rounded p-3 bg-white">
        <h4 class="mb-3">Menu Items</h4>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Restaurant</th>
                        <th>Item</th>
                        <th>Price (Rs)</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($menu->num_rows > 0): ?>
                        <?php while($row = $menu->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['restaurant_name']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= number_format($row['price'], 2) ?></td>
                                <td>
                                    <?php if($row['image']): ?>
                                        <img src="../assets/images/<?= htmlspecialchars($row['image']) ?>" width="50" alt="Menu Image">
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
                                    <a href="manage_menu.php?delete=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Delete this menu item?')">
                                       Delete
                                    </a>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $row['id'] ?>" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel<?= $row['id'] ?>">Edit Menu Item</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <form method="POST">
                                      <div class="modal-body">
                                          <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                          <div class="mb-3">
                                              <label class="form-label">Restaurant</label>
                                              <select name="restaurant_id" class="form-select" required>
                                                  <?php
                                                  // Re-fetch restaurants for the modal
                                                  $restaurants_modal = $conn->query("SELECT * FROM restaurants");
                                                  while($r_modal = $restaurants_modal->fetch_assoc()):
                                                  ?>
                                                      <option value="<?= $r_modal['id'] ?>" <?= $r_modal['id']==$row['restaurant_id']?"selected":"" ?>>
                                                          <?= htmlspecialchars($r_modal['name']) ?>
                                                      </option>
                                                  <?php endwhile; ?>
                                              </select>
                                          </div>
                                          <div class="mb-3">
                                              <label class="form-label">Menu Item Name</label>
                                              <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
                                          </div>
                                          <div class="mb-3">
                                              <label class="form-label">Price (Rs)</label>
                                              <input type="number" step="0.01" name="price" class="form-control" value="<?= $row['price'] ?>" required>
                                          </div>
                                          <div class="mb-3">
                                              <label class="form-label">Image Filename</label>
                                              <input type="text" name="image" class="form-control" value="<?= htmlspecialchars($row['image']) ?>">
                                          </div>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" name="edit_menu" class="btn btn-primary">Save Changes</button>
                                      </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No menu items found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
