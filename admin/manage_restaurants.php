<?php
session_start();
require '../includes/db_connect.php';

// Add new restaurant
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_restaurant'])) {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);

    $stmt = $conn->prepare("INSERT INTO restaurants (name, address) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $address);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_restaurants.php");
    exit;
}

// Edit restaurant
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_restaurant'])) {
    $id = intval($_POST['restaurant_id']);
    $name = trim($_POST['edit_name']);
    $address = trim($_POST['edit_address']);

    $stmt = $conn->prepare("UPDATE restaurants SET name=?, address=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $address, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_restaurants.php");
    exit;
}

// Delete restaurant
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM restaurants WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_restaurants.php");
    exit;
}

// Fetch all restaurants
$result = $conn->query("SELECT * FROM restaurants");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Restaurants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4 text-center">Manage Restaurants</h2>

    <!-- Add Restaurant Form -->
    <div class="card shadow rounded mb-4 p-4 bg-white">
        <form method="POST" class="row g-3">
            <div class="col-md-5">
                <input type="text" name="name" class="form-control" placeholder="Restaurant Name" required>
            </div>
            <div class="col-md-5">
                <input type="text" name="address" class="form-control" placeholder="Address" required>
            </div>
            <div class="col-md-2">
                <button type="submit" name="add_restaurant" class="btn btn-success w-100">Add Restaurant</button>
            </div>
        </form>
    </div>

    <!-- List of Restaurants -->
    <div class="card shadow rounded p-3 bg-white">
        <h4 class="mb-3">Restaurant List</h4>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['address']) ?></td>
                                <td>
                                    <!-- Edit Button -->
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
                                    <!-- Delete Button -->
                                    <a href="manage_restaurants.php?delete=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Delete this restaurant?')">
                                       Delete
                                    </a>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $row['id'] ?>" aria-hidden="true">
                                      <div class="modal-dialog">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel<?= $row['id'] ?>">Edit Restaurant</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                          </div>
                                          <form method="POST">
                                              <div class="modal-body">
                                                  <input type="hidden" name="restaurant_id" value="<?= $row['id'] ?>">
                                                  <div class="mb-3">
                                                      <label class="form-label">Name</label>
                                                      <input type="text" name="edit_name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
                                                  </div>
                                                  <div class="mb-3">
                                                      <label class="form-label">Address</label>
                                                      <input type="text" name="edit_address" class="form-control" value="<?= htmlspecialchars($row['address']) ?>" required>
                                                  </div>
                                              </div>
                                              <div class="modal-footer">
                                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                  <button type="submit" name="edit_restaurant" class="btn btn-primary">Save Changes</button>
                                              </div>
                                          </form>
                                        </div>
                                      </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No restaurants found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
