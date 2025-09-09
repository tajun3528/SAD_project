<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.html");
  exit();
}

include 'db.php';
$username = $_SESSION['username'];

// Validate GET parameters
$type = $_GET['type'] ?? '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!in_array($type, ['fruit', 'medicine']) || $id <= 0) {
  echo "<script>alert('Invalid item type or ID.'); window.location.href='user_dashboard.php';</script>";
  exit();
}

// Fetch item details
if ($type === 'fruit') {
  $stmt = $conn->prepare("SELECT * FROM fruits WHERE id = ?");
} else {
  $stmt = $conn->prepare("SELECT * FROM medicines WHERE id = ?");
}
$stmt->bind_param("i", $id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) {
  echo "<script>alert('Item not found.'); window.location.href='user_dashboard.php';</script>";
  exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $quantity = intval($_POST['quantity'] ?? 0);
  $notes = trim($_POST['notes'] ?? '');

  if ($quantity <= 0) {
    echo "<script>alert('Sorry!! Product Not Available.'); window.history.back();</script>";
    exit();
  }

  $available = intval($item['quantity']);
  if ($quantity > $available) {
    echo "<script>alert('Requested quantity exceeds available stock. Only $available left.'); window.history.back();</script>";
    exit();
  }

  // Get unit price
  $unit_price = ($type === 'fruit') ? floatval($item['price_per_kg']) : floatval($item['price_per_piece']);
  $total_price = $unit_price * $quantity;

  // Insert into cart
  $stmt = $conn->prepare("INSERT INTO cart (username, item_type, item_id, quantity, notes, unit_price, total_price, added_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
  $stmt->bind_param("ssissdd", $username, $type, $id, $quantity, $notes, $unit_price, $total_price);
  $stmt->execute();

  // Update stock
  if ($type === 'fruit') {
    $updateStmt = $conn->prepare("UPDATE fruits SET quantity = quantity - ? WHERE id = ?");
  } else {
    $updateStmt = $conn->prepare("UPDATE medicines SET quantity = quantity - ? WHERE id = ?");
  }
  $updateStmt->bind_param("ii", $quantity, $id);
  $updateStmt->execute();

  echo "<script>alert('Item added to cart successfully!'); window.location.href='view_cart.php';</script>";
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Add to Cart</title>
  <style>
    body { font-family: 'Segoe UI'; background-color: #f0f8ff; padding: 40px; }
    .form-box { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    h2 { color: #0077b6; margin-bottom: 20px; }
    img { width: 100%; max-height: 250px; object-fit: cover; border-radius: 8px; margin-bottom: 20px; }
    label { display: block; margin-top: 10px; font-weight: bold; }
    input, textarea { width: 100%; padding: 8px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }
    button { margin-top: 20px; padding: 10px 20px; background-color: #0077b6; color: white; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background-color: #023e8a; }
    .price-box { margin-top: 10px; font-size: 16px; color: #333; }
  </style>
</head>
<body>
  <div class="form-box">
    <h2>Add to Cart: <?php echo htmlspecialchars($item['name']); ?></h2>
    <img src="<?php echo $item['image_path']; ?>" alt="Item Image">
    <p><strong>Available:</strong> <?php echo $item['quantity']; ?> <?php echo $type === 'fruit' ? 'kg' : 'pieces'; ?></p>
    <p><strong>Price:</strong>
      <?php
        $unit_price = ($type === 'fruit') ? $item['price_per_kg'] : $item['price_per_piece'];
        echo "৳" . number_format($unit_price, 2) . " per " . ($type === 'fruit' ? "kg" : "piece");
      ?>
    </p>

    <form method="POST">
      <label>Quantity (<?php echo $type === 'fruit' ? 'kg' : 'pieces'; ?>):</label>
      <input type="number" name="quantity" min="1" max="<?php echo $item['quantity']; ?>" required oninput="calculateTotal(this.value)">
      
      <label>Notes (optional):</label>
      <textarea name="notes" rows="3"></textarea>

      <div class="price-box" id="totalPriceBox">Total Price: ৳0.00</div>

      <button type="submit">Add to Cart</button>
    </form>
  </div>

  <script>
    const unitPrice = <?php echo $unit_price; ?>;
    function calculateTotal(qty) {
      const total = unitPrice * parseFloat(qty || 0);
      document.getElementById('totalPriceBox').innerText = 'Total Price: ৳' + total.toFixed(2);
    }
  </script>
</body>
</html>