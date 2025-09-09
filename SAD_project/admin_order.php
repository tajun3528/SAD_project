<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_name'])) {
  header("Location: admin_login.html");
  exit();
}

$admin = $_SESSION['admin_name'];
$stmt = $conn->query("SELECT * FROM orders ORDER BY ordered_at DESC");
$orders = $stmt->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Order Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }
    body {
      display: flex;
      min-height: 100vh;
      background-color: #f1f5f9;
    }
    .sidebar {
      width: 220px;
      background-color: #0f172a;
      color: white;
      padding: 20px;
    }
    .sidebar h2 {
      margin-bottom: 30px;
      font-size: 22px;
      color: #38bdf8;
    }
    .sidebar a {
      display: block;
      color: white;
      text-decoration: none;
      margin: 15px 0;
      padding: 10px;
      border-radius: 5px;
      transition: background 0.3s;
    }
    .sidebar a:hover {
      background-color: #1e293b;
    }
    .main {
      flex: 1;
      padding: 30px;
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }
    .header h1 {
      color: #0f172a;
    }
    .header button {
      padding: 10px 20px;
      background-color: #ef4444;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .order-card {
      background-color: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }
    .order-card h3 {
      margin-bottom: 10px;
      color: #2563eb;
    }
    .order-details {
      font-size: 15px;
      color: #334155;
      margin-bottom: 10px;
    }
    .status {
      font-weight: bold;
      padding: 5px 10px;
      border-radius: 5px;
      display: inline-block;
    }
    .status.pending {
      background-color: #facc15;
      color: #92400e;
    }
    .status.confirmed {
      background-color: #34d399;
      color: #065f46;
    }
    .status.cancelled {
      background-color: #f87171;
      color: #7f1d1d;
    }
    .update-form {
      margin-top: 10px;
    }
    .update-form select, .update-form button {
      padding: 6px 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      margin-right: 10px;
    }
    .update-form button {
      background-color: #10b981;
      color: white;
      border: none;
      cursor: pointer;
    }
    .update-form button:hover {
      background-color: #059669;
    }
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

    <h2 style="color:#2563eb; margin-bottom:20px;">Order Management</h2>

    <?php foreach ($orders as $order): ?>
      <div class="order-card">
        <h3>Order #<?php echo $order['id']; ?> — <?php echo ucfirst($order['item_type']); ?> ID: <?php echo $order['item_id']; ?></h3>
        <div class="order-details">
          <strong>User:</strong> <?php echo $order['username']; ?><br>
          <strong>Quantity:</strong> <?php echo $order['quantity']; ?><br>
          <strong>Total Price:</strong> ৳<?php echo number_format($order['total_price'], 2); ?><br>
          <strong>Group ID:</strong> <?php echo $order['order_group_id']; ?><br>
          <strong>Ordered At:</strong> <?php echo date("d M Y, h:i A", strtotime($order['ordered_at'])); ?><br>
          <strong>Status:</strong> <span class="status <?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
        </div>
        <form class="update-form" method="POST" action="update_order_status.php">
          <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
          <select name="status">
            <option value="pending" <?php if ($order['status'] === 'pending') echo 'selected'; ?>>Pending</option>
            <option value="confirmed" <?php if ($order['status'] === 'confirmed') echo 'selected'; ?>>Confirmed</option>
            <option value="cancelled" <?php if ($order['status'] === 'cancelled') echo 'selected'; ?>>Cancelled</option>
          </select>
          <button type="submit">Update</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
</body>
</html>