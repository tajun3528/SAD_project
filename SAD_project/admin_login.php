<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Prepared statement to prevent SQL injection
  $stmt = $conn->prepare("SELECT id, name FROM admins WHERE email = ? AND password = ?");
  $stmt->bind_param("ss", $email, $password);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $name);
    $stmt->fetch();

    $_SESSION['admin_name'] = $name;
    $_SESSION['admin_id'] = $id;

    header("Location: admin_dashboard.php");
    exit();
  } else {
    echo "<p class='error'>Invalid credentials</p>";
  }

  $stmt->close();
  $conn->close();
}
?>