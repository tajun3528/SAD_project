<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.html");
  exit();
}

include 'db.php';
$username = $_SESSION['username'];

// Fetch cart items
$stmt = $conn->prepare("SELECT * FROM cart WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$cartItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Your Cart</title>
  <style>
    body { font-family: 'Segoe UI'; background-color: #f0f8ff; margin: 0; padding: 0; }
    header, nav { background-color: #0077b6; color: white; padding: 15px; text-align: center; }
    nav ul { display: flex; list-style: none; padding: 0; margin: 0; background-color: #03045e; }
    nav ul li a { flex: 1; text-align: center; padding: 15px; display: block; color: white; text-decoration: none; font-weight: bold; }
    nav ul li a:hover { background-color: #0077b6; }
    .container { max-width: 900px; margin: 40px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    h2 { color: #0077b6; margin-bottom: 20px; }
    .cart-item { border-bottom: 1px solid #ccc; padding: 15px 0; }
    .cart-item:last-child { border-bottom: none; }
    .item-name { font-weight: bold; font-size: 18px; }
    .item-type { color: #555; }
    .btn-remove { background-color: #e63946; color: white; padding: 6px 12px; border: none; border-radius: 5px; cursor: pointer; }
    .btn-remove:hover { background-color: #d62828; }
    .checkout-btn { margin-top: 30px; padding: 12px 24px; background-color: #10b981; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
    .checkout-btn:hover { background-color: #059669; }
    .total-price-box { font-size: 20px; font-weight: bold; color: #0077b6; margin-top: 30px; }
    .checkout-btn {
  background-color: #10b981;
  color: white;
  padding: 12px 24px;
  border: none;
  border-radius: 5px;
  font-size: 16px;
  cursor: pointer;
}
.checkout-btn:hover {
  background-color: #059669;
}
  </style>
</head>
<body>
  <header>
    <h1>🛒 Your Cart</h1>
  </header>

  <nav>
    <ul>
      <li><a href="user_dashboard.php">Home</a></li>
      <li><a href="user_pov_hospital_list.php">Hospital</a></li>
      <li><a href="User_pov_fruits_list.php">Food</a></li>
      <li><a href="user_pov_medicine_list.php">Madicine</a></li>
      <li><a href="view_cart.php" class="active">View Cart</a></li>
      <li><a href="#">About us</a></li>
    </ul>
  </nav>

  <div class="container">
    <h2>Items in Your Cart</h2>
    <?php if (count($cartItems) > 0): ?>
      <?php $totalCartPrice = 0; ?>
      <?php foreach ($cartItems as $item): ?>
        <?php
          // Fetch item name from respective table
          if ($item['item_type'] === 'fruit') {
            $query = $conn->prepare("SELECT name FROM fruits WHERE id = ?");
          } else {
            $query = $conn->prepare("SELECT name FROM medicines WHERE id = ?");
          }
          $query->bind_param("i", $item['item_id']);
          $query->execute();
          $nameResult = $query->get_result()->fetch_assoc();
          $itemName = $nameResult['name'] ?? 'Unknown Item';

          $totalCartPrice += $item['total_price'];
        ?>
        <div class="cart-item">
          <div class="item-name"><?php echo htmlspecialchars($itemName); ?></div>
          <div class="item-type">Type: <?php echo ucfirst($item['item_type']); ?></div>
          <div>Quantity: <?php echo $item['quantity']; ?></div>
          <div>Unit Price: ৳<?php echo number_format($item['unit_price'], 2); ?></div>
          <div>Total Price: ৳<?php echo number_format($item['total_price'], 2); ?></div>
          <div>Notes: <?php echo htmlspecialchars($item['notes']); ?></div>
          <form method="POST" action="remove_from_cart.php" style="margin-top: 10px;">
            <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
            <button type="submit" class="btn-remove">Remove</button>
          </form>
        </div>
      <?php endforeach; ?>
      <div class="total-price-box">🧾 Total Cart Price: ৳<?php echo number_format($totalCartPrice, 2); ?></div>
      <button class="checkout-btn" onclick="window.location.href='make_payment.php?amount=<?php echo $totalCartPrice; ?>'">Proceed to Checkout</button>
    <?php endif; ?>
  </div>
</body>
</html>