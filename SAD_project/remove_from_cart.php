<?php
session_start();
include 'db.php';

$cart_id = intval($_POST['cart_id'] ?? 0);

if ($cart_id > 0) {
  // Step 1: Fetch cart item details
  $stmt = $conn->prepare("SELECT item_type, item_id, quantity FROM cart WHERE id = ?");
  $stmt->bind_param("i", $cart_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $cartItem = $result->fetch_assoc();

  if ($cartItem) {
    $type = $cartItem['item_type'];
    $item_id = intval($cartItem['item_id']);
    $quantity = intval($cartItem['quantity']);

    // Step 2: Restore quantity to product table
    if ($type === 'fruit') {
      $updateStmt = $conn->prepare("UPDATE fruits SET quantity = quantity + ? WHERE id = ?");
    } else {
      $updateStmt = $conn->prepare("UPDATE medicines SET quantity = quantity + ? WHERE id = ?");
    }
    $updateStmt->bind_param("ii", $quantity, $item_id);
    $updateStmt->execute();

    // Step 3: Delete cart item
    $deleteStmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
    $deleteStmt->bind_param("i", $cart_id);
    $deleteStmt->execute();
  }
}

header("Location: view_cart.php");
exit();