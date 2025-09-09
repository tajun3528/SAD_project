<?php
include 'db.php';
session_start();
if (!isset($_SESSION['admin_name'])) {
  header("Location: admin_login.html");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['hospital_name'];
  $location = $_POST['location'];
  $recommendation = $_POST['recommendation'];
  $details = $_POST['details'];
  $created_at = date('Y-m-d H:i:s');

  $imageName = $_FILES['hospital_image']['name'];
  $imageTmp = $_FILES['hospital_image']['tmp_name'];
  $imagePath = 'uploads/' . basename($imageName);

  if (!is_dir('uploads')) {
    mkdir('uploads', 0755, true);
  }

  if (move_uploaded_file($imageTmp, $imagePath)) {
    $sql = "INSERT INTO hospitals (name, location, recommendation, image_path, details, created_at)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $name, $location, $recommendation, $imagePath, $details, $created_at);

    if ($stmt->execute()) {
      header("Location: manage_hospitals.php");
      exit();
    } else {
      echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
  } else {
    echo "❌ Failed to upload image.";
  }

  $conn->close();
}
?>