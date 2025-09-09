<?php
include 'db.php';
session_start();
if (!isset($_SESSION['admin_name'])) {
  header("Location: admin_login.html");
  exit();
}

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);

  $getImage = $conn->prepare("SELECT image_path FROM hospitals WHERE id = ?");
  $getImage->bind_param("i", $id);
  $getImage->execute();
  $result = $getImage->get_result();
  if ($row = $result->fetch_assoc()) {
    if (file_exists($row['image_path'])) {
      unlink($row['image_path']);
    }
  }

  $delete = $conn->prepare("DELETE FROM hospitals WHERE id = ?");
  $delete->bind_param("i", $id);
  $delete->execute();

  header("Location: manage_hospitals.php");
  exit();
}
?>