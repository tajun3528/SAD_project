<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.html");
  exit();
}

include 'db.php';
$username = $_SESSION['username'];

// Fetch orders grouped by order_group_id
$stmt = $conn->prepare("SELECT * FROM orders WHERE username = ? ORDER BY order_group_id DESC, ordered_at DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Group orders by order_group_id
$groupedOrders = [];
foreach ($orders as $order) {
  $groupedOrders[$order['order_group_id']][] = $order;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Orders</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f0f8ff;
      margin: 0;
      padding: 0;
    }
    header {
      background-color: #0077b6;
      color: white;
      padding: 20px;
      text-align: center;
    }
    .container {
      max-width: 900px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    h2 {
      color: #0077b6;
      margin-bottom: 20px;
      text-align: center;
    }
    .order-group {
      margin-bottom: 40px;
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 20px;
    }
    .order-group h3 {
      margin-top: 0;
      color: #023e8a;
    }
    .order-item {
      border-bottom: 1px solid #eee;
      padding: 10px 0;
    }
    .order-item:last-child {
      border-bottom: none;
    }
    .status {
      font-weight: bold;
      color: #10b981;
    }
    .status.pending {
      color: #f59e0b;
    }
    .status.cancelled {
      color: #ef4444;
    }
    .summary {
      margin-top: 15px;
      font-weight: bold;
      color: #0077b6;
    }
  </style>
</head>
<body>
  <header>
    <h1>📦 Your Order Status</h1>
  </header>
  <div class="container">
    <h2>Order History</h2>
    <?php if (count($groupedOrders) > 0): ?>
      <?php foreach ($groupedOrders as $groupId => $group): ?>
        <div class="order-group">
          <h3>Order ID: <?php echo $groupId; ?></h3>
          <?php $groupTotal = 0; ?>
          <?php foreach ($group as $order): ?>
            <?php $groupTotal += $order['total_price']; ?>
            <div class="order-item">
              <strong><?php echo ucfirst($order['item_type']); ?> ID: <?php echo $order['item_id']; ?></strong><br>
              Quantity: <?php echo $order['quantity']; ?><br>
              Unit Price: ৳<?php echo number_format($order['unit_price'], 2); ?><br>
              Total Price: ৳<?php echo number_format($order['total_price'], 2); ?><br>
              Payment Method: <?php echo ucfirst($order['payment_method']); ?><br>
              Transaction ID: <?php echo htmlspecialchars($order['transaction_id']); ?><br>
              Status: <span class="status <?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span><br>
              Ordered At: <?php echo date("d M Y, h:i A", strtotime($order['ordered_at'])); ?>
            </div>
          <?php endforeach; ?>
          <div class="summary">Total for this order: ৳<?php echo number_format($groupTotal, 2); ?></div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="text-align: center;">You have no orders yet.</p>
    <?php endif; ?>
  </div>
</body>
</html>