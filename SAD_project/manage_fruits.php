<?php
session_start();
if (!isset($_SESSION['admin_name'])) {
  header("Location: admin_login.html");
  exit();
}
$admin = $_SESSION['admin_name'];
include 'db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search !== '') {
  $stmt = $conn->prepare("SELECT * FROM fruits WHERE name LIKE ? OR category LIKE ? ORDER BY created_at DESC");
  $searchTerm = '%' . $search . '%';
  $stmt->bind_param("ss", $searchTerm, $searchTerm);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $sql = "SELECT * FROM fruits ORDER BY created_at DESC";
  $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Fruits</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
    body { display: flex; min-height: 100vh; background-color: #f1f5f9; }
    .sidebar { width: 220px; background-color: #0f172a; color: white; padding: 20px; }
    .sidebar h2 { margin-bottom: 30px; font-size: 22px; color: #38bdf8; }
    .sidebar a { display: block; color: white; text-decoration: none; margin: 15px 0; padding: 10px; border-radius: 5px; transition: background 0.3s; }
    .sidebar a:hover { background-color: #1e293b; }
    .main { flex: 1; padding: 30px; }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .header h1 { color: #0f172a; }
    .header button { padding: 10px 20px; background-color: #ef4444; color: white; border: none; border-radius: 5px; cursor: pointer; }
    .form-section, .list-section { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 30px; }
    .form-section h2, .list-section h2 { margin-bottom: 20px; color: #2563eb; }
    form input, form textarea { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; }
    form input[type="submit"] { background-color: #2563eb; color: white; border: none; cursor: pointer; }
    form input[type="submit"]:hover { background-color: #1e40af; }
    .fruit-card { display: flex; gap: 20px; margin-bottom: 20px; background: #f9fafb; padding: 15px; border-radius: 10px; }
    .fruit-card img { width: 120px; height: 80px; object-fit: cover; border-radius: 5px; }
    .fruit-info h3 { margin: 0; color: #0f172a; }
    .fruit-info p { margin: 5px 0; color: #64748b; }
    .btn { padding: 6px 12px; text-decoration: none; border-radius: 5px; color: white; margin-right: 10px; }
    .btn-view { background-color: #0ea5e9; }
    .btn-edit { background-color: #2563eb; }
    .btn-delete { background-color: #ef4444; }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php">🏠 Home</a>
    <a href="manage_hospitals.php">🏥 Manage Hospitals</a>
    <a href="manage_medicines.php">💊 Manage Medicines</a>
    <a href="manage_fruits.php">🍎 Manage Fruits</a>
    <a href="admin_order.php">📦 Order List</a>
    <a href="#">👤 Profile</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <div class="main">
    <div class="header">
      <h1>Welcome, <?php echo htmlspecialchars($admin); ?> 👋</h1>
      <button onclick="location.href='logout.php'">Logout</button>
    </div>

    <div class="form-section">
      <h2>Add New Fruit</h2>
      <form action="add_fruits.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Fruit Name" required>
        <input type="text" name="category" placeholder="Category (e.g., Citrus)" required>
        <textarea name="recommendation" rows="4" placeholder="Recommendation" required></textarea>
        <input type="number" name="quantity" placeholder="Quantity" required>
        <input type="number" step="0.01" name="price_per_kg" placeholder="Price per Kg" required>
        <textarea name="details" rows="4" placeholder="Additional Details"></textarea>
        <input type="file" name="image" accept="image/*" required>
        <input type="submit" value="Add Fruit">
      </form>
    </div>

    <div class="form-section">
      <h2>Search Fruits</h2>
      <form method="GET" action="manage_fruits.php">
        <input type="text" name="search" placeholder="Search by name or category" value="<?php echo htmlspecialchars($search); ?>">
        <input type="submit" value="Search">
      </form>
    </div>

    <div class="list-section">
      <h2>Available Fruits</h2>
      <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <div class="fruit-card">
            <img src="<?php echo $row['image_path']; ?>" alt="Fruit Image">
            <div class="fruit-info">
              <h3><?php echo htmlspecialchars($row['name']); ?></h3>
              <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></p>
              <p><strong>Recommendation:</strong> <?php echo htmlspecialchars($row['recommendation']); ?></p>
              <p><strong>Quantity:</strong> <?php echo htmlspecialchars($row['quantity']); ?> kg</p>
              <p><strong>Price per Kg:</strong> ৳<?php echo htmlspecialchars(number_format($row['price_per_kg'], 2)); ?></p>
              <p><em>Added on: <?php echo date("d M Y", strtotime($row['created_at'])); ?></em></p>
              <div style="margin-top: 10px;">
                <a href="view_fruit.php?id=<?php echo $row['id']; ?>" class="btn btn-view">See Details</a>
                <a href="edit_fruit.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Edit</a>
                <a href="delete_fruit.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this fruit?');" class="btn btn-delete">Delete</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No fruits found<?php echo $search ? ' for "' . htmlspecialchars($search) . '"' : ''; ?>.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>