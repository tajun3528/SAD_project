<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name     = trim($_POST['name']);
  $email    = trim($_POST['email']);
  $password = trim($_POST['password']);

  if (empty($name) || empty($email) || empty($password)) {
    die("All fields are required.");
  }

  

  // Insert into database
  $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $password);

  if ($stmt->execute()) {
    header("Location: login.html");
    exit();
  } else {
    echo "Error: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
}
?>