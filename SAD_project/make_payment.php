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
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Make Payment</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f0f8ff;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 800px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #0077b6;
      margin-bottom: 30px;
    }
    .cart-item {
      border-bottom: 1px solid #ccc;
      padding: 15px 0;
    }
    .cart-item:last-child {
      border-bottom: none;
    }
    .cart-item strong {
      font-size: 16px;
      color: #023e8a;
    }
    form {
      margin-top: 30px;
    }
    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
      color: #333;
    }
    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 16px;
    }
    .total-box {
      margin-top: 20px;
      font-size: 18px;
      font-weight: bold;
      color: #0077b6;
    }
    button {
      margin-top: 25px;
      width: 100%;
      padding: 12px;
      background-color: #0077b6;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover {
      background-color: #023e8a;
    }
    nav ul {
  display: flex;
  list-style: none;
  margin: 0;
  padding: 0;
  background-color: #03045e;
}
nav ul li {
  flex: 1;
}
nav ul li a {
  display: block;
  text-align: center;
  padding: 15px;
  color: white;
  text-decoration: none;
  font-weight: bold;
  transition: background 0.3s;
}
nav ul li a:hover {
  background-color: #0077b6;
}
  </style>
</head>
<body>
  <nav>
  <ul>
    <li><a href="user_dashboard.php">Home</a></li>
    <li><a href="user_pov_hospital_list.php">Hospital</a></li>
    <li><a href="User_pov_fruits_list.php">Food</a></li>
    <li><a href="user_pov_medicine_list.php">Medicine</a></li>
    <li><a href="view_cart.php">View Cart</a></li>
    <li><a href="#">About Us</a></li>
  </ul>
</nav>
  <div class="container">
    <h2>Make Payment</h2>

    <?php if (count($cartItems) > 0): ?>
      <?php $total = 0; ?>
      <?php foreach ($cartItems as $item): ?>
        <?php $total += $item['total_price']; ?>
        <div class="cart-item">
          <strong><?php echo ucfirst($item['item_type']); ?> ID: <?php echo $item['item_id']; ?></strong><br>
          Quantity: <?php echo $item['quantity']; ?><br>
          Unit Price: ৳<?php echo number_format($item['unit_price'], 2); ?><br>
          Total: ৳<?php echo number_format($item['total_price'], 2); ?>
        </div>
      <?php endforeach; ?>

      <div class="total-box">Total Amount: ৳<?php echo number_format($total, 2); ?></div>

      <form action="process_payment.php" method="POST">
        <input type="hidden" name="amount" value="<?php echo $total; ?>">

        <label>Payment Method</label>
        <select name="payment_method" required>
          <option value="">-- Select --</option>
          <option value="bkash">Bkash</option>
          <option value="nagad">Nagad</option>
          <option value="card">Card</option>
          <option value="cash">Cash</option>
        </select>

        <label>Transaction ID</label>
        <input type="text" name="transaction_id" placeholder="Enter transaction/reference ID" required>

        <button type="submit">Confirm Payment</button>
      </form>
    <?php else: ?>
      <p>Your cart is empty. Please add items before proceeding to payment.</p>
    <?php endif; ?>
  </div>
</body>
</html>