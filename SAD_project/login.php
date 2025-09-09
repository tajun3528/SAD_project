<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Use prepared statement to prevent SQL injection
  $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ? AND password = ?");
  $stmt->bind_param("ss", $email, $password);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $name);
    $stmt->fetch();

    // ✅ Set session variable
    $_SESSION['username'] = $name;

    // Redirect to dashboard
    header("Location: user_dashboard.php");
    exit();
  } else {
    echo "Login failed";
  }

  $stmt->close();
  $conn->close();
}
?>