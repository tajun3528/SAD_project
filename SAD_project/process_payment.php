<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
  header("Location: login.html");
  exit();
}

$username = $_SESSION['username'];
$method = $_POST['payment_method'] ?? '';
$txn = trim($_POST['transaction_id'] ?? '');
$amount = floatval($_POST['amount'] ?? 0);

// Validate input
if ($amount <= 0 || !$method || !$txn) {
  echo "<script>alert('Invalid payment details.'); window.history.back();</script>";
  exit();
}

// Fetch cart items
$stmt = $conn->prepare("SELECT * FROM cart WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (count($items) === 0) {
  echo "<script>alert('Your cart is empty.'); window.location.href='view_cart.php';</script>";
  exit();
}

// Generate a unique order group ID
$orderGroupId = uniqid('ORD_');

// Insert each item into orders table
foreach ($items as $item) {
  $insert = $conn->prepare("INSERT INTO orders (
    username, item_type, item_id, quantity, unit_price, total_price,
    payment_method, transaction_id, status, ordered_at, order_group_id
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'confirmed', NOW(), ?)");
  
  $insert->bind_param(
    "ssiiddsss",
    $username,
    $item['item_type'],
    $item['item_id'],
    $item['quantity'],
    $item['unit_price'],
    $item['total_price'],
    $method,
    $txn,
    $orderGroupId
  );
  
  $insert->execute();
}

// Insert a summary payment record
$summary = $conn->prepare("INSERT INTO payments (
  username, item_type, item_id, amount, payment_method, transaction_id, status, paid_at
) VALUES (?, 'cart', 0, ?, ?, ?, 'completed', NOW())");

$summary->bind_param("sdss", $username, $amount, $method, $txn);
$summary->execute();

// Clear cart
$clear = $conn->prepare("DELETE FROM cart WHERE username = ?");
$clear->bind_param("s", $username);
$clear->execute();

echo "<script>alert('Payment successful! Your order has been placed.'); window.location.href='order_status.php';</script>";
?>