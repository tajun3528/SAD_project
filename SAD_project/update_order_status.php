<?php
session_start();
include 'db.php';

$order_id = intval($_POST['order_id'] ?? 0);
$status = $_POST['status'] ?? '';

if ($order_id > 0 && in_array($status, ['pending', 'confirmed', 'cancelled'])) {
  $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
  $stmt->bind_param("si", $status, $order_id);
  $stmt->execute();
}

header("Location: admin_order.php");
exit();