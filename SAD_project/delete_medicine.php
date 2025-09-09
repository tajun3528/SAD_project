<?php
include 'db.php';
session_start();
if (!isset($_SESSION['admin_name'])) {
  header("Location: admin_login.html");
  exit();
}

$id = $_GET['id'];
$sql = "DELETE FROM medicines WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: manage_medicines.php");
exit();
?>
